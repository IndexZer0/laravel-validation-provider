<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Traits;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

trait HasValidationProviderChildren
{
    /** @var ValidationProvider[] */
    public readonly array $validationProviders;

    public function prependNestedKey(string $nestedKey): void
    {
        parent::prependNestedKey($nestedKey);
        foreach ($this->validationProviders as $validationProvider) {
            $validationProvider->prependNestedKey($nestedKey);
        }
    }
}
