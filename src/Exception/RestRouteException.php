<?php

declare(strict_types=1);

namespace SymPress\Bundle\Exception;

final class RestRouteException extends \RuntimeException
{
    public static function invalidRoute(string $route, string $className, string $methodName): self
    {
        return new self(\sprintf(
            'Invalid REST route "%s" on %s::%s. Routes must start with "/".',
            $route,
            $className,
            $methodName,
        ));
    }

    public static function missingNamespace(string $className, string $methodName): self
    {
        return new self(\sprintf(
            'REST route on %s::%s is missing a namespace (e.g., "myplugin/v1").',
            $className,
            $methodName,
        ));
    }
}
