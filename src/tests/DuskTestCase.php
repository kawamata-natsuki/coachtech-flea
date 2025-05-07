<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    public static function prepare()
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }
}
