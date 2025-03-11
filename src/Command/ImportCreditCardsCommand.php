<?php

namespace App\Command;

use App\Entity\ApiLog;
use App\Entity\Bank;
use App\Entity\CardType;
use App\Entity\CreditCard;
use App\Entity\CreditCardFeature;
use App\Entity\CreditCardImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(name: 'app:import-creditcards', description: 'Imports or updates credit cards from API')]
class ImportCreditCardsCommand extends Command
{
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $params;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $apiUrl = $this->params->get('api_url');
        
        $output->writeln('<info>Fetching credit card data from API...</info>');
        
        try {
            $response = $this->httpClient->request('GET', $apiUrl);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();
        } catch (\Exception $e) {
            $output->writeln('<error>Failed to fetch API: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        // Log API request & response
        $apiLog = new ApiLog();
        $apiLog->setRequestUrl($apiUrl);
        $apiLog->setResponse('Status Code: ' . $statusCode . ' - Response Content: ' . $content);
        $this->entityManager->persist($apiLog);
        $this->entityManager->flush();
        
        if ($statusCode !== 200) {
            $output->writeln('<error>API returned status code ' . $statusCode . '</error>');
            return Command::FAILURE;
        }

        // Parse XML response
        $xml = simplexml_load_string($content);
        if (!$xml) {
            $output->writeln('<error>Invalid XML response.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Processing credit card data...</info>');

        $this->entityManager->beginTransaction();

        try {
            foreach ($xml->product as $product) {
                $this->processProduct($product);
            }

            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $output->writeln('<error>Failed to process data: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Import completed successfully.</info>');
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
            $creditCard->setName((string) $product->produkt);
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
            $this->setFeatureProperties($feature, $product);
            $this->entityManager->persist($feature);
        } else {
            $this->setFeatureProperties($feature, $product);
        }
        
        $this->entityManager->flush();
    }

    private function setFeatureProperties(CreditCardFeature $feature, $product): void
    {
        $feature->setLink((string) $product->link);
        $feature->setTestSeal((string) $product->testsiegel);
        $feature->setTestSealUrl((string) $product->testsiegel_url);
        $feature->setNotes((string) $product->anmerkungen);
        $feature->setRating((float) $product->bewertung);
        $feature->setHasEvaluation((bool) $product->bewertung_anzahl);
        $feature->setIncentive((float) $product->incentive);
        $feature->setAnnualFees((float) $product->gebuehren);
        $feature->setAnnualTransactionCosts((float) $product->kosten);
        $feature->setHasBonusProgram((bool) $product->bonusprogram);
        $feature->setHasAdditionalInsurance((bool) $product->insurances);
        $feature->setHasDiscountBenefits((bool) $product->benefits);
        $feature->setHasAdditionalServices((bool) $product->services);
        $feature->setSpecialFeatures((string) $product->besonderheiten);
        $feature->setParticipationFee((float) $product->gebuehrenmitaktion);
        $feature->setParticipationCost((float) $product->kostenmitaktion);
        $feature->setFirstYearFee((float) $product->gebuehrenjahr1);
        $feature->setSecondYearFee((float) $product->dauerhaft);
        $feature->setGcDomesticAtmFee((float) $product->gc_atmfree_domestic);
        $feature->setGcInternationalAtmFee((float) $product->gc_atmfree_international);
        $feature->setCcDomesticAtmFee((float) $product->cc_atmfree_domestic);
        $feature->setCcInternationalAtmFee((float) $product->cc_atmfree_international);
        $feature->setIncentiveAmount((float) $product->incentive_amount);
        $feature->setInterestRate((float) $product->habenzins);
        $feature->setShallInterestRate((float) $product->sollzins);
        $feature->setCcEuroAtmFee((float) $product->cc_atmfree_euro);
        $feature->setKkOffer((bool) $product->kkoffer);
    }
}
