<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Helpers\ArrayHelper;
use IndexZer0\LaravelValidationProvider\Traits\HasValidationProviderChild;

class MapAttributesValidationProvider extends AbstractValidationProvider
{
    use HasValidationProviderChild;

    public function __construct(
        public readonly array $mapAttributes,
        public readonly ValidationProvider $validationProvider
    ) {

    }

    /*
     * --------------------------------
     * Core
     * --------------------------------
     */
    public function rules(): array
    {
        return $this->mapRules($this->validationProvider->rules());
    }

    public function messages(): array
    {
        return $this->mapMessages();
    }

    public function attributes(): array
    {
        return $this->mapRules($this->validationProvider->attributes());
    }

    /*
     * --------------------------------
     * Helpers
     * --------------------------------
     */

    private function mapRules(array $array): array
    {
        return ArrayHelper::mapWithKeys($array, function ($value, $key) {
            if (array_key_exists($key, $this->mapAttributes)) {
                return [$this->mapAttributes[$key] => $value];
            }
            return [$key => $value];
        });
    }

    private function mapMessages(): array
    {
        return ArrayHelper::mapWithKeys($this->validationProvider->messages(), function ($value, $key) {
            foreach ($this->mapAttributes as $from => $to) {
                if (str_starts_with($key, $from . '.')) {
                    return [str_replace($from . '.', $to . '.', $key) => $value];
                }
            }
            return [$key => $value];
        });
    }
}
