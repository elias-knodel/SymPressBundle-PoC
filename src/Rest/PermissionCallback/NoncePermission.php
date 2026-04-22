<?php

declare(strict_types=1);

namespace SymPress\Bundle\Rest\PermissionCallback;

/**
 * Permission callback that verifies a WordPress nonce.
 */
final readonly class NoncePermission
{
    public function __construct(
        private string $action,
        private string $nonceParam = '_wpnonce',
    ) {
    }

    public function __invoke(\WP_REST_Request $request): bool
    {
        $nonce = $request->get_param($this->nonceParam);

        if (!\is_string($nonce)) {
            return false;
        }

        return (bool) wp_verify_nonce($nonce, $this->action);
    }
}
