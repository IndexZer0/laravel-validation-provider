<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use Illuminate\Contracts\Validation\Validator;
use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

abstract class AbstractValidationProvider implements ValidationProvider
{
    protected array $nestedKey = [];

    protected int $level = 1;

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

    public function dependentField(string $field): string
    {
        $nestedKey = $this->getNestedKeyDotNotation();
        if ($nestedKey === '') {
            return $field;
        }
        return join('.', [$nestedKey, $field]);
    }

    public function prependNestedKey(string $nestedKey, bool $increaseLevel): void
    {
        if ($increaseLevel) {
            $this->increaseLevel();
        }
        array_unshift($this->nestedKey, $nestedKey);
    }

    protected function getNestedKeyDotNotation(): string
    {
        return join('.', $this->nestedKey);
    }

    public function increaseLevel(): void
    {
        $this->level++;
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
}
