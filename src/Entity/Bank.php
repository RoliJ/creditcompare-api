<?php

namespace App\Entity;

use App\Repository\BankRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: BankRepository::class)]
#[ORM\Table(name: 'banks')]
#[ORM\Index(columns: ['name'], name: 'idx_bank_name')]
class Bank
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', unique: true, nullable: false, options: ["comment" => "bankid: Unique ID of the bank"])]
    private int $bankId;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false, options: ["comment" => "bank: Name of the bank"])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true, options: ["comment" => "Extra description about the bank"])]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: CreditCard::class, mappedBy: 'bank')]
    private Collection $creditCards;

    public function __construct()
    {
        $this->creditCards = new ArrayCollection();
    }

    // Getters and Setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBankId(): int
    {
        return $this->bankId;
    }

    public function setBankId(int $bankId): self
    {
        $this->bankId = $bankId;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection|CreditCard[]
     */
    public function getCreditCards(): Collection
    {
        return $this->creditCards;
    }

    public function addCreditCard(CreditCard $creditCard): self
    {
        if (!$this->creditCards->contains($creditCard)) {
            $this->creditCards[] = $creditCard;
            $creditCard->setBank($this);
        }

        return $this;
    }

    public function removeCreditCard(CreditCard $creditCard): self
    {
        if ($this->creditCards->removeElement($creditCard)) {
            // Soft delete the CreditCard entity
            $creditCard->setDeletedAt(new \DateTime());
        }

        return $this;
    }
}
