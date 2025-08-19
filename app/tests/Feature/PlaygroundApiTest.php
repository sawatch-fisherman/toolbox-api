<?php

namespace Tests\Feature;

use Tests\TestCase;

class PlaygroundApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_sample_api_returns_expected_response(): void
    {
        $response = $this->getJson('/api/playground/sample');

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'This is a sample API response.',
            ]);
    }
}
