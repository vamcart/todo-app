<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of all todos.
     * GET /api/todos
     */
    public function index(): JsonResponse
    {
        $query = Todo::query();

        // Optional filters
        if ($request = request()) {
            if ($request->has('completed')) {
                $query->where('is_completed', (bool) $request->get('completed'));
            }
        }

        $todos = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($todos, 200);
    }

    /**
     * Store a newly created todo in storage.
     * POST /api/todos
     */
    public function store(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);

        // Create todo (user_id is null by default for public todos)
        $todo = Todo::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_completed' => $validated['is_completed'] ?? false,
        ]);

        return response()->json($todo->load('user'), 201);
    }

    /**
     * Display the specified todo.
     * GET /api/todos/{id}
     */
    public function show(int $id): JsonResponse
    {
        $todo = Todo::findOrFail($id);

        return response()->json($todo->load('user'), 200);
    }

    /**
     * Update the specified todo in storage.
     * PUT /api/todos/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Find todo
        $todo = Todo::findOrFail($id);

        // Validate request
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);

        // Update fields
        if (isset($validated['title'])) {
            $todo->title = $validated['title'];
        }
        if (isset($validated['description'])) {
            $todo->description = $validated['description'];
        }
        if (isset($validated['is_completed'])) {
            $todo->is_completed = $validated['is_completed'];
        }

        $todo->save();

        return response()->json($todo->load('user'), 200);
    }

    /**
     * Remove the specified todo from storage.
     * DELETE /api/todos/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        // Find and delete todo
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json(null, 204);
    }
}
