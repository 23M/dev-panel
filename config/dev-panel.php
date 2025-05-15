<?php

return [
    'path' => 'dev',

    'auth_middleware' => [
        \Filament\Http\Middleware\Authenticate::class,
    ],

    'models' => [
        app_path('Models') => 'App\\Models',
    ],

    'base_model_class' => Illuminate\Database\Eloquent\Model::class,

    'field_strategies' => [],

    'column_strategies' => [],
];
