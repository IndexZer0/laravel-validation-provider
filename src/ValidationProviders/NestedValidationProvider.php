<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

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

    protected function getNestedKeyPrefix(): string
    {
        return $this->nestedKey[count($this->nestedKey) - 1];
    }

    private function mapWithKeys(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {

            $assoc = (function ($value, $key) {
                return [join('.', [$this->getNestedKeyPrefix(), $key]) => $value];
            })($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }
}
