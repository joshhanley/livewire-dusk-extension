<?php

namespace LivewireDuskExtension;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;

class LivewireDuskExtensionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::get('/livewire-dusk/{component}', function ($component) {
            $class = urldecode($component);

            return app()->call(new $class());
        })->middleware('web');

        $testComponents = $this->generateTestComponentsClassList();

        $testComponents->each(function ($componentClass) {
            Livewire::component($componentClass);
        });
    }

    protected function getTestsDirectory()
    {
        return base_path('tests/Browser');
    }

    protected function generateTestComponentsClassList()
    {
        return collect(File::allFiles($this->getTestsDirectory()))
            ->map(function ($file) {
                return $this->generateClassNameFromFile($file);
            })
            ->filter(function ($computedClassName) {
                return class_exists($computedClassName);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Component::class);
            });
    }

    protected function generateClassNameFromFile($file)
    {
        return $this->getTestsNamespace().'\\'. Str::of($file->getRelativePathname())->before('.php')->replace('/', '\\');
    }

    public function getTestsNamespace()
    {
        return 'Tests\\Browser';
    }
}
