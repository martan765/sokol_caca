<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Testy nesmí záviset na sestaveném frontendu (npm run build)
        $this->withoutVite();
    }
}
