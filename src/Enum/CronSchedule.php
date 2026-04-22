<?php

declare(strict_types=1);

namespace SymPress\Bundle\Enum;

/**
 * WordPress cron schedule intervals.
 *
 * Built-in schedules map to WordPress defaults. Use CUSTOM with
 * the interval parameter on #[CronJob] for custom schedules.
 */
enum CronSchedule: string
{
    case HOURLY = 'hourly';
    case TWICE_DAILY = 'twicedaily';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';

    /**
     * Interval in seconds for this schedule.
     */
    public function intervalInSeconds(): int
    {
        return match ($this) {
            self::HOURLY => 3600,
            self::TWICE_DAILY => 43200,
            self::DAILY => 86400,
            self::WEEKLY => 604800,
        };
    }

    /**
     * Human-readable display name.
     */
    public function displayName(): string
    {
        return match ($this) {
            self::HOURLY => 'Once Hourly',
            self::TWICE_DAILY => 'Twice Daily',
            self::DAILY => 'Once Daily',
            self::WEEKLY => 'Once Weekly',
        };
    }
}
