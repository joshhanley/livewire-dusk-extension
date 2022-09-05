<?php

namespace LivewireDuskExtension;

use Laravel\Dusk\Browser;
use Livewire\Macros\DuskBrowserMacros;
use Laravel\Dusk\TestCase as DuskTestCase;

abstract class LivewireDuskExtensionTestCase extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Browser::mixin(new DuskBrowserMacros());
    }
}
