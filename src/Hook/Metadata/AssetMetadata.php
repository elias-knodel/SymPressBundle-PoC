<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook\Metadata;

/**
 * Value object holding metadata for a WordPress asset enqueue registration.
 */
final readonly class AssetMetadata
{
    public function __construct(
        public string $hook,
        public string $serviceId,
        public string $method,
    ) {
    }
}
