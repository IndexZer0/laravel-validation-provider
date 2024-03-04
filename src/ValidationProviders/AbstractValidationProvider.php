<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use Illuminate\Contracts\Validation\Validator;
use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

abstract class AbstractValidationProvider implements ValidationProvider
{
    protected array $nestedKey = [];

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }

    public function dependentField(string $otherField): string
    {
        $nestedKey = $this->getNestedKeyDotNotation();

        if ($nestedKey === '') {
            return $otherField;
        }

        return join('.', [$nestedKey, $otherField]);
    }

    public function prependNestedKey(string $nestedKey): void
    {
        array_unshift($this->nestedKey, $nestedKey);
    }

    protected function getNestedKeyDotNotation(): string
    {
        return join('.', $this->nestedKey);
    }

    public function createValidator($data): Validator
    {
        return \Illuminate\Support\Facades\Validator::make(
            $data,
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
    }

    public function validate($data): array
    {
        return ($this->createValidator($data))->validate();
    }

    public function nested(string $nestedKey): ValidationProvider
    {
        return new NestedValidationProvider(
            $nestedKey,
            $this
        );
    }

    public function nestedArray(string $nestedKey): ValidationProvider
    {
        return new NestedValidationProvider(
            $nestedKey,
            new NestedValidationProvider(
                '*',
                $this
            )
        );
    }

    public function with(string|ValidationProvider $validationProvider): ValidationProvider
    {
        if (is_string($validationProvider)) {
            if (!class_exists($validationProvider) || !is_a($validationProvider, ValidationProvider::class, true)) {
                throw new \Exception('Class must be a ValidationProvider');
            }
            $validationProvider = new $validationProvider;
        }

        return new AggregateValidationProvider(
            $validationProvider,
            $this,
        );
    }
}
