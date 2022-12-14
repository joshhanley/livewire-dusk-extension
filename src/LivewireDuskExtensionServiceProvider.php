<?php

namespace LivewireDuskExtension;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component as LivewireComponent;
use Livewire\LifecycleManager;
use Livewire\Livewire;

class LivewireDuskExtensionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/livewire-dusk-extension.php', 'livewire-dusk-extension');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/livewire-dusk-extension.php' => config_path('livewire-dusk-extension.php'),
        ]);

        foreach (config('livewire-dusk-extension.test-directories') as $namespace => $directory) {
            $testComponents = $this->generateTestComponentsClassList($directory, $namespace);

            $testComponents->each(function ($componentClass) {
                Livewire::component($componentClass);
            });
        }

        LivewireComponent::macro('mountInvokableComponent', function ($class, $componentParams) {
            $instance = new $class();

            $manager = LifecycleManager::fromInitialInstance($instance)
                ->boot()
                ->initialHydrate()
                ->mount($componentParams)
                ->renderToView();

            if ($instance->redirectTo) {
                return redirect()->response($instance->redirectTo);
            }

            $instance->ensureViewHasValidLivewireLayout($instance->preRenderedView);

            $layout = $instance->preRenderedView->livewireLayout;

            return app('view')->file(base_path("vendor/livewire/livewire/src/Macros/livewire-view-{$layout['type']}.blade.php"), [
                'view' => $layout['view'],
                'params' => $layout['params'],
                'slotOrSection' => $layout['slotOrSection'],
                'manager' => $manager,
            ]);
        });

        Route::get('/livewire-dusk/{component}', function ($component) {
            $class = urldecode($component);

            $parameters = request()->get('parameters');

            return LivewireComponent::mountInvokableComponent($class, $parameters);
        })->middleware('web');
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
                return is_subclass_of($class, LivewireComponent::class);
            });
    }

    protected function generateClassNameFromFile($file, $namespace)
    {
        return $namespace.'\\'. Str::of($file->getRelativePathname())->before('.php')->replace('/', '\\');
    }
}
