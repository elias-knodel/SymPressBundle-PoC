<?php

declare(strict_types=1);

namespace SymPress\Bundle\Rest\PermissionCallback;

/**
 * Permission callback that checks a WordPress capability.
 */
final readonly class CapabilityPermission
{
    public function __construct(
        private string $capability,
    ) {
    }

    public function __invoke(): bool
    {
        return current_user_can($this->capability);
    }
}
