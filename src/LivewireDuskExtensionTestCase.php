<?php

namespace LivewireDuskExtension;

use Laravel\Dusk\Browser;
use Livewire\Macros\DuskBrowserMacros;
use Laravel\Dusk\TestCase as DuskTestCase;
use Livewire\Livewire;

abstract class LivewireDuskExtensionTestCase extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Browser::mixin(new DuskBrowserMacros());

        Browser::macro('livewire', function ($class, $queryString = '') {
            return Livewire::visit($this, $class, $queryString);
        });
    }
}
