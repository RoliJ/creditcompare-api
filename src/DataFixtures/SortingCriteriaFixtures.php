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
            ['table' => 'credit_card_features', 'field' => 'annual_fees', 'direction' => 'ASC', 'priority' => 1],
            ['table' => 'credit_card_features', 'field' => 'annual_transaction_costs', 'direction' => 'ASC', 'priority' => 2],
            ['table' => 'credit_card_features', 'field' => 'first_year_fee', 'direction' => 'ASC', 'priority' => 3],
            ['table' => 'credit_card_features', 'field' => 'second_year_fee', 'direction' => 'ASC', 'priority' => 4],
            ['table' => 'credit_card_features', 'field' => 'incentive_amount', 'direction' => 'ASC', 'priority' => 5],
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
