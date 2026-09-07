<?php

namespace Tests;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        if ($this->app->bound(Gate::class)) {
            $this->app->forgetInstance(Gate::class);
        }

        parent::tearDown();
    }
}
