# laravel-validation-provider

[![Latest Version on Packagist](https://img.shields.io/packagist/v/indexzer0/laravel-validation-provider.svg?style=flat-square)](https://packagist.org/packages/indexzer0/laravel-validation-provider)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/indexzer0/laravel-validation-provider/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/indexzer0/laravel-validation-provider/actions?query=workflow%3Arun-tests+branch%3Amain)
[![codecov](https://codecov.io/gh/IndexZer0/laravel-validation-provider/graph/badge.svg?token=76KXOLBQYJ)](https://codecov.io/gh/IndexZer0/laravel-validation-provider)
[![Total Downloads](https://img.shields.io/packagist/dt/indexzer0/laravel-validation-provider.svg?style=flat-square)](https://packagist.org/packages/indexzer0/laravel-validation-provider)

---

- Store all your validation logic in clean reusable composable providers.
- Avoid duplicating validation definitions.
- Standardise the way you define and use validation in `Form Requests` and elsewhere.
- Compose validation with the use of `AggregateValidationProvider` and `NestedValidationProvider`.
- Easily create and validate data straight from the `ValidationProvider`.

---
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
  - [Defining Validation Providers](#defining-validation-providers)
  - [Service Class Usage](#service-class-usage)
  - [Form Requests Usage](#form-requests-usage)
  - [Composing Validation Providers](#composing-validation-providers)
  - [Dependent Rules](#dependent-rules)
- [Package Offering](#package-offering)

---

## Requirements

- PHP Version >= 8.1
- Laravel Version >= 10

---

## Installation

You can install the package via composer:

```bash
composer require indexzer0/laravel-validation-provider
```

---

## Usage

### Defining Validation Providers

- Create granular representations of domain concepts in validation provider classes.

```php
class AddressValidationProvider extends AbstractValidationProvider
{
    public function rules(): array
    {
        return [
            'post_code' => ['required', 'string', 'between:1,20'],
            //...
        ];
    }

    public function messages(): array
    {
        // messages
    }

    public function attributes(): array
    {
        // attributes
    }
}
```

---

### Service Class Usage

```php
$addressValidationProvider = new AddressValidationProvider();

/** @var Illuminate\Validation\Validator $validator */
$validator = $addressValidationProvider->createValidator($data);

/** @var array $validated */
$validated = $addressValidationProvider->validate($data);
```

---

### Form Requests Usage

You can use validation providers in your form requests via two methods.

- [Extending Abstract](#extending-abstract)
- [Decorate With Trait](#decorate-with-trait)

---

#### Extending Abstract

- `ValidationProviderFormRequest` is provided to extend your form requests from.

Using `prepareForValidation` hook to instantiate validation provider.
```php
class StoreAddressRequest extends ValidationProviderFormRequest
{
    public function prepareForValidation()
    {
        $this->validationProvider = new AddressValidationProvider();
    }
}
```

Or using dependency injection.
```php
// In a service provider.
$this->app->when(StoreAddressRequest::class)
    ->needs(ValidationProvider::class)
    ->give([AddressValidationProvider::class]);
  
class StoreAddressRequest extends ValidationProviderFormRequest
{
    public function __construct(ValidationProvider $validationProvider)
    {
        $this->validationProvider = $validationProvider;
    }
}
```

---

#### Decorate With Trait

- `HasValidationProvider` is provided to decorate your existing form requests.

If you don't have the ability to extend `ValidationProviderFormRequest`. You can instead use the `HasValidationProvider` trait in your existing form request.

```php
class StoreAddressRequest extends FormRequest
{
    use HasValidationProvider;
    
    public function prepareForValidation()
    {
        $this->validationProvider = new AddressValidationProvider();
    }
}
```

---

### Composing Validation Providers

You may have routes that allow for storing of multiple domain concepts and want to validate data in nested arrays without duplication.

Lets look at the example of 3 routes.

- Route: address - stores address information.
- Route: contact-details - stores contact information.
- Route: profile - stores address and contact information.

```php
/*
 * ------------------
 * Address
 * ------------------
 */
Route::post('address', StoreAddress::class);
class StoreAddress extends Controller
{
    public function __invoke(StoreAddressRequest $request) {}
}
class StoreAddressRequest extends ValidationProviderFormRequest
{
    public function prepareForValidation()
    {
        $this->validationProvider = new AddressValidationProvider();
    }
}
```

```php
/*
 * ------------------
 * Contact
 * ------------------
 */
Route::post('contact-details', StoreContactDetails::class);
class StoreContactDetails extends Controller
{
    public function __invoke(StoreContactDetailsRequest $request) {}
}
class StoreContactDetailsRequest extends ValidationProviderFormRequest
{
    public function prepareForValidation()
    {
        $this->validationProvider = new ContactValidationProvider();
    }
}
```

```php
/*
 * ------------------
 * Profile
 * ------------------
 */
Route::post('profile', StoreProfile::class);
class StoreProfile extends Controller
{
    public function __invoke(StoreProfileRequest $request) {}
}
class StoreProfileRequest extends ValidationProviderFormRequest
{
    public function prepareForValidation()
    {
        $this->validationProvider = new NestedValidationProvider(
            'profile',
            new AggregateValidationProvider(
                new NestedValidationProvider(
                    'address',
                    new AddressValidationProvider()                    
                ),
                new NestedValidationProvider(
                    'contact',
                    new ContactValidationProvider()                    
                ),
            )
        )
    }
}
```

---

### Dependent Rules

- When using any of the [dependent](https://github.com/laravel/framework/blob/5e95946a8283a8d5c015035793f9c61c297e937f/src/Illuminate/Validation/Validator.php#L236) rules, you should use the `$this->dependentField()` helper.
  - This ensures that when using the `NestedValidationProvider`, the dependent field will have the correct nesting.

```php
class PriceRangeValidationProvider extends AbstractValidationProvider
{
    public function rules(): array
    {
        return [
            'min_price' => ["lt:{$this->dependentField('max_price')}"],
            'max_price' => ["gt:{$this->dependentField('min_price')}"],
        ];
    }
}

$validationProvider = new NestedValidationProvider(
    'product',
    new PriceRangeValidationProvider()
);

$validationProvider->rules();

//  [
//      "product.min_price" => [
//          "lt:product.max_price"
//      ]
//      "product.max_price" => [
//          "gt:product.min_price"
//      ]
//  ]
```

---

## Package Offering
```php
// Interface
interface ValidationProvider {}

// Validation Providers
class AbstractValidationProvider implements ValidationProvider {}
class NestedValidationProvider extends AbstractValidationProvider {}
class AggregateValidationProvider extends AbstractValidationProvider {}

// Form Request
class ValidationProviderFormRequest extends \Illuminate\Foundation\Http\FormRequest {}
trait HasValidationProvider {}
```

---

## Testing

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

---

## Credits

- [IndexZer0](https://github.com/IndexZer0)
- [All Contributors](../../contributors)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
