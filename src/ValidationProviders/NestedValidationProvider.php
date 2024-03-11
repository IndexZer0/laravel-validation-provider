<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use Illuminate\Support\Arr;
use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Traits\HasValidationProviderChild;

class NestedValidationProvider extends AbstractValidationProvider
{
    use HasValidationProviderChild;

    public function __construct(
        string $nestedKey,
        public readonly ValidationProvider $validationProvider
    ) {
        $this->prependNestedKey($nestedKey);
    }

    /*
     * --------------------------------
     * Core
     * --------------------------------
     */

    public function rules(): array
    {
        return $this->mapWithKeys($this->validationProvider->rules());
    }

    public function messages(): array
    {
        return $this->mapWithKeys($this->validationProvider->messages());
    }

    public function attributes(): array
    {
        return $this->mapWithKeys($this->validationProvider->attributes());
    }

    /*
     * --------------------------------
     * Helpers
     * --------------------------------
     */

    protected function getNestedKeyPrefix(): string
    {
        return $this->nestedKey[count($this->nestedKey) - 1];
    }

    private function mapWithKeys(array $array): array
    {
        return Arr::mapWithKeys($array, function ($value, $key) {
            return [join('.', [$this->getNestedKeyPrefix(), $key]) => $value];
        });
    }
}
