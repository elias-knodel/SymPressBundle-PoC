<?php

declare(strict_types=1);

namespace SymPress\Bundle\Cron;

use Psr\Container\ContainerInterface;
use SymPress\Bundle\Hook\HookRegistry;

/**
 * Registers cron jobs with WordPress and schedules events.
 */
final readonly class CronRegistrar
{
    public function __construct(
        private HookRegistry $registry,
        private ContainerInterface $serviceLocator,
    ) {
    }

    /**
     * Register cron action handlers and schedule events.
     */
    public function register(): void
    {
        foreach ($this->registry->getCrons() as $cron) {
            add_action(
                $cron->hook,
                function () use ($cron): void {
                    $service = $this->serviceLocator->get($cron->serviceId);
                    $service->{$cron->method}();
                },
            );

            if (!wp_next_scheduled($cron->hook)) {
                wp_schedule_event(time(), $cron->schedule, $cron->hook);
            }
        }
    }
}
