<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Facades;

use Illuminate\Support\Facades\Facade;
use IndexZer0\LaravelValidationProvider\ValidationProviderFactory;

/**
 * @method static \IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider make(mixed $config)
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
