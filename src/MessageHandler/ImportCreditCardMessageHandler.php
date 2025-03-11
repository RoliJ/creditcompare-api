<?php

namespace App\MessageHandler;

use App\Command\ImportCreditCardsCommand;
use App\Message\ImportCreditCardMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsMessageHandler]
class ImportCreditCardHandler
{
    private ImportCreditCardsCommand $importCommand;

    public function __construct(ImportCreditCardsCommand $importCommand)
    {
        $this->importCommand = $importCommand;
    }

    public function __invoke(ImportCreditCardMessage $message): void
    {
        $input = new ArrayInput([]);
        $output = new NullOutput();
        $this->importCommand->run($input, $output);
    }
}
