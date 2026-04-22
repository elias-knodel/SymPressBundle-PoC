<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook\Metadata;

/**
 * Value object holding metadata for a WordPress action hook registration.
 */
final readonly class ActionMetadata
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
