<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // 如果返回302，验证是否重定向到登录页
        if ($response->status() === 302) {
            $response->assertRedirect('/login');
        } else {
            // 否则验证返回200
            $response->assertStatus(200);
        }
    }
}
