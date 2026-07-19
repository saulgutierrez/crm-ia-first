<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_application_autoload_works(): void
    {
        $this->assertTrue(class_exists(\App\Helpers\Response::class));
        $this->assertTrue(class_exists(\App\Helpers\Session::class));
        $this->assertTrue(class_exists(\App\Helpers\Csrf::class));
        $this->assertTrue(class_exists(\App\Helpers\Pagination::class));
    }

    public function test_env_is_loaded(): void
    {
        $this->assertEquals('testing', $_ENV['APP_ENV'] ?? getenv('APP_ENV'));
    }

    public function test_csrf_token_generation(): void
    {
        $token = \App\Helpers\Csrf::generate();
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex chars
    }
}
