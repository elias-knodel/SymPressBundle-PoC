<?php

declare(strict_types=1);

namespace SymPress\Bundle\Cron;

use SymPress\Bundle\Hook\HookRegistry;

/**
 * Registers custom cron schedules via the `cron_schedules` filter.
 */
final readonly class CronScheduleProvider
{
    public function __construct(
        private HookRegistry $registry,
    ) {
    }

    /**
     * @param array<string, array{interval: int, display: string}> $schedules
     *
     * @return array<string, array{interval: int, display: string}>
     */
    public function addSchedules(array $schedules): array
    {
        foreach ($this->registry->getCrons() as $cron) {
            if ($cron->customInterval !== null && !isset($schedules[$cron->schedule])) {
                $schedules[$cron->schedule] = [
                    'interval' => $cron->customInterval,
                    'display' => $cron->customDisplayName ?? ('Every '.$cron->customInterval.' seconds'),
                ];
            }
        }

        return $schedules;
    }
}
