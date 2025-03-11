<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use App\Repository\SortingCriteriaRepository;

#[ORM\Entity(repositoryClass: SortingCriteriaRepository::class)]
#[ORM\Table(name: 'sorting_criteria')]
#[ORM\UniqueConstraint(name: 'unique_field_priority', columns: ['field_name', 'priority'])]
class SortingCriteria
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100, options: ["comment" => "Table name where the field resides, e.g. 'credit_card_features'"])]
    private string $tableName;

    #[ORM\Column(type: 'string', length: 100, options: ["comment" => "Field name to sort by, e.g. 'annualFees'"])]
    private string $fieldName;

    #[ORM\Column(type: 'string', length: 4, options: ['default' => 'ASC', "comment" => "Sort direction: ASC or DESC"])]
    private string $direction = 'ASC';

    #[ORM\Column(type: 'integer', options: ["comment" => "Sorting priority, 1 for highest priority"])]
    private int $priority;

    // Getters and Setters...

    public function getId(): int
    {
        return $this->id;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): self
    {
        $this->fieldName = $fieldName;
        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }
}
