<?php

namespace App\DataFixtures;

use App\Entity\SortingCriteria;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SortingCriteriaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $defaultSorting = [
            ['table' => 'credit_card_features', 'field' => 'annualFees', 'direction' => 'ASC', 'priority' => 1],
            ['table' => 'credit_card_features', 'field' => 'annualTransactionCosts', 'direction' => 'ASC', 'priority' => 2],
            ['table' => 'credit_card_features', 'field' => 'firstYearFee', 'direction' => 'ASC', 'priority' => 3],
            ['table' => 'credit_card_features', 'field' => 'secondYearFee', 'direction' => 'ASC', 'priority' => 4],
            ['table' => 'credit_card_features', 'field' => 'incentive_amount', 'direction' => 'ASC', 'priority' => 5],
            ['table' => 'card_types', 'field' => 'name', 'direction' => 'ASC', 'priority' => 6],
        ];
        

        foreach ($defaultSorting as $rule) {
            $sortingCriteria = new SortingCriteria();
            $sortingCriteria->setTableName($rule['table']);
            $sortingCriteria->setFieldName($rule['field']);
            $sortingCriteria->setDirection($rule['direction']);
            $sortingCriteria->setPriority($rule['priority']);
            $manager->persist($sortingCriteria);
        }

        $manager->flush();
    }
}
