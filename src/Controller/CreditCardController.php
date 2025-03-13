<?php

namespace App\Controller;

use App\Service\CreditCardSorter;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CreditCardController extends AbstractController
{
    private CacheItemPoolInterface $cache;
    private CreditCardSorter $creditCardSorter;

    public function __construct(CacheItemPoolInterface $cache, CreditCardSorter $creditCardSorter)
    {
        $this->cache = $cache;
        $this->creditCardSorter = $creditCardSorter;
    }

    #[Route('/api/credit-cards', name: 'api_credit_cards', methods: ['GET'])]
    public function getCreditCards(): JsonResponse
    {
        // Retrieve cached credit cards
        $cacheItem = $this->cache->getItem('sorted_credit_cards');
        if (!$cacheItem->isHit()) {
            // Cache item is not available, call CreditCardSorter service
            $this->creditCardSorter->sortAndCacheCreditCards();
            $cacheItem = $this->cache->getItem('sorted_credit_cards');
        }
        $creditCards = $cacheItem->isHit() ? $cacheItem->get() : [];

        return $this->json($creditCards);
    }
}
