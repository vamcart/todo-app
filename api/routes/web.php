<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Swagger/OpenAPI docs route accessible without /api prefix
Route::get('/api-docs', function () {
    $spec = [
        'openapi' => '3.0.3',
        'info' => [
            'title' => 'Todo API',
            'version' => '1.0.0',
            'description' => 'RESTful API for Todo CRUD operations with Sanctum authentication',
        ],
        'servers' => [
            ['url' => 'http://localhost:8000/api'],
        ],
        'paths' => [
            '/todos' => [
                'get' => [
                    'summary' => 'List all todos',
                    'responses' => [
                        '200' => ['description' => 'A list of todos'],
                    ],
                ],
                'post' => [
                    'summary' => 'Create a new todo',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'title' => ['type' => 'string'],
                                        'description' => ['type' => 'string', 'nullable' => true],
                                        'is_completed' => ['type' => 'boolean'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Todo created successfully'],
                    ],
                ],
            ],
            '/todos/{id}' => [
                'get' => [
                    'summary' => 'Get a single todo',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer'],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Todo found'],
                        '404' => ['description' => 'Todo not found'],
                    ],
                ],
                'put' => [
                    'summary' => 'Update a todo',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer'],
                        ],
                    ],
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'title' => ['type' => 'string'],
                                        'description' => ['type' => 'string', 'nullable' => true],
                                        'is_completed' => ['type' => 'boolean'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Todo updated successfully'],
                    ],
                ],
                'delete' => [
                    'summary' => 'Delete a todo',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer'],
                        ],
                    ],
                    'responses' => [
                        '204' => ['description' => 'Todo deleted successfully'],
                        '404' => ['description' => 'Todo not found'],
                    ],
                ],
            ],
        ],
    ];
    header('Content-Type: application/json');
    echo json_encode($spec, JSON_PRETTY_PRINT);
});
