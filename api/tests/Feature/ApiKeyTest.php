<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Todo;

class ApiKeyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_returns_401_when_api_key_is_missing_or_invalid(): void
    {
        $response = $this->getJson('/api/todos');
        $response->assertStatus(401);

        $response = $this->withHeaders(['X-API-Key' => 'wrong-key'])->getJson('/api/todos');
        $response->assertStatus(401);
    }

    /** @test */
    public function test_it_allows_access_with_correct_api_key(): void
    {
        $apiKey = env('API_KEY');
        $response = $this->withHeaders(['X-API-Key' => $apiKey])->getJson('/api/todos');
        $response->assertStatus(200);
    }
}
