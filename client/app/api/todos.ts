/**
 * API Client for Laravel Backend
 * Base URL: http://localhost:8000/api
 */

export interface Todo {
  id: number;
  title: string;
  description?: string | null;
  is_completed: boolean;
  created_at: string;
  updated_at?: string | null;
}

export interface PaginatedTodos {
  data: Todo[];
  links?: {
    first?: string;
    last?: string;
    current_page?: number;
    per_page?: number;
  };
}

const API_BASE_URL = 'http://localhost:8000/api';

/**
 * Fetch all todos from the API
 */
export async function fetchTodos(page: number = 1): Promise<PaginatedTodos> {
  const response = await fetch(`${API_BASE_URL}/todos`, {
    headers: {
        'X-API-Key': 'd9f43933a766ade69b20c1bdab8a83a9',
    },
});
  
  if (!response.ok) {
    throw new Error(`Failed to fetch todos: ${response.status} ${response.statusText}`);
  }
  
  return response.json();
}

/**
 * Create a new todo
 */
export async function createTodo(data: {
  title: string;
  description?: string | null;
  is_completed?: boolean;
}): Promise<Todo> {
  const response = await fetch(`${API_BASE_URL}/todos`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-API-Key': 'd9f43933a766ade69b20c1bdab8a83a9',
    },
    body: JSON.stringify(data),
  });
  
  if (!response.ok) {
    throw new Error(`Failed to create todo: ${response.status} ${response.statusText}`);
  }
  
  return response.json();
}

/**
 * Fetch a single todo by ID
 */
export async function fetchTodo(id: number): Promise<Todo> {
const response = await fetch(`${API_BASE_URL}/todos/${id}`, {
    headers: {
      'Accept': 'application/json',
      'X-API-Key': 'd9f43933a766ade69b20c1bdab8a83a9',
    },
});
  
  if (!response.ok) {
    throw new Error(`Failed to fetch todo ${id}: ${response.status} ${response.statusText}`);
  }
  
  return response.json();
}

/**
 * Update a todo by ID
 */
export async function updateTodo(id: number, data: Partial<{
  title: string;
  description?: string | null;
  is_completed?: boolean;
}>): Promise<Todo> {
  const response = await fetch(`${API_BASE_URL}/todos/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-API-Key': 'd9f43933a766ade69b20c1bdab8a83a9',
    },
    body: JSON.stringify(data),
  });

  if (!response.ok) {
    throw new Error(`Failed to update todo ${id}: ${response.status} ${response.statusText}`);
  }

  return response.json();
}

/**
 * Delete a todo by ID
 */
export async function deleteTodo(id: number): Promise<void> {
  const response = await fetch(`${API_BASE_URL}/todos/${id}`, {
    method: 'DELETE',
    headers: {
      'X-API-Key': 'd9f43933a766ade69b20c1bdab8a83a9',
    },
  });

  if (!response.ok) {
    throw new Error(`Failed to delete todo ${id}: ${response.status} ${response.statusText}`);
  }
  
  return response.json();
}
