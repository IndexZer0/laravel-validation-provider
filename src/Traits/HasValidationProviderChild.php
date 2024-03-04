<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Traits;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

trait HasValidationProviderChild
{
    public readonly ValidationProvider $validationProvider;

    public function prependNestedKey(string $nestedKey): void
    {
        parent::prependNestedKey($nestedKey);
        $this->validationProvider->prependNestedKey($nestedKey);
    }
}
