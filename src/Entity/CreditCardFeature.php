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

    #[ORM\ManyToOne(targetEntity: CreditCard::class, inversedBy: "images")]
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

    // Getters and setters...
}
