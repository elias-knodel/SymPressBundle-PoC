<?php

declare(strict_types=1);

namespace SymPress\Bundle\Enum;

/**
 * HTTP methods for REST API route registration.
 */
enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';

    public function isReadOnly(): bool
    {
        return $this === self::GET;
    }

    public function hasRequestBody(): bool
    {
        return match ($this) {
            self::POST, self::PUT, self::PATCH => true,
            self::GET, self::DELETE => false,
        };
    }
}
