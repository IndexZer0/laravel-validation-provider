<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

class NestedValidationProvider extends AbstractValidationProvider
{
    public function __construct(
        string $nestedKey,
        public readonly ValidationProvider $validationProvider
    ) {
        $this->prependNestedKey($nestedKey);
    }

    public function rules(): array
    {
        return $this->mapWithKeys($this->validationProvider->rules(), function ($value, $key) {
            return [join('.', [$this->getNestedKeyForLevel(), $key]) => $value];
        });
    }

    public function messages(): array
    {
        return $this->mapWithKeys($this->validationProvider->messages(), function ($value, $key) {
            return [join('.', [$this->getNestedKeyForLevel(), $key]) => $value];
        });
    }

    public function attributes(): array
    {
        return $this->mapWithKeys($this->validationProvider->attributes(), function ($value, $key) {
            return [join('.', [$this->getNestedKeyForLevel(), $key]) => $value];
        });
    }

    public function prependNestedKey(string $nestedKey): void
    {
        parent::prependNestedKey($nestedKey);
        $this->validationProvider->prependNestedKey($nestedKey);
    }

    private function getNestedKeyForLevel(): string
    {
        return $this->nestedKey[count($this->nestedKey) - 1];
    }

    private function mapWithKeys(array $array, callable $callback): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }
}
