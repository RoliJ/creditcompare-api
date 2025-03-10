<?php

namespace App\Entity;

use App\Repository\CreditCardRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CreditCardRepository::class)]
#[ORM\Table(name: 'credit_cards')]
#[ORM\Index(columns: ['product_id'], name: 'idx_credit_card_product')]
#[ORM\Index(columns: ['bank_id'], name: 'idx_credit_card_bank')]
#[ORM\Index(columns: ['card_type_id'], name: 'idx_credit_card_type')]
#[ORM\Index(columns: ['sort'], name: 'idx_credit_card_sort')]
#[ORM\Index(columns: ['has_admin_edition'], name: 'idx_credit_card_has_admin_edition')]
class CreditCard
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', unique: true, nullable: false, options: ["comment" => "product id & productid: Unique ID of the product"])]
    private int $productId;

    #[ORM\Column(type: 'string', length: 255, nullable: false, options: ["comment" => "produkt: Product title"])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true, options: ["comment" => "anmerkungen: Extra information"])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: CardType::class, inversedBy: 'creditCards')]
    #[ORM\JoinColumn(nullable: false)]
    private CardType $cardType;

    #[ORM\ManyToOne(targetEntity: Bank::class, inversedBy: 'creditCards')]
    #[ORM\JoinColumn(nullable: false)]
    private Bank $bank;

    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $sort = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $has_admin_edition = false;

    #[ORM\OneToMany(targetEntity: CreditCardFeature::class, mappedBy: 'creditCard')]
    private Collection $features;

    #[ORM\OneToMany(targetEntity: CreditCardImage::class, mappedBy: 'creditCard')]
    private Collection $images;

    public function __construct()
    {
        $this->features = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    // Getters and Setters...
}
