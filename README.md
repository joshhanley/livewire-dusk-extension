# Livewire Dusk Extension

Livewire Dusk Extension adds support for testing individual Livewire components in your app with Laravel Dusk.

## Getting Started

It's recommended you read the documentation of these packages before going through this document:

- [Livewire](https://laravel-livewire.com/docs)
- [Laravel Dusk](https://laravel.com/docs/dusk)

## Installation

To install through composer, run the following command from terminal:

```bash
composer require --dev joshhanley/livewire-dusk-extension
```

## Usage

To use this package you need to:

### Ensure Laravel Dusk is installed and configured

Ensure Laravel Dusk is installed and configured as per the [Laravel Dusk documentation](https://laravel.com/docs/dusk)

### Update DuskTestCase

Laravel Dusk creates a file `tests\DuskTestCase.php` which needs to be updated to extend `LivewireDuskExtension`

```php
use LivewireDuskExtension\LivewireDuskExtensionTestCase;

abstract class DuskTestCase extends LivewireDuskExtensionTestCase
```

### Configure Tests Directory and Namespace

This package assumes you have a `tests\Browser` directory at the root of your project and that it's namespace is `Tests\Browser`.

If you have a different configuration, you can publish the Livewire Dusk Extension config and specify your tests namespaces and directories.

To publish the config run

```bash
php artisan vendor:publish --provider="LivewireDuskExtension\LivewireDuskExtensionServiceProvider"
```

Then open the config and update the `test-directories` array with your tests namespace and directory details, with the namespace being the key and the directory is the value.

```php
'test-directories' => [
    'My\\Custom\\Namespace => base_path('my/custom/namespace'),
],
```

### Create a Test

You can now create a dusk test that uses `Livewire::visit()` to test your component.

To do this, pass the `$browser` object and the class name of the component you want to test into the `Livewire::visit()` method.

Then you can chain assertions from the `visit()` call.

```php
public function testExample()
{
    $this->browse(function (Browser $browser) {
        Livewire::visit($browser, SampleComponent::class)
            ->assertSee('Sample!');
    });
}
```

### Using a Test Component

If you don't want to test a Livewire component that exists in your app, and instead want to test something like a Blade Component that has Livewire interactivity, then you can create a test Livewire component.

In the same directory as your test, create a Livewire component and reference that in your test. This package will autoload the component for you.

Then in your test component you can include any Blade Components or Alpine, etc that you want to test out.

## Troubleshooting

This is just a convenience wrapper around Laravel Dusk to make testing Livewire Components in your app easier.

Consult the documentation for the relevant packages for troubleshooting.

- [Livewire](https://laravel-livewire.com/docs)
- [Laravel Dusk](https://laravel.com/docs/dusk)
