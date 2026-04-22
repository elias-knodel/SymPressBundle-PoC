<?php

declare(strict_types=1);

namespace SymPress\Bundle\Rest\PermissionCallback;

/**
 * Permission callback that always grants access.
 */
final readonly class PublicPermission
{
    public function __invoke(): bool
    {
        return true;
    }
}
