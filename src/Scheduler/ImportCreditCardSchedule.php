<?php

namespace App\Scheduler;

use App\Message\ImportCreditCardMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('creditcard_import')]
final class ImportCreditCardSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                // Schedule the message to run every day at 2 AM
                RecurringMessage::cron('0 2 * * *', new ImportCreditCardMessage()),
            )
            ->stateful($this->cache)
        ;
    }
}
