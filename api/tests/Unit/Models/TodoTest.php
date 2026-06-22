<?php

namespace Tests\Unit\Models;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Test Todo model behavior including scopes and relationships
 */
class TodoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure database is sqlite for testing
        $this->artisan('config:clear');
    }

    /** @test */
    public function it_creates_a_new_todo()
    {
        $todo = Todo::create([
            'title' => 'My First Todo',
            'description' => 'This is my first todo description',
            'is_completed' => false,
        ]);

        $this->assertDatabaseCount('todos', 1);
    }

    /** @test */
    public function it_retrieves_all_todos()
    {
        Todo::create([
            'title' => 'Todo 1',
            'description' => null,
            'is_completed' => false,
        ]);

        Todo::create([
            'title' => 'Todo 2',
            'description' => 'Second todo',
            'is_completed' => true,
        ]);

        $todos = Todo::all();
        $this->assertCount(2, $todos);
    }

    /** @test */
    public function it_filters_todos_by_completed_status()
    {
        Todo::create([
            'title' => 'Completed',
            'is_completed' => true,
        ]);

        Todo::create([
            'title' => 'Not Completed',
            'is_completed' => false,
        ]);

        // Test completed scope
        $completed = Todo::completed()->get();
        $this->assertCount(1, $completed);

        // Test pending scope
        $pending = Todo::pending()->get();
        $this->assertCount(1, $pending);
    }

    /** @test */
    public function it_gets_paginated_todos()
    {
        for ($i = 0; $i < 5; $i++) {
            Todo::create([
                'title' => "Todo {$i}",
                'is_completed' => $i % 2 === 0,
            ]);
        }

        // Test pagination
        $paginated = Todo::paginate(2);
        
        $this->assertEquals(2, count($paginated->data));
    }

    /** @test */
    public function it_updates_todo_fields()
    {
        $todo = Todo::create([
            'title' => 'Original Title',
            'description' => null,
            'is_completed' => false,
        ]);

        $todo->update([
            'title' => 'Updated Title',
            'description' => 'Added description',
            'is_completed' => true,
        ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'description' => 'Added description',
            'is_completed' => true,
        ]);
    }

    /** @test */
    public function it_deletes_a_todo()
    {
        $todo = Todo::create([
            'title' => 'To Be Deleted',
        ]);

        $todo->delete();

        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    /** @test */
    public function it_gets_todo_by_id()
    {
        $todo = Todo::create([
            'title' => 'Single Todo',
            'description' => 'Only todo',
            'is_completed' => true,
        ]);

        $retrieved = Todo::find($todo->id);

        $this->assertNotNull($retrieved);
    }

    /** @test */
    public function completed_at_accessor_returns_updated_at_when_completed()
    {
        $todo = Todo::create([
            'title' => 'Completed Item',
            'is_completed' => true,
        ]);

        $todo->updated_at = now();
        $todo->save();

        $this->assertEquals(now()->toDateTimeString(), $todo->completed_at);
    }
}
