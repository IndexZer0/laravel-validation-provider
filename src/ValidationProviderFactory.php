<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

class ValidationProviderFactory
{
    public static function make($config): ValidationProvider
    {
        if (is_object($config)) {
            if (!is_a($config, ValidationProvider::class, true)) {
                throw new \Exception('Object must be a ValidationProvider');
            }
            return $config;
        }

        if (is_string($config)) {
            if (!class_exists($config) || !is_a($config, ValidationProvider::class, true)) {
                throw new \Exception('Class must be a ValidationProvider');
            }
            return new $config;
        }

        return self::handleMakeArray($config);
    }

    private static function handleMakeArray($config): ValidationProvider
    {
        if (count($config) < 2) {
            return self::handleMakeKeyValue(array_keys($config)[0], $config[array_key_first($config)]);
        }

        $validationProviders = [];
        foreach ($config as $key => $value) {
            $validationProviders[] = self::handleMakeKeyValue($key, $value);
        }

        return new AggregateValidationProvider(...$validationProviders);
    }

    private static function handleMakeKeyValue($key, $value): ValidationProvider
    {
        if (is_string($key)) {
            return new NestedValidationProvider(
                $key,
                self::make($value),
            );
        }

        return self::make($value);
    }
}
