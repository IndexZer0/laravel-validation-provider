<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

class ExcludeAttributesValidationProvider extends AbstractValidationProvider
{
    public function __construct(
        public readonly array $excludeAttributes,
        public readonly ValidationProvider $validationProvider
    ) {

    }

    public function rules(): array
    {
        return $this->removeAttributes($this->validationProvider->rules());
    }

    public function messages(): array
    {
        return $this->removeMessages($this->validationProvider->messages());
    }

    public function attributes(): array
    {
        return $this->removeAttributes($this->validationProvider->attributes());
    }

    public function prependNestedKey(string $nestedKey): void
    {
        parent::prependNestedKey($nestedKey);
        $this->validationProvider->prependNestedKey($nestedKey);
    }

    private function removeAttributes(array $data): array
    {
        return array_filter($data, function ($key) {
            return !in_array($key, $this->excludeAttributes, true);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function removeMessages(array $data): array
    {
        return array_filter($data, function ($key) {

            $keyStartsWithAnExcludedAttribute = false;
            foreach ($this->excludeAttributes as $attribute) {
                if (str_starts_with($key, $attribute)) {
                    $keyStartsWithAnExcludedAttribute = true;
                }
            }
            return !$keyStartsWithAnExcludedAttribute;

        }, ARRAY_FILTER_USE_KEY);
    }
}
