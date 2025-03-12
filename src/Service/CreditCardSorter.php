<?php

namespace App\Service;

use App\Entity\SortingCriteria;
use App\Helper\EntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class CreditCardSorter
{
    private EntityManagerInterface $entityManager;
    private CacheItemPoolInterface $cache;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function sortAndCacheCreditCards(): void
    {
        $this->logger->info('Sorting credit cards...');

        // Fetch sorting criteria
        $criteriaRepo = $this->entityManager->getRepository(SortingCriteria::class);
        $sortingRules = $criteriaRepo->findBy([], ['priority' => 'ASC']);

        // Convert sorting rules to array format
        $sortingRulesArray = [];
        foreach ($sortingRules as $rule) {
            $sortingRulesArray[] = [
                'table' => $rule->getTableName(),
                'field' => $rule->getFieldName(),
                'direction' => $rule->getDirection(),
            ];
        }

        // Default sorting if criteria table is empty
        if (empty($sortingRulesArray)) {
            $this->logger->info('No sorting criteria found. Using default sorting.');
            $sortingRulesArray = [
                ['table' => 'credit_card_features', 'field' => 'annual_fees', 'direction' => 'ASC'],
                ['table' => 'credit_card_features', 'field' => 'annual_transaction_costs', 'direction' => 'ASC'],
                ['table' => 'credit_card_features', 'field' => 'first_year_fee', 'direction' => 'ASC'],
                ['table' => 'credit_card_features', 'field' => 'second_year_fee', 'direction' => 'ASC'],
                ['table' => 'credit_card_features', 'field' => 'incentive_amount', 'direction' => 'ASC'],
            ];
        }

        // Generate dynamic JOINs and ORDER BY clause
        $joins = [];
        $orderBy = [];

        foreach ($sortingRulesArray as $rule) {
            $table = $rule['table'];
            $field = $rule['field'];
            $direction = $rule['direction'];

            $tableEntityClass = EntityHelper::getClassNameFromTableName($this->entityManager, $table);
            $fieldName = EntityHelper::getFieldNameFromColumnName($this->entityManager, $tableEntityClass, $field);

            if ($table !== 'credit_cards') {
                $joins[$table] = "LEFT JOIN $tableEntityClass $table WITH c.id = $table.creditCard";
                $orderBy[] = "$table.$fieldName $direction";
            } else {
                $orderBy[] = "c.$fieldName $direction";
            }
        }

        $joinString = implode(' ', array_values($joins));
        $orderByString = implode(', ', $orderBy);

        // Query to fetch sorted credit card IDs
        $query = $this->entityManager->createQuery(
            "SELECT c.id
            FROM App\\Entity\\CreditCard c
            $joinString
            WHERE c.deletedAt IS NULL
            ORDER BY $orderByString"
        );

        $sortedCreditCardIds = $query->getResult();

        // Update sort field based on sorted IDs
        $sortOrder = 1;
        foreach ($sortedCreditCardIds as $creditCard) {
            $this->entityManager->createQuery(
                "UPDATE App\\Entity\\CreditCard c
                SET c.sort = :sortOrder
                WHERE c.id = :id"
            )
            ->setParameter('sortOrder', $sortOrder)
            ->setParameter('id', $creditCard['id'])
            ->execute();

            $sortOrder++;
        }

        $this->entityManager->flush();

        // Cache the sorted results
        $this->cacheSortedResults();

        $this->logger->info('Sort field updated and results cached.');
    }

    private function cacheSortedResults(): void
    {
        $this->logger->info('Caching sorted credit cards...');

        $cacheItem = $this->cache->getItem('sorted_credit_cards');

        $sortedCards = $this->entityManager->createQuery("
            SELECT c, ct, b, cf, ci 
            FROM App\\Entity\\CreditCard c
            LEFT JOIN c.cardType ct
            LEFT JOIN c.bank b
            LEFT JOIN c.features cf
            LEFT JOIN c.images ci
            WHERE c.deletedAt IS NULL
            ORDER BY c.sort ASC
        ")->getArrayResult();

        $cacheItem->set($sortedCards);
        // Optionally, do not expire or set a longer expiration time:
        $cacheItem->expiresAfter(null);
        $this->cache->save($cacheItem);

        $this->logger->info('Sorted credit cards cached.');
    }
}