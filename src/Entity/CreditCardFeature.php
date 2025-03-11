<?php

namespace App\Entity;

use App\Repository\CreditCardFeatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CreditCardFeatureRepository::class)]
#[ORM\Table(name: 'credit_card_features')]
#[ORM\Index(columns: ['credit_card_id'], name: 'idx_feature_credit_card')]
class CreditCardFeature
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: CreditCard::class, inversedBy: "features")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private CreditCard $creditCard;

    #[ORM\Column(type: "string", length: 255, nullable: false, options: ["comment" => "link: Deeplink"])]
    private string $link;

    #[ORM\Column(type: "string", length: 255, nullable: true, options: ["comment" => "testsiegel: URL to the test seal in a standard format"])]
    private ?string $testSeal = null;

    #[ORM\Column(type: "string", length: 255, nullable: true, options: ["comment" => "testsiegel_url: URL to the test seal in a standard format"])]
    private ?string $testSealUrl = null;

    #[ORM\Column(type: "text", nullable: false, options: ["comment" => "anmerkungen: Extra information"])]
    private string $notes;

    #[ORM\Column(type: "decimal", precision: 2, scale: 1, nullable: true, options: ["comment" => "bewertung: Star rating from the provider (1-5)"])]
    private ?float $rating = null;

    #[ORM\Column(type: "boolean", nullable: true, options: ["default" => 0, "comment" => "bewertung_anzahl: Evaluation number; 0 = No, 1 = Yes"])]
    private bool $hasEvaluation;

    #[ORM\Column(type: "float", nullable: false, options: ["comment" => "incentive: T.A.E."])]
    private float $incentive;

    #[ORM\Column(type: "float", nullable: false, options: ["comment" => "gebuehren: Annual charges of the credit card in Euro (card fees)"])]
    private float $annualFees;

    #[ORM\Column(type: "float", nullable: false, options: ["comment" => "kosten: Annual transaction costs of the credit card in Euro"])]
    private float $annualTransactionCosts;

    #[ORM\Column(type: "boolean", nullable: true, options: ["default" => 0, "comment" => "bonusprogram: If the credit card offers a bonus program; 0 = No, 1 = Yes"])]
    private bool $hasBonusProgram;

    #[ORM\Column(type: "boolean", nullable: true, options: ["default" => 0, "comment" => "insurances: Offers additional insurance cover; 0 = No, 1 = Yes"])]
    private bool $hasAdditionalInsurance;

    #[ORM\Column(type: "boolean", nullable: true, options: ["default" => 0, "comment" => "benefits: Offers discount benefits on selected Partners; 0 = No, 1 = Yes"])]
    private bool $hasDiscountBenefits;

    #[ORM\Column(type: "boolean", nullable: true, options: ["default" => 0, "comment" => "services: Offers additional services?; 0 = No, 1 = Yes"])]
    private bool $hasAdditionalServices;

    #[ORM\Column(type: "string", length: 255, nullable: true, options: ["comment" => "besonderheiten: Text field for special features of the credit card"])]
    private ?string $specialFeatures = null;

    #[ORM\Column(type: "string", length: 255, nullable: true, options: ["comment" => "gebuehrenmitaktion: Participation fee"])]
    private float $participationFee;

    #[ORM\Column(type: "string", length: 255, nullable: true, options: ["comment" => "kostenmitaktion: Participation cost"])]
    private float $participationCost;

    #[ORM\Column(type: "float", nullable: false, options: ["comment" => "gebuehrenjahr1: 1st year fee"])]
    private float $firstYearFee;

    #[ORM\Column(type: "float", nullable: false, options: ["comment" => "dauerhaft: Fee from 2nd year"])]
    private float $secondYearFee;

    #[ORM\Column(type: "float", nullable: true, options: ["comment" => "gc_atmfree_domestic: National ATM fee"])]
    private float $gcDomesticAtmFee;

    #[ORM\Column(type: "float", nullable: true, options: ["comment" => "gc_atmfree_international: International ATM fee"])]
    private float $gcInternationalAtmFee;

    #[ORM\Column(type: "float", nullable: true, options: ["comment" => "cc_atmfree_domestic: Offers a free fee on national ATM?"])]
    private float $ccDomesticAtmFee;

    #[ORM\Column(type: "float", nullable: true, options: ["comment" => "cc_atmfree_international: Offers a free fee on international ATM?"])]
    private float $ccInternationalAtmFee;

    #[ORM\Column(type: "float", nullable: false, options: ["comment" => "incentive_amount: T.A.E."])]
    private float $incentiveAmount;
    
    #[ORM\Column(type: "float", nullable: true, options: ["comment" => "habenzins: Interest rate"])]
    private float $interestRate;

    #[ORM\Column(type: "float", nullable: true, options: ["comment" => "sollzins: Shall interest rate"])]
    private float $shallInterestRate;

    #[ORM\Column(type: "float", nullable: true, options: ["comment" => "cc_atmfree_euro: Offers a free fee on EU ATM?"])]
    private float $ccEuroAtmFee;

    #[ORM\Column(type: "boolean", nullable: true, options: ["default" => 0, "comment" => "kkoffer; 0 = No, 1 = Yes"])]
    private bool $kkOffer;

    // Getters and Setters...

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreditCard(): CreditCard
    {
        return $this->creditCard;
    }

    public function setCreditCard(CreditCard $creditCard): self
    {
        $this->creditCard = $creditCard;
        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function getTestSeal(): ?string
    {
        return $this->testSeal;
    }

    public function setTestSeal(?string $testSeal): self
    {
        $this->testSeal = $testSeal;
        return $this;
    }

    public function getTestSealUrl(): ?string
    {
        return $this->testSealUrl;
    }

    public function setTestSealUrl(?string $testSealUrl): self
    {
        $this->testSealUrl = $testSealUrl;
        return $this;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function getHasEvaluation(): bool
    {
        return $this->hasEvaluation;
    }

    public function setHasEvaluation(bool $hasEvaluation): self
    {
        $this->hasEvaluation = $hasEvaluation;
        return $this;
    }

    public function getIncentive(): float
    {
        return $this->incentive;
    }

    public function setIncentive(float $incentive): self
    {
        $this->incentive = $incentive;
        return $this;
    }

    public function getAnnualFees(): float
    {
        return $this->annualFees;
    }

    public function setAnnualFees(float $annualFees): self
    {
        $this->annualFees = $annualFees;
        return $this;
    }

    public function getAnnualTransactionCosts(): float
    {
        return $this->annualTransactionCosts;
    }

    public function setAnnualTransactionCosts(float $annualTransactionCosts): self
    {
        $this->annualTransactionCosts = $annualTransactionCosts;
        return $this;
    }

    public function getHasBonusProgram(): bool
    {
        return $this->hasBonusProgram;
    }

    public function setHasBonusProgram(bool $hasBonusProgram): self
    {
        $this->hasBonusProgram = $hasBonusProgram;
        return $this;
    }

    public function getHasAdditionalInsurance(): bool
    {
        return $this->hasAdditionalInsurance;
    }

    public function setHasAdditionalInsurance(bool $hasAdditionalInsurance): self
    {
        $this->hasAdditionalInsurance = $hasAdditionalInsurance;
        return $this;
    }

    public function getHasDiscountBenefits(): bool
    {
        return $this->hasDiscountBenefits;
    }

    public function setHasDiscountBenefits(bool $hasDiscountBenefits): self
    {
        $this->hasDiscountBenefits = $hasDiscountBenefits;
        return $this;
    }

    public function getHasAdditionalServices(): bool
    {
        return $this->hasAdditionalServices;
    }

    public function setHasAdditionalServices(bool $hasAdditionalServices): self
    {
        $this->hasAdditionalServices = $hasAdditionalServices;
        return $this;
    }

    public function getSpecialFeatures(): ?string
    {
        return $this->specialFeatures;
    }

    public function setSpecialFeatures(?string $specialFeatures): self
    {
        $this->specialFeatures = $specialFeatures;
        return $this;
    }

    public function getParticipationFee(): ?float
    {
        return $this->participationFee;
    }

    public function setParticipationFee(?float $participationFee): self
    {
        $this->participationFee = $participationFee;
        return $this;
    }

    public function getParticipationCost(): ?float
    {
        return $this->participationCost;
    }

    public function setParticipationCost(?float $participationCost): self
    {
        $this->participationCost = $participationCost;
        return $this;
    }

    public function getFirstYearFee(): float
    {
        return $this->firstYearFee;
    }

    public function setFirstYearFee(float $firstYearFee): self
    {
        $this->firstYearFee = $firstYearFee;
        return $this;
    }

    public function getSecondYearFee(): float
    {
        return $this->secondYearFee;
    }

    public function setSecondYearFee(float $secondYearFee): self
    {
        $this->secondYearFee = $secondYearFee;
        return $this;
    }

    public function getGcDomesticAtmFee(): ?float
    {
        return $this->gcDomesticAtmFee;
    }

    public function setGcDomesticAtmFee(?float $gcDomesticAtmFee): self
    {
        $this->gcDomesticAtmFee = $gcDomesticAtmFee;
        return $this;
    }

    public function getGcInternationalAtmFee(): ?float
    {
        return $this->gcInternationalAtmFee;
    }

    public function setGcInternationalAtmFee(?float $gcInternationalAtmFee): self
    {
        $this->gcInternationalAtmFee = $gcInternationalAtmFee;
        return $this;
    }

    public function getCcDomesticAtmFee(): ?float
    {
        return $this->ccDomesticAtmFee;
    }

    public function setCcDomesticAtmFee(?float $ccDomesticAtmFee): self
    {
        $this->ccDomesticAtmFee = $ccDomesticAtmFee;
        return $this;
    }

    public function getCcInternationalAtmFee(): ?float
    {
        return $this->ccInternationalAtmFee;
    }

    public function setCcInternationalAtmFee(?float $ccInternationalAtmFee): self
    {
        $this->ccInternationalAtmFee = $ccInternationalAtmFee;
        return $this;
    }

    public function getIncentiveAmount(): float
    {
        return $this->incentiveAmount;
    }

    public function setIncentiveAmount(float $incentiveAmount): self
    {
        $this->incentiveAmount = $incentiveAmount;
        return $this;
    }

    public function getInterestRate(): ?float
    {
        return $this->interestRate;
    }

    public function setInterestRate(?float $interestRate): self
    {
        $this->interestRate = $interestRate;
        return $this;
    }

    public function getShallInterestRate(): ?float
    {
        return $this->shallInterestRate;
    }

    public function setShallInterestRate(?float $shallInterestRate): self
    {
        $this->shallInterestRate = $shallInterestRate;
        return $this;
    }

    public function getCcEuroAtmFee(): ?float
    {
        return $this->ccEuroAtmFee;
    }

    public function setCcEuroAtmFee(?float $ccEuroAtmFee): self
    {
        $this->ccEuroAtmFee = $ccEuroAtmFee;
        return $this;
    }

    public function getKkOffer(): bool
    {
        return $this->kkOffer;
    }

    public function setKkOffer(bool $kkOffer): self
    {
        $this->kkOffer = $kkOffer;
        return $this;
    }
}
