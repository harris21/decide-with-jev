<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_home_page_redirects_to_the_preflight_screen(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/preflight');
    }
}
