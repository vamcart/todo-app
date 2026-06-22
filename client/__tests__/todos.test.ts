// Set environment variables first
process.env.NEXT_PUBLIC_API_BASE_URL = 'http://localhost:8000/api';
process.env.NEXT_PUBLIC_API_KEY = 'd9f43933a766ade69b20c1bdab8a83a9';

import { fetchTodos, createTodo, fetchTodo, updateTodo, deleteTodo } from '../app/api/todos';

  // Mock global.fetch
const mockFetch = jest.fn();
global.fetch = mockFetch as any;

describe('API client', () => {
  const baseUrl = process.env.NEXT_PUBLIC_API_BASE_URL as string;
  const apiKey   = process.env.NEXT_PUBLIC_API_KEY as string;


  beforeEach(() => {
    mockFetch.mockReset();
  });

  test('fetchTodos returns paginated data', async () => {
    const payload = { data: [{ id:1, title:'a', is_completed:false, created_at:'2026-01-01' }] };
    mockFetch.mockResolvedValueOnce({ ok:true, json:()=>Promise.resolve(payload) });

    const res = await fetchTodos();
    expect(res).toEqual(payload);
    expect(mockFetch).toHaveBeenCalledWith(`${baseUrl}/todos`, {
      headers:{ 'X-API-Key': apiKey }
    });
  });

  test('createTodo sends correct body', async () => {
    const todo = { id:2, title:'b', is_completed:true, created_at:'2026-01-02' };
    mockFetch.mockResolvedValueOnce({ ok:true, json:()=>Promise.resolve(todo) });

    const res = await createTodo({ title:'b' });
    expect(res).toEqual(todo);
    expect(mockFetch).toHaveBeenCalledWith(`${baseUrl}/todos`, {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        Accept:'application/json',
        'X-API-Key': apiKey
      },
      body:JSON.stringify({ title:'b' })
    });
  });

  test('fetchTodo throws on non-ok response', async () => {
    mockFetch.mockResolvedValueOnce({ ok:false, status:404, statusText:'Not Found'});
    await expect(fetchTodo(1)).rejects.toThrow(/Failed to fetch todo/);
  });
});
