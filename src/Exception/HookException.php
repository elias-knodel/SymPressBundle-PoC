<?php

declare(strict_types=1);

namespace SymPress\Bundle\Exception;

final class HookException extends \RuntimeException
{
    public static function invalidHookName(string $hookName, string $className, string $methodName): self
    {
        return new self(\sprintf(
            'Invalid hook name "%s" on %s::%s. Hook names must be non-empty strings.',
            $hookName,
            $className,
            $methodName,
        ));
    }

    public static function methodNotCallable(string $className, string $methodName): self
    {
        return new self(\sprintf(
            'Method %s::%s is not callable. Hook handler methods must be public.',
            $className,
            $methodName,
        ));
    }

    public static function duplicateRegistration(string $type, string $identifier): self
    {
        return new self(\sprintf(
            'Duplicate %s registration for "%s".',
            $type,
            $identifier,
        ));
    }
}
