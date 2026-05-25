'use client';

import TodoList from './components/TodoList';

export default function Home() {
  return (
    <div className="min-h-screen bg-gray-100 py-8 px-4">
      <TodoList title="My Todo List" />
    </div>
  );
}
