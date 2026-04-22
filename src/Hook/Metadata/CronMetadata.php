<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook\Metadata;

/**
 * Value object holding metadata for a WordPress cron job registration.
 */
final readonly class CronMetadata
{
    public function __construct(
        public string $hook,
        public string $schedule,
        public string $serviceId,
        public string $method,
        public ?int $customInterval = null,
        public ?string $customDisplayName = null,
    ) {
    }
}
