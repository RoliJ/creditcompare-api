<?php

namespace App\Entity;

use App\Repository\CardTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CardTypeRepository::class)]
#[ORM\Table(name: 'card_types')]
#[ORM\Index(columns: ['name'], name: 'idx_card_type_name')]
class CardType
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'cardtypeenum', nullable: false, options: ["comment" => "cardtype_text: Credit or Debit card"])]
    private string $name;

    #[ORM\Column(type: "smallint", nullable: true, options: ["comment" => "cardtype: Card type ID; 0 = credit, 2 = debit"])]
    private int $cardType;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCardType(): int
    {
        return $this->cardType;
    }

    public function setCardType(int $cardType): self
    {
        $this->cardType = $cardType;
        return $this;
    }
}
