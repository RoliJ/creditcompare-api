<?php

namespace App\Command;

use App\Entity\ApiLog;
use App\Entity\Bank;
use App\Entity\CardType;
use App\Entity\CreditCard;
use App\Entity\CreditCardFeature;
use App\Entity\CreditCardImage;
use App\Service\CreditCardSorter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraints\Length;

#[AsCommand(name: 'app:import-creditcards', description: 'Imports or updates credit cards from API')]
class ImportCreditCardsCommand extends Command
{
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $params;
    private LoggerInterface $logger;
    private CreditCardSorter $creditCardSorter;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager, ParameterBagInterface $params, LoggerInterface $logger, CreditCardSorter $creditCardSorter)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->params = $params;
        $this->logger = $logger;
        $this->creditCardSorter = $creditCardSorter;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $apiUrl = $this->params->get('api_url');
        
        $this->logger->info('Fetching credit card data from API...');
        
        try {
            $response = $this->httpClient->request('GET', $apiUrl);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch API: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Log API request & response
        $apiLog = new ApiLog();
        $apiLog->setRequestUrl($apiUrl);
        $apiLog->setResponse('Status Code: ' . $statusCode . ' - Response Content: ' . $content);
        $this->entityManager->persist($apiLog);
        $this->entityManager->flush();
        
        if ($statusCode !== 200) {
            $this->logger->error('API returned status code ' . $statusCode);
            return Command::FAILURE;
        }

        // Parse XML response
        $xml = simplexml_load_string($content);
        if (!$xml) {
            $this->logger->error('Invalid XML response.');
            return Command::FAILURE;
        }

        $this->logger->info('Processing credit card data...');

        $this->entityManager->beginTransaction();

        try {
            foreach ($xml->product as $product) {
                $this->processProduct($product);
            }

            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Failed to process data: ' . $e->getMessage());
            $this->logger->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }

        $this->logger->info('Import completed successfully. Triggering sorting...');

        // Ensure all changes are flushed before starting the new command
        $this->entityManager->flush();

        try {
            $this->creditCardSorter->sortAndCacheCreditCards();
        } catch (\Exception $e) {
            $this->logger->error('Failed to sort credit cards: ' . $e->getMessage());
            $this->logger->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function processProduct($product): void
    {
        // Check if bank exists, otherwise create it
        $bank = $this->entityManager->getRepository(Bank::class)->findOneBy(['bankId' => (string) $product->bankid]);
        
        if (!$bank) {
            $bank = new Bank();
            $bank->setBankId((int) $product->bankid);
            $bank->setName((string) $product->bank);
            $this->entityManager->persist($bank);
        } else {
            $bank->setName((string) $product->bank);
        }

        $this->entityManager->flush();

        // Check if card exists
        $cardType = $this->entityManager->getRepository(CardType::class)->findOneBy(['name' => (string) $product->cardtype_text]);
        $creditCard = $this->entityManager->getRepository(CreditCard::class)->findOneBy(['productId' => (int) $product->productid]);

        $hasAdminEdition = false;
        if ($creditCard) {
            $hasAdminEdition = $creditCard->getHasAdminEdition();
        }
        
        if (!$creditCard) {
            $creditCard = new CreditCard();
            $creditCard->setCardType($cardType);
            $creditCard->setBank($bank);
            $creditCard->setProductId((int) $product->productid);
            $creditCard->setName((string) $product->produkt);
            $this->entityManager->persist($creditCard);
        } else {
            $creditCard->setCardType($cardType);
            $creditCard->setBank($bank);
            !$hasAdminEdition ? $creditCard->setName((string) $product->produkt) : $creditCard->setOriginalValue('credit_cards', ['name' => (string) $product->produkt]);
        }
        
        $this->entityManager->flush();

        // Ensure the directory exists
        $imageDir = 'public/images/cards/';
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0775, true);
        }

        // Check if image already exists and if the URL is different
        if ((string) $product->logo) {
            $existingImage = $this->entityManager->getRepository(CreditCardImage::class)->findOneBy(['creditCard' => $creditCard]);
            $imageUrl = (string) $product->logo;
            $imagePath = $imageDir . (int) $product->productid . '.jpg';

            if ($existingImage) {
                if ($existingImage->getImageUrl() !== $imageUrl) {
                    // Update the image if the URL is different
                    file_put_contents($imagePath, file_get_contents($imageUrl));

                    $existingImage->setImageUrl($imageUrl);
                    $existingImage->setLocalPath($imagePath);
                    $this->entityManager->flush();
                }
            } else {
                // Download & Save Image
                file_put_contents($imagePath, file_get_contents($imageUrl));

                $creditCardImage = new CreditCardImage();
                $creditCardImage->setCreditCard($creditCard);
                $creditCardImage->setImageUrl($imageUrl);
                $creditCardImage->setLocalPath($imagePath);
                $this->entityManager->persist($creditCardImage);
                $this->entityManager->flush();
            }
        }

        // Process Features
        $feature = $this->entityManager->getRepository(CreditCardFeature::class)->findOneBy(['creditCard' => $creditCard]);
        if (!$feature) {
            $feature = new CreditCardFeature();
            $feature->setCreditCard($creditCard);
            $this->setFeatureProperties($feature, $product, $hasAdminEdition);
            $this->entityManager->persist($feature);
        } else {
            $this->setFeatureProperties($feature, $product, $hasAdminEdition);
        }
        
        $this->entityManager->flush();
    }

    private function setFeatureProperties(CreditCardFeature $feature, $product, bool $hasAdminEdition): void
    {
        !$hasAdminEdition ? $feature->setLink((string) $product->link) : $feature->setOriginalValue('credit_card_features', ['link' => (string) $product->link]);
        !$hasAdminEdition ? $feature->setTestSeal((string) $product->testsiegel) : $feature->setOriginalValue('credit_card_features', ['test_seal' => (string) $product->testsiegel]);
        !$hasAdminEdition ? $feature->setTestSealUrl((string) $product->testsiegel_url) : $feature->setOriginalValue('credit_card_features', ['test_seal_url' => (string) $product->testsiegel_url]);
        !$hasAdminEdition ? $feature->setNotes((string) $product->anmerkungen) : $feature->setOriginalValue('credit_card_features', ['notes' => (string) $product->anmerkungen]);
        !$hasAdminEdition ? $feature->setRating((float) $product->bewertung) : $feature->setOriginalValue('credit_card_features', ['rating' => (float) $product->bewertung]);
        !$hasAdminEdition ? $feature->setHasEvaluation((bool) $product->bewertung_anzahl) : $feature->setOriginalValue('credit_card_features', ['has_evaluation' => (bool) $product->bewertung_anzahl]);
        !$hasAdminEdition ? $feature->setIncentive((float) $product->incentive) : $feature->setOriginalValue('credit_card_features', ['incentive' => (float) $product->incentive]);
        !$hasAdminEdition ? $feature->setAnnualFees((float) $product->gebuehren) : $feature->setOriginalValue('credit_card_features', ['annual_fees' => (float) $product->gebuehren]);
        !$hasAdminEdition ? $feature->setAnnualTransactionCosts((float) $product->kosten) : $feature->setOriginalValue('credit_card_features', ['annual_transaction_costs' => (float) $product->kosten]);
        !$hasAdminEdition ? $feature->setHasBonusProgram((bool) $product->bonusprogram) : $feature->setOriginalValue('credit_card_features', ['has_bonus_program' => (bool) $product->bonusprogram]);
        !$hasAdminEdition ? $feature->setHasAdditionalInsurance((bool) $product->insurances) : $feature->setOriginalValue('credit_card_features', ['has_additional_insurance' => (bool) $product->insurances]);
        !$hasAdminEdition ? $feature->setHasDiscountBenefits((bool) $product->benefits) : $feature->setOriginalValue('credit_card_features', ['has_discount_benefits' => (bool) $product->benefits]);
        !$hasAdminEdition ? $feature->setHasAdditionalServices((bool) $product->services) : $feature->setOriginalValue('credit_card_features', ['has_additional_services' => (bool) $product->services]);
        !$hasAdminEdition ? $feature->setSpecialFeatures((string) $product->besonderheiten) : $feature->setOriginalValue('credit_card_features', ['special_features' => (string) $product->besonderheiten]);
        !$hasAdminEdition ? $feature->setParticipationFee((float) $product->gebuehrenmitaktion) : $feature->setOriginalValue('credit_card_features', ['participation_fee' => (float) $product->gebuehrenmitaktion]);
        !$hasAdminEdition ? $feature->setParticipationCost((float) $product->kostenmitaktion) : $feature->setOriginalValue('credit_card_features', ['participation_cost' => (float) $product->kostenmitaktion]);
        !$hasAdminEdition ? $feature->setFirstYearFee((float) $product->gebuehrenjahr1) : $feature->setOriginalValue('credit_card_features', ['first_year_fee' => (float) $product->gebuehrenjahr1]);
        !$hasAdminEdition ? $feature->setSecondYearFee((float) $product->dauerhaft) : $feature->setOriginalValue('credit_card_features', ['second_year_fee' => (float) $product->dauerhaft]);
        !$hasAdminEdition ? $feature->setGcDomesticAtmFee((float) $product->gc_atmfree_domestic) : $feature->setOriginalValue('credit_card_features', ['gc_domestic_atm_fee' => (float) $product->gc_atmfree_domestic]);
        !$hasAdminEdition ? $feature->setGcInternationalAtmFee((float) $product->gc_atmfree_international) : $feature->setOriginalValue('credit_card_features', ['gc_international_atm_fee' => (float) $product->gc_atmfree_international]);
        !$hasAdminEdition ? $feature->setCcDomesticAtmFee((float) $product->cc_atmfree_domestic) : $feature->setOriginalValue('credit_card_features', ['cc_domestic_atm_fee' => (float) $product->cc_atmfree_domestic]);
        !$hasAdminEdition ? $feature->setCcInternationalAtmFee((float) $product->cc_atmfree_international) : $feature->setOriginalValue('credit_card_features', ['cc_international_atm_fee' => (float) $product->cc_atmfree_international]);
        !$hasAdminEdition ? $feature->setIncentiveAmount((float) $product->incentive_amount) : $feature->setOriginalValue('credit_card_features', ['incentive_amount' => (float) $product->incentive_amount]);
        !$hasAdminEdition ? $feature->setInterestRate((float) $product->habenzins) : $feature->setOriginalValue('credit_card_features', ['interest_rate' => (float) $product->habenzins]);
        !$hasAdminEdition ? $feature->setShallInterestRate((float) $product->sollzins) : $feature->setOriginalValue('credit_card_features', ['shall_interest_rate' => (float) $product->sollzins]);
        !$hasAdminEdition ? $feature->setCcEuroAtmFee((float) $product->cc_atmfree_euro) : $feature->setOriginalValue('credit_card_features', ['cc_euro_atm_fee' => (float) $product->cc_atmfree_euro]);
        !$hasAdminEdition ? $feature->setKkOffer((bool) $product->kkoffer) : $feature->setOriginalValue('credit_card_features', ['kk_offer' => (bool) $product->kkoffer]);
    }
}
