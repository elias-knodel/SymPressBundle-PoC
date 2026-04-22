<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook\Metadata;

/**
 * Value object holding metadata for a WordPress REST API route registration.
 */
final readonly class RestRouteMetadata
{
    public function __construct(
        public string $namespace,
        public string $route,
        /** @var list<string> */
        public array $methods,
        public string $serviceId,
        public string $method,
        public ?string $permission = null,
    ) {
    }
}
