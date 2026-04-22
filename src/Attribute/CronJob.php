<?php

declare(strict_types=1);

namespace SymPress\Bundle\Attribute;

/**
 * Registers a method as a WordPress cron job via wp_schedule_event().
 *
 * The annotated method will be executed on the specified schedule. If no hook
 * name is provided, one will be auto-generated from the class and method name.
 * A custom interval (in seconds) can be provided for schedules not built into
 * WordPress.
 *
 * Example usage:
 *
 *     class MaintenanceTasks
 *     {
 *         #[CronJob(schedule: 'daily')]
 *         public function cleanExpiredTokens(): void
 *         {
 *             // Runs once daily. Hook name is auto-generated.
 *         }
 *
 *         #[CronJob(schedule: 'hourly', hook: 'myapp_sync_inventory')]
 *         public function syncInventory(): void
 *         {
 *             // Runs hourly with an explicit hook name.
 *         }
 *
 *         #[CronJob(schedule: 'every_five_minutes', customInterval: 300)]
 *         public function checkExternalApi(): void
 *         {
 *             // Runs every 5 minutes using a custom interval.
 *             // The custom schedule 'every_five_minutes' is automatically
 *             // registered via the cron_schedules filter.
 *         }
 *     }
 *
 * @see https://developer.wordpress.org/reference/functions/wp_schedule_event/
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class CronJob
{
    /**
     * @param string      $schedule       The recurrence schedule name (e.g., 'hourly', 'daily',
     *                                    'twicedaily', or a custom schedule name). Default 'hourly'.
     * @param string|null $hook           The action hook name for this cron event. If null, a hook
     *                                    name will be auto-generated from the class and method name.
     * @param int|null    $customInterval The interval in seconds for custom schedules. When provided,
     *                                    the schedule name will be registered automatically via the
     *                                    cron_schedules filter. Default null.
     */
    public function __construct(
        public string $schedule = 'hourly',
        public ?string $hook = null,
        public ?int $customInterval = null,
    ) {
    }
}
