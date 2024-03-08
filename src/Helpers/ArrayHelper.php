<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Helpers;

class ArrayHelper
{
    /*
     * Copy of Arr::mapWithKeys() because function isn't in laravel 10 lowest version.
     */
    public static function mapWithKeys(array $array, callable $callback)
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
