<?php

namespace App\Entity;

use App\Repository\CreditCardEditRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CreditCardEditRepository::class)]
#[ORM\Table(name: 'credit_card_edits')]
#[ORM\Index(columns: ['credit_card_id'], name: 'idx_edit_credit_card')]
class CreditCardEdit
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $editedBy;

    #[ORM\ManyToOne(targetEntity: CreditCard::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CreditCard $creditCard;

    #[ORM\Column(type: 'string', length: 50)]
    private string $tableName;

    #[ORM\Column(type: 'string', length: 50)]
    private string $columnName;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $oldValue = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $newValue = null;

    // Getters and Setters...
}
