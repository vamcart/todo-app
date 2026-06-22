# Todo Application

A full-stack Todo application with Next.js frontend and Laravel backend.

## Project Structure

```
/home/work/My/1/todo-react/
├── api/                    # Laravel API backend
│   ├── app/Models/Todo.php         # Todo model with scopes
│   ├── app/Http/Controllers/TodoController.php  # CRUD controller
│   ├── routes/api.php              # API routes + Swagger docs
│   ├── bootstrap/app.php           # Laravel app config (Sanctum)
│   ├── database/migrations/        # Database migrations
│   └── tests/                     # PHPUnit tests
│       ├── Unit/Models/TodoTest.php
│       └── Feature/TodoApiTest.php
├── client/                  # Next.js frontend
│   ├── app/
│   │   ├── api/todos.ts          # API client functions
│   │   ├── components/TodoList.tsx
│   │   ├── layout.tsx
│   │   └── page.tsx
│   ├── jest.config.js           # Jest configuration
│   └── setupTests.ts            # Jest setup files
└── README.md
```

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18
- npm

## Installation & Setup

### Backend (Laravel API)

```bash
cd api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
# SQLite is already configured, migrations run automatically
```

**API Port**: `http://localhost:8000`
**OpenAPI Spec**: `http://localhost:8000/api-docs`

### Frontend (Next.js Client)

```bash
cd client
npm install
```

**Client Port**: `http://localhost:3000`

## Running the Application

Terminal 1 - Laravel API Backend:
```bash
cd api
php artisan serve --host=127.0.0.1 --port=8000
```

Terminal 2 - Next.js Frontend:
```bash
cd client
npm run dev
```

Visit `http://localhost:3000` to use the app.

## API Documentation

Access Swagger/OpenAPI documentation at: `http://localhost:3000/docs`

### Available Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/todos` | List all todos (paginated) |
| POST | `/api/todos` | Create new todo |
| GET | `/api/todos/{id}` | Get single todo |
| PUT | `/api/todos/{id}` | Update todo |
| DELETE | `/api/todos/{id}` | Delete todo |

### Query Parameters

- `GET /api/todos?completed=true` - Filter by completed status

## Testing

### Backend (PHPUnit)

```bash
cd api
php artisan test
# or
vendor/bin/phpunit
```

Run specific test file:
```bash
./vendor/bin/phpunit tests/Unit/Models/TodoTest.php
```

### Frontend (Jest)

```bash
cd client
npm test
# or for watch mode:
npm run test:watch
```

## Features

### Backend (Laravel)
- ✅ RESTful API with Laravel Sanctum authentication ready
- ✅ CORS configured for Next.js frontend
- ✅ SQLite database with migration system
- ✅ OpenAPI/Swagger documentation
- ✅ Model scopes (`completed()`, `pending()`)
- ✅ Validation on all endpoints
- ✅ PHPUnit tests for models and API

### Frontend (Next.js)
- ✅ Modern UI with Tailwind CSS
- ✅ Async server functions (App Router)
- ✅ Full CRUD operations (Create, Read, Update, Delete)
- ✅ Todo completion toggle
- ✅ Loading states
- ✅ Error handling
- ✅ Empty state display
- ✅ Jest tests for components

## TODO Model Schema

```sql
CREATE TABLE todos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_completed BOOLEAN DEFAULT FALSE,
    user_id INTEGER,  -- Nullable, for future auth integration
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## License

MIT
