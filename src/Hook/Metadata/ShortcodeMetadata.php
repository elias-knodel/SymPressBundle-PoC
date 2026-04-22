<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook\Metadata;

/**
 * Value object holding metadata for a WordPress shortcode registration.
 */
final readonly class ShortcodeMetadata
{
    public function __construct(
        public string $tag,
        public string $serviceId,
        public string $method,
    ) {
    }
}
