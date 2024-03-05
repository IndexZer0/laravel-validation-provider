<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Exceptions\InvalidArgumentException;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

class ValidationProviderFactory
{
    public function make(ValidationProvider|string|array $config): ValidationProvider
    {
        if (is_a($config, ValidationProvider::class)) {
            return $config;
        }

        if (is_string($config)) {
            return ValidationProviderFactory::instantiateValidationProvider($config);
        }

        if (count($config) < 1) {
            throw new InvalidArgumentException('Empty array provided');
        }

        return $this->makeFromArray($config);
    }

    private function makeFromArray(array $config): ValidationProvider
    {
        $validationProviders = [];
        foreach ($config as $key => $value) {
            $validationProviders[] = $this->makeArrayElement($key, $value);
        }

        if (count($validationProviders) < 2) {
            return $validationProviders[0];
        }

        return new AggregateValidationProvider(...$validationProviders);
    }

    private function makeArrayElement($key, $value): ValidationProvider
    {
        if (is_string($key)) {
            return new NestedValidationProvider(
                $key,
                $this->make($value),
            );
        }

        return $this->make($value);
    }

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
