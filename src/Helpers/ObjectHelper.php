<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Helpers;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Exceptions\InvalidArgumentException;

class ObjectHelper
{
    public static function instantiateValidationProvider(string $fqcn): ValidationProvider
    {
        self::ensureFqcnIsValidationProvider($fqcn);
        return new $fqcn();
    }

    private static function ensureFqcnIsValidationProvider(string $fqcn): void
    {
        if (!class_exists($fqcn) || !is_a($fqcn, ValidationProvider::class, true)) {
            throw new InvalidArgumentException('Class must be a ValidationProvider');
        }
    }
}
