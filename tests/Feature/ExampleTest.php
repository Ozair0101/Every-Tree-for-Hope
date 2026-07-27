<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The home page still renders.
     *
     * Laravel's scaffolding ships this with RefreshDatabase commented out, which
     * was fine when the home page was static. It now sums planted trees out of
     * the `events` table, so without a schema the request 500s and the failure
     * looks like a bug in the page rather than a missing database.
     */
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
