<?php

namespace App\Entity;

use App\Repository\CreditCardImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CreditCardImageRepository::class)]
#[ORM\Table(name: "credit_card_images")]
#[ORM\Index(columns: ["credit_card_id"], name: "idx_credit_card_image")]
class CreditCardImage
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

    #[ORM\Column(type: "string", length: 255, nullable: false, options: ["comment" => "logo: Credit card logo (120x76)"])]
    private string $imageUrl;

    #[ORM\Column(type: "string", length: 255, nullable: true, options: ["comment" => "Local storage path if the image is downloaded"])]
    private ?string $localPath = null;

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

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getLocalPath(): ?string
    {
        return $this->localPath;
    }

    public function setLocalPath(?string $localPath): self
    {
        $this->localPath = $localPath;
        return $this;
    }
}
