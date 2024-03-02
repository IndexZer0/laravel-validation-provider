<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use Illuminate\Support\Arr;
use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

class NestedValidationProvider extends AbstractValidationProvider
{
    public function __construct(
        string $nestedKey,
        public readonly ValidationProvider $validationProvider
    ) {
        $this->prependNestedKey($nestedKey, false);
    }

    public function rules(): array
    {
        return Arr::mapWithKeys($this->validationProvider->rules(), function ($value, $key) {
            return [join('.', [$this->getNestedKeyForLevel(), $key]) => $value];
        });
    }

    public function messages(): array
    {
        return Arr::mapWithKeys($this->validationProvider->messages(), function ($value, $key) {
            return [join('.', [$this->getNestedKeyForLevel(), $key]) => $value];
        });
    }

    public function attributes(): array
    {
        return Arr::mapWithKeys($this->validationProvider->attributes(), function ($value, $key) {
            return [join('.', [$this->getNestedKeyForLevel(), $key]) => $value];
        });
    }

    public function prependNestedKey(string $nestedKey, bool $increaseLevel): void
    {
        parent::prependNestedKey($nestedKey, $increaseLevel);
        $this->validationProvider->prependNestedKey($nestedKey, true);
    }

    private function getNestedKeyForLevel(): string
    {
        return $this->nestedKey[$this->level - 1];
    }
}
