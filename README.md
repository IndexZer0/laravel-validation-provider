# laravel-validation-provider

[![Latest Version on Packagist](https://img.shields.io/packagist/v/indexzer0/laravel-validation-provider.svg?style=flat-square)](https://packagist.org/packages/indexzer0/laravel-validation-provider)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/indexzer0/laravel-validation-provider/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/indexzer0/laravel-validation-provider/actions?query=workflow%3Arun-tests+branch%3Amain)
[![codecov](https://codecov.io/gh/IndexZer0/laravel-validation-provider/graph/badge.svg?token=76KXOLBQYJ)](https://codecov.io/gh/IndexZer0/laravel-validation-provider)
[![Total Downloads](https://img.shields.io/packagist/dt/indexzer0/laravel-validation-provider.svg?style=flat-square)](https://packagist.org/packages/indexzer0/laravel-validation-provider)

---

- Write all your validation rules in clean reusable composable providers.
- **Standardise** the way you define and use validation in `Form Requests` and elsewhere.
- **Easily** compose validation rules using multiple validation providers.
- **Conveniently** create and validate data straight from the `ValidationProvider`.

---
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
  - [Defining Validation Providers](#defining-validation-providers)
  - [Creating Validation Providers](#creating-validation-providers)
    - [Manual Creation](#manual-creation)
    - [Facade](#facade)
    - [Fluent API](#fluent-api)
  - [Service/Action Class Usage](#serviceaction-class-usage)
  - [Form Requests Usage](#form-requests-usage)
      - [Extending Abstract](#extending-abstract)
      - [Decorate With Trait](#decorate-with-trait)
  - [Available Validation Providers](#available-validation-providers)
    - [Aggregate Validation Provider](#aggregate-validation-provider)
    - [Nested Validation Provider](#nested-validation-provider)
    - [Array Validation Provider](#array-validation-provider)
    - [Custom Validation Provider](#custom-validation-provider)
    - [Exclude Attributes Validation Provider](#exclude-attributes-validation-provider)
  - [Digging Deeper](#digging-deeper)
    - [Using Fluent API](#using-fluent-api)
    - [Using Facade](#using-facade)
  - [Composing Validation Providers](#composing-validation-providers)
  - [Dependent Rules](#dependent-rules)
  - [Error Handling](#error-handling)
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
  - Should extend `AbstractValidationProvider`.

```php
class AddressValidationProvider extends AbstractValidationProvider
{
    public function rules(): array
    {
        return [
            'post_code' => ['required', 'string', 'between:1,20'],
        ];
    }

    public function messages(): array { return []; }
   
    public function attributes(): array { return []; }
}
```

---

### Creating Validation Providers

There are 3 ways to create validation providers.

In all 3 examples, were going to use the following two defined validation providers along-side this packages core validation providers to achieve validation rules of:

```php
class AuthorValidationProvider extends AbstractValidationProvider
{
    public function rules(): array
    {
        return ['name' => ['required'],];
    }
}

class BookValidationProvider extends AbstractValidationProvider
{
    public function rules(): array
    {
        return ['title' => ['required',],];
    }
}

// Desired validation rules:
// [
//     'author.name'          => ['required'],
//     'author.books'         => ['required', 'array', 'min:1', 'max:2',],
//     'author.books.*.title' => ['required'],
// ]
```

- [Manual Creation](#manual-creation)
- [Facade](#facade)
- [Fluent API](#fluent-api)

#### Manual Creation

```php
$validationProvider = new NestedValidationProvider(
    'author',
    new AggregateValidationProvider(
        new AuthorValidationProvider(),
        new CustomValidationProvider(['books' => ['required', 'array', 'min:1', 'max:2',],]),
        new ArrayValidationProvider('books', new BookValidationProvider())
    )
);
$validationProvider->rules();
```

#### Facade

```php
use IndexZer0\LaravelValidationProvider\Facades\ValidationProvider;

$validationProvider = ValidationProvider::make([
    'author' => [
        AuthorValidationProvider::class,
        new CustomValidationProvider(['books' => ['required', 'array', 'min:1', 'max:2',],]),
        new ArrayValidationProvider('books', new BookValidationProvider()),
    ],
]);
$validationProvider->rules();
```

#### Fluent API

- For the fluent API, you compose your validation providers from bottom up.

```php
$validationProvider = (new BookValidationProvider())
    ->nestedArray('books')
    ->with(new CustomValidationProvider(['books' => ['required', 'array', 'min:1', 'max:2',],]))
    ->with(AuthorValidationProvider::class)
    ->nested('author');
$validationProvider->rules();
```

---

### Service/Action Class Usage

In your services and actions `->createValidator()` and `->validate()` methods are provided for convenience.

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

#### Extending Abstract

`ValidationProviderFormRequest` is provided to extend your form requests from.

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
    ->give(AddressValidationProvider::class);
  
class StoreAddressRequest extends ValidationProviderFormRequest
{
    public function __construct(ValidationProvider $validationProvider)
    {
        $this->validationProvider = $validationProvider;
    }
}
```

#### Decorate With Trait

`HasValidationProvider` is provided to decorate your existing form requests.

Sometimes you don't have the ability to extend `ValidationProviderFormRequest`. You can instead use the `HasValidationProvider` trait in your existing form request.

```php
class StoreAddressRequest extends YourOwnExistingFormRequest
{
    use HasValidationProvider;
    
    public function prepareForValidation()
    {
        $this->validationProvider = new AddressValidationProvider();
    }
}
```

---

### Available Validation Providers

This package provides core classes that give you the ability to compose your validation providers.

- [Aggregate Validation Provider](#aggregate-validation-provider)
- [Nested Validation Provider](#nested-validation-provider)
- [Array Validation Provider](#array-validation-provider)
- [Custom Validation Provider](#custom-validation-provider)
- [Exclude Attributes Validation Provider](#exclude-attributes-validation-provider)

#### Aggregate Validation Provider

- Used when composing validation providers next to each other.

```php
class AggregateValidationProvider extends AbstractValidationProvider {}
$validationProvider = new AggregateValidationProvider(
    new AuthorValidationProvider(),
    new BookValidationProvider(),
);
$validationProvider->rules();
// [
//     'name'  => ['required'], // From AuthorValidationProvider.
//     'title' => ['required'], // From BookValidationProvider.
// ]
```

#### Nested Validation Provider

- Used when wanting to nest a validation provider inside an array.

```php
class NestedValidationProvider extends AbstractValidationProvider {}
$validationProvider = new NestedValidationProvider(
    'author',
    new AuthorValidationProvider(),
);
$validationProvider->rules();
// [
//     'author.name'  => ['required'], // From AuthorValidationProvider.
// ]
```

#### Array Validation Provider

- Used when validating an array of domain models.
- https://laravel.com/docs/10.x/validation#validating-nested-array-input

```php
class ArrayValidationProvider extends NestedValidationProvider {}
$validationProvider = new ArrayValidationProvider('books', new BookValidationProvider());
$validationProvider->rules();
// [
//     'books.*.title'  => ['required'], // From BookValidationProvider.
// ]
```

#### Custom Validation Provider

- Used when wanting to validate data without creating a dedicated ValidationProvider class.

```php
class CustomValidationProvider extends AbstractValidationProvider {}
$customRules = [
    'books' => ['required', 'array', 'min:1', 'max:2',],
];
$customMessages = [
    'books.required' => 'Provide :attribute'
];
$customAttributes = [
    'books' => 'BOOKS'
];
$validationProvider = new CustomValidationProvider($customRules, $customMessages, $customAttributes);
$validationProvider->rules();
// [
//     'books' => ['required', 'array', 'min:1', 'max:2',],
// ]
```

#### Exclude Attributes Validation Provider

- Sometimes you may want to remove certain attributes from a validation provider.

```php
class ExcludeAttributesValidationProvider extends AbstractValidationProvider {}
$validationProvider = new ExcludeAttributesValidationProvider(
    ['one'],
    new CustomValidationProvider([
        'one' => ['required',],    
        'two' => ['required',],    
    ])
);
$validationProvider->rules();
// [
//     'two' => ['required',],
// ]
```

---

### Digging Deeper

#### Using Fluent API

| Method                                                                | Returns                               |
|-----------------------------------------------------------------------|---------------------------------------|
| `nested(string $nestedKey)`                                           | `NestedValidationProvider`            |
| `nestedArray(string $nestedKey)`                                      | `ArrayValidationProvider`             |
| <code>with(string&#124;ValidationProvider $validationProvider)</code> | `AggregateValidationProvider`         |
| `exclude(array $attributes)`                                          | `ExcludeAttributesValidationProvider` |

#### Using Facade

`ValidationProvider::make(ValidationProviderInterface|string|array $config): ValidationProviderInterface`

- Can use fully qualified class name strings.

```php
// Returns AuthorValidationProvider
$validationProvider = ValidationProvider::make(AuthorValidationProvider::class);
```

- Invalid class string throws exception.

```php
// throws ValidationProviderExceptionInterface
try {
    $validationProvider = ValidationProvider::make('This is an invalid fqcn string');
} catch (\IndexZer0\LaravelValidationProvider\Contracts\ValidationProviderExceptionInterface $exception) {
    $exception->getMessage(); // Class must be a ValidationProvider
}

```

- Can use validation provider objects. Essentially does nothing.

```php
// Returns AuthorValidationProvider (same object)
$validationProvider = ValidationProvider::make(new AuthorValidationProvider());
```

- Can use arrays (fully qualified class name strings and objects).

```php
// Returns AuthorValidationProvider
$validationProvider = ValidationProvider::make([
    AuthorValidationProvider::class,
]); 

// Returns AggregateValidationProvider
$validationProvider = ValidationProvider::make([
    AuthorValidationProvider::class,
    new BookValidationProvider()
]);
```

- Array string keys create `NestedValidationProvider`.

```php
// Returns NestedValidationProvider
$validationProvider = ValidationProvider::make([
    'author' => [
        AuthorValidationProvider::class,
    ],
]);
```

- Empty array is invalid.

```php
// throws ValidationProviderExceptionInterface
try {
    $validationProvider = ValidationProvider::make([]);
} catch (\IndexZer0\LaravelValidationProvider\Contracts\ValidationProviderExceptionInterface $exception) {
    $exception->getMessage(); // Empty array provided
}
```

---

### Composing Validation Providers

#### Use case:

- You may have parts of your application that need to validate data for multiple domain concepts.
- You may want to validate data in nested arrays without introducing duplication in your rule definitions.

#### Example:

Let's look at the example of 3 routes and how you could reuse your Validation Providers.

- Route: address
  - Stores address information
  - Uses `AddressValidationProvider`
- Route: contact-details
  - Stores contact information
  - Uses `ContactValidationProvider`
- Route: profile
  - Stores address **and** contact information.
  - Uses
    - `NestedValidationProvider`
    - `AggregateValidationProvider`
    - `AddressValidationProvider`
    - `ContactValidationProvider`

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
        );
        
        // or using Facade
        $this->validationProvider = ValidationProvider::make([
            'profile' => [
                'address' => AddressValidationProvider::class
                'contact' => ContactValidationProvider::class
            ]
        ]);
    }
}
```

---

### Dependent Rules

- When using any of the [dependent](https://github.com/laravel/framework/blob/5e95946a8283a8d5c015035793f9c61c297e937f/src/Illuminate/Validation/Validator.php#L236) rules, you should use the `$this->dependentField()` helper.
  - This ensures that when using the `NestedValidationProvider` and `ArrayValidationProvider`, the dependent field will have the correct nesting.

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

### Error Handling

All exceptions thrown by the package implement `\IndexZer0\LaravelValidationProvider\Contracts\ValidationProviderExceptionInterface`.

How-ever it doesn't harm to also catch `\Throwable`.

```php
try {
    $validationProvider = ValidationProvider::make('This is an invalid fqcn string');
} catch (\IndexZer0\LaravelValidationProvider\Contracts\ValidationProviderExceptionInterface $exception) {
    $exception->getMessage(); // Class must be a ValidationProvider
} catch (\Throwable $t) {
    // Shouldn't happen - but failsafe.
}
```

---

## Package Offering
```php
// Interface
interface ValidationProvider {}

// Validation Providers
abstract class AbstractValidationProvider implements ValidationProvider {}
class AggregateValidationProvider extends AbstractValidationProvider {}
class NestedValidationProvider extends AbstractValidationProvider {}
class ArrayValidationProvider extends NestedValidationProvider {}
class CustomValidationProvider extends AbstractValidationProvider {}
class ExcludeAttributesValidationProvider extends AbstractValidationProvider {}

// Form Request
class ValidationProviderFormRequest extends \Illuminate\Foundation\Http\FormRequest {}
trait HasValidationProvider {}

// Facade
class ValidationProvider extends \Illuminate\Support\Facades\Facade {}
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

## Credits

- [IndexZer0](https://github.com/IndexZer0)
- [All Contributors](../../contributors)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
