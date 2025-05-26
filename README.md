# 23M Dev Panel

This panel creates reads the models of a Laravel application and creates an interactive panel to view and edit them.

It is in a very early stage of development and is not yet ready for production use.

## Installation

Since the package is not yet published, you can install it via composer directly from the repository:

```bash
composer require 23m/dev-panel:dev-main
```

Then you can publish the assets and configuration file:

```bash
php artisan vendor:publish --provider="DevPanel\DevPanelServiceProvider"
```

## Configuration

You can configure the package by editing the `config/dev-panel.php` file. 

```php
'path' => 'dev',
```
With this configuration, the panel will be available at `http://your-app.dev/dev`.

```php
'auth_middleware' => [
    \Filament\Http\Middleware\Authenticate::class,
],
```
You can add your own authentication middleware to protect the panel. By default, it uses the `Filament\Http\Middleware\Authenticate` middleware, which requires you to be logged in as a Filament user.

```php
'models' => [
    app_path('Models') => 'App\\Models',
],
```
You can configure the models that should be available in the panel. The key is the path to the models directory, and the value is the namespace of the models. You can add multiple directories and namespaces.

```php
'base_model_class' => Illuminate\Database\Eloquent\Model::class
```
You can configure the base model class that the panel will use to read the models. By default, it uses `Illuminate\Database\Eloquent\Model`. Only Models that extend this class will be available in the panel.

```php
'field_strategies' => []
```
You can add custom field strategies to the panel. Classes given here must extend `DevPanel\FieldStrategies\FieldStrategy`.

```php
'column_strategies' => []
```
You can add custom column strategies to the panel. Classes given here must extend `DevPanel\ColumnStrategies\ColumnStrategy`.

## Usage
To generate the panel, you can run the following command:

```bash
php artisan dev-panel:generate
```
This will read the models from the configured directories and generate the panel. The panel will be available at the configured path (default is `/dev`).
