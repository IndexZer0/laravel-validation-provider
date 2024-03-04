<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Facades;

use Illuminate\Support\Facades\Facade;
use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider as ValidationProviderContract;
use IndexZer0\LaravelValidationProvider\ValidationProviderFactory;

/**
 * @method static ValidationProviderContract make(ValidationProviderContract|string|array $config)
 *
 * @see ValidationProviderFactory
 */
class ValidationProvider extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ValidationProviderFactory::class;
    }
}
