<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\HasApiTokens;
use Tests\TestCase;

/**
 * Test Todo API endpoints with CRUD operations and authentication
 */
class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $apiKey = env('API_KEY');
        $this->withHeaders(['X-API-Key' => $apiKey]);    
        
        // Ensure we're using SQLite for testing
        $this->artisan('config:clear');
    }

    /** @test */
    public function test_it_lists_all_todos()
    {
        Todo::create([
            'title' => 'Todo 1',
            'description' => 'First todo',
            'is_completed' => false,
        ]);

        Todo::create([
            'title' => 'Todo 2',
            'is_completed' => true,
        ]);

        $response = $this->getJson('/api/todos');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function test_it_creates_a_new_todo()
    {
        $todo = [
            'title' => 'New Todo',
            'description' => 'Description for new todo',
            'is_completed' => false,
        ];

        $response = $this->postJson('/api/todos', $todo);

        $response->assertStatus(201);
        
        // Verify data was saved
        $this->assertDatabaseCount('todos', 1);
    }

    /** @test */
    public function test_it_retrieves_single_todo()
    {
        $todo = Todo::create([
            'title' => 'My Todo',
            'description' => 'My description',
            'is_completed' => false,
        ]);

        $response = $this->getJson("/api/todos/{$todo->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $todo->id,
            'title' => 'My Todo',
            'description' => 'My description',
            'is_completed' => false,
        ]);
    }

    /** @test */
    public function test_it_returns_404_for_nonexistent_todo()
    {
        $response = $this->getJson('/api/todos/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_it_updates_a_todo()
    {
        $todo = Todo::create([
            'title' => 'Original Title',
            'description' => null,
            'is_completed' => false,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'is_completed' => true,
        ];

        $response = $this->putJson("/api/todos/{$todo->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'is_completed' => true,
        ]);
        
        // Verify in database
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'is_completed' => true,
        ]);
    }

    /** @test */
    public function test_it_partial_updates_a_todo()
    {
        $todo = Todo::create([
            'title' => 'Original Title',
            'description' => null,
            'is_completed' => false,
        ]);

        // Only update title
        $response = $this->putJson("/api/todos/{$todo->id}", [
            'title' => 'New Title',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'New Title',
            'description' => null, // Should remain unchanged
            'is_completed' => false, // Should remain unchanged
        ]);
    }

    /** @test */
    public function test_it_deletes_a_todo()
    {
        $todo = Todo::create([
            'title' => 'To Delete',
        ]);

        $response = $this->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(204);
        
        // Verify deletion
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    /** @test */
    public function test_it_deletes_nonexistent_todo_with_404()
    {
        $response = $this->deleteJson('/api/todos/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_it_filters_todos_by_completed_status_via_query_param()
    {
        Todo::create([
            'title' => 'Completed',
            'is_completed' => true,
        ]);

        Todo::create([
            'title' => 'Not Completed',
            'is_completed' => false,
        ]);

        // Filter completed only
        $response = $this->getJson('/api/todos?completed=true');

        $response->assertStatus(200);
        
        // Verify the response contains only completed todo
        $responseData = collect($response->json('data'))->first();
        $this->assertTrue($responseData['is_completed']);
    }

    /** @test */
    public function test_it_requires_title_field_on_create()
    {
        // Create request without title - should fail validation
        $response = $this->postJson('/api/todos', [
            'description' => 'No title todo',
            'is_completed' => false,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_it_rejects_empty_title_on_create()
    {
        // Create request with empty title - should fail validation
        $response = $this->postJson('/api/todos', [
            'title' => '',
            'description' => null,
            'is_completed' => false,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_it_does_not_update_todo_when_missing_id()
    {
        $response = $this->putJson('/api/todos/0', [
            'title' => 'New Title',
        ]);

        $response->assertStatus(422); // Validation error for invalid id
    }
}
