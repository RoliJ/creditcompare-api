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

    #[ORM\ManyToOne(targetEntity: CardType::class, inversedBy: 'creditCards')]
    #[ORM\JoinColumn(nullable: false)]
    private CardType $cardType;

    #[ORM\ManyToOne(targetEntity: Bank::class, inversedBy: 'creditCards')]
    #[ORM\JoinColumn(nullable: false)]
    private Bank $bank;

    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $sort = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $hasAdminEdition = false;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCardType(): CardType
    {
        return $this->cardType;
    }

    public function setCardType(CardType $cardType): self
    {
        $this->cardType = $cardType;
        return $this;
    }

    public function getBank(): Bank
    {
        return $this->bank;
    }

    public function setBank(Bank $bank): self
    {
        $this->bank = $bank;
        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    public function getHasAdminEdition(): bool
    {
        return $this->hasAdminEdition;
    }

    public function setHasAdminEdition(bool $hasAdminEdition): self
    {
        $this->hasAdminEdition = $hasAdminEdition;
        return $this;
    }

    /**
     * @return Collection|CreditCardFeature[]
     */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(CreditCardFeature $feature): self
    {
        if (!$this->features->contains($feature)) {
            $this->features[] = $feature;
            $feature->setCreditCard($this);
        }

        return $this;
    }

    public function removeFeature(CreditCardFeature $feature): self
    {
        if ($this->features->removeElement($feature)) {
            // Soft delete the CreditCardFeature entity
            $feature->setDeletedAt(new \DateTime());
        }

        return $this;
    }

    /**
     * @return Collection|CreditCardImage[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(CreditCardImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setCreditCard($this);
        }

        return $this;
    }

    public function removeImage(CreditCardImage $image): self
    {
        if ($this->images->removeElement($image)) {
            // Soft delete the CreditCardImage entity
            $image->setDeletedAt(new \DateTime());
        }

        return $this;
    }
}
