<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Disable CSRF verification for all feature tests (Laravel 12 uses ValidateCsrfToken).
        // Auth & authorization middleware remain active (needed for 403 assertions).
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }
}


