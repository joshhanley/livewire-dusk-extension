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
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/livewire-dusk-extension.php', 'livewire-dusk-extension');
    }

    public function boot()
    {
        Route::get('/livewire-dusk/{component}', function ($component) {
            $class = urldecode($component);

            return app()->call(new $class());
        })->middleware('web');

        foreach (config('livewire-dusk-extension.test-directories') as $namespace => $directory) {
            $testComponents = $this->generateTestComponentsClassList($directory, $namespace);

            $testComponents->each(function ($componentClass) {
                Livewire::component($componentClass);
            });
        }
    }

    protected function generateTestComponentsClassList($directory, $namespace)
    {
        return collect(File::allFiles($directory))
            ->map(function ($file) use ($namespace) {
                return $this->generateClassNameFromFile($file, $namespace);
            })
            ->filter(function ($computedClassName) {
                return class_exists($computedClassName);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Component::class);
            });
    }

    protected function generateClassNameFromFile($file, $namespace)
    {
        return $namespace.'\\'. Str::of($file->getRelativePathname())->before('.php')->replace('/', '\\');
    }
}
