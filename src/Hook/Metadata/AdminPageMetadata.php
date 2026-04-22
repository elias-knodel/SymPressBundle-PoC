<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook\Metadata;

/**
 * Value object holding metadata for a WordPress admin page registration.
 */
final readonly class AdminPageMetadata
{
    public function __construct(
        public string $title,
        public string $menuSlug,
        public string $serviceId,
        public string $capability = 'manage_options',
        public string $icon = 'dashicons-admin-generic',
        public int $position = 99,
        public ?string $parentSlug = null,
    ) {
    }

    public function isSubmenu(): bool
    {
        return $this->parentSlug !== null;
    }
}
