<?php

namespace App\Command;

use App\Service\CreditCardSorter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sort-credit-cards', description: 'Sort credit cards based on defined criteria')]
class SortCreditCardsCommand extends Command
{
    private CreditCardSorter $creditCardSorter;

    public function __construct(CreditCardSorter $creditCardSorter)
    {
        parent::__construct();
        $this->creditCardSorter = $creditCardSorter;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->creditCardSorter->sortAndCacheCreditCards();
        } catch (\Exception $e) {
            $this->creditCardSorter->getLogger()->error('Failed to sort credit cards: ' . $e->getMessage());
            $this->creditCardSorter->getLogger()->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
