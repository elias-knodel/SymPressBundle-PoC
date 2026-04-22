<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook\Metadata;

/**
 * Value object holding metadata for a WordPress AJAX action registration.
 */
final readonly class AjaxMetadata
{
    public function __construct(
        public string $action,
        public string $serviceId,
        public string $method,
        public bool $public = false,
    ) {
    }
}
