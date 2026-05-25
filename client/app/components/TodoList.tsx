'use client';

import { useState, useEffect } from 'react';
import type { PaginatedTodos, Todo } from '../api/todos';
import {
  fetchTodos,
  createTodo,
  updateTodo,
  deleteTodo,
} from '../api/todos';

interface TodoListProps {
  title?: string;
}

export default function TodoList({ title = 'Todo List' }: TodoListProps) {
  const [todos, setTodos] = useState<Todo[]>([]);
  const [page, setPage] = useState(1);
const [lastPage, setLastPage] = useState(1);
const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  
  // Form state for create new todo
  const [newTodoTitle, setNewTodoTitle] = useState('');
  const [newTodoDescription, setNewTodoDescription] = useState('');
  const [newTodoIsCompleted, setNewTodoIsCompleted] = useState(false);

  /**
   * Load todos from API on mount
   */
  useEffect(() => {
    loadTodos();
  }, []);

  /**
   * Fetch todos from Laravel API (handles pagination)
   */
  async function loadTodos() {
    try {
      setLoading(true);
      const response = await fetchTodos();
      
      // Extract data array from paginated response
      if ('data' in response && Array.isArray(response.data)) {
        setTodos(response.data);
      } else if (Array.isArray((response as any).items)) {
        // Fallback for different API responses
        setTodos((response as any).items);
      }
      
      setError(null);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load todos');
    } finally {
      setLoading(false);
    }
  }

  /**
   * Handle form submit for creating new todo
   */
  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();

    try {
      if (!newTodoTitle.trim()) {
        return; // Don't create empty todos
      }

      await createTodo({
        title: newTodoTitle.trim(),
        description: newTodoDescription.trim() || null,
        is_completed: newTodoIsCompleted,
      });
      
      // Reset form
      setNewTodoTitle('');
      setNewTodoDescription('');
      setNewTodoIsCompleted(false);
      
      // Reload todos list
      loadTodos();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to create todo');
    }
  }

  /**
   * Handle delete of a todo
   */
  async function handleDelete(id: number, e?: React.MouseEvent) {
    e?.preventDefault();
    
    try {
      await deleteTodo(id);
      loadTodos();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to delete todo');
    }
  }

  /**
   * Toggle complete state and save
   */
  async function handleToggleComplete(id: number, currentStatus: boolean) {
    try {
      await updateTodo(id, { is_completed: !currentStatus });
      loadTodos();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to toggle completion');
    }
  }

  /**
   * Render loading state
   */
  if (loading) {
    return (
      <div className="py-12 flex justify-center items-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span className="ml-3 text-gray-500">Loading todos...</span>
      </div>
    );
  }

  /**
   * Render error message
   */
  if (error) {
    return (
      <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <strong>Error:</strong> {error}
      </div>
    );
  }

  /**
   * Render empty state when no todos exist
   */
  // if (todos.length === 0) {
  //   return (
  //     <div className="text-center py-12">
  //       <p className="text-gray-500 text-lg">No todos yet</p>
  //       <p className="text-gray-400 text-sm mt-2">Add your first todo above!</p>
  //     </div>
  //   );
  // }

  /**
   * Render todos list
   */
  return (
    <div className="space-y-6">
      {/* Header with title and count */}
      <div className="flex items-center justify-between">
        <h2 className="text-xl font-semibold text-gray-800">{title}</h2>
        <span className="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
          {todos.filter(t => !t.is_completed).length} pending • 
          {todos.filter(t => t.is_completed).length} completed
        </span>
      </div>

      {/* Form for create new todo */}
      <form onSubmit={handleSubmit} className="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 className="text-sm font-medium text-gray-600 mb-3">Add New Todo</h3>
        
        <div className="space-y-3">
          <input
            type="text"
            placeholder="Todo title *"
            value={newTodoTitle}
            onChange={(e) => setNewTodoTitle(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />

          <input
            type="text"
            placeholder="Description (optional)"
            value={newTodoDescription}
            onChange={(e) => setNewTodoDescription(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />

          <label className="flex items-center gap-2 cursor-pointer">
            <input
              type="checkbox"
              checked={newTodoIsCompleted}
              onChange={(e) => setNewTodoIsCompleted(e.target.checked)}
              className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span className="text-sm text-gray-700">Mark as completed</span>
          </label>

          <button
            type="submit"
            disabled={!newTodoTitle.trim()}
            className="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-gray-300 disabled:text-gray-500"
          >
            Add Todo
          </button>
        </div>
      </form>

      {/* Todos list */}
      <div className="space-y-3">
        {todos.map((todo) => (
          <div
            key={todo.id}
            className={`bg-white p-4 rounded-lg border ${
              todo.is_completed ? 'border-green-300' : 'border-gray-200'
            } shadow-sm hover:shadow-md transition-shadow`}
          >
            {/* Todo header with checkbox */}
            <div className="flex items-start justify-between mb-2">
              <label className="flex items-center cursor-pointer flex-1 mr-4">
                <input
                  type="checkbox"
                  checked={todo.is_completed}
                  onChange={() => handleToggleComplete(todo.id, todo.is_completed)}
                  className="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 mr-2"
                />
                <span className={`text-lg font-medium ${
                  todo.is_completed ? 'line-through text-gray-400' : 'text-gray-900'
                }`}>
                  {todo.title}
                </span>
              </label>
            </div>

            {/* Todo description */}
            {todo.description && (
              <p className={`text-sm ${
                todo.is_completed ? 'text-gray-400' : 'text-gray-600'
              }`}>
                {todo.description}
              </p>
            )}

            {/* Meta info */}
            <div className="mt-3 flex items-center gap-4 text-xs text-gray-400">
              <span>ID: {todo.id}</span>
              <span>Created: {new Date(todo.created_at).toLocaleDateString()}</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
