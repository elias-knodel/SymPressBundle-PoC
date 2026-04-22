<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook\Metadata;

/**
 * Value object holding metadata for a WordPress filter hook registration.
 */
final readonly class FilterMetadata
{
    public function __construct(
        public string $hook,
        public string $serviceId,
        public string $method,
        public int $priority = 10,
        public int $acceptedArgs = 1,
    ) {
    }
}
