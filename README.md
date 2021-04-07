# October IDE Helper Generator

**Complete PHPDocs, directly from the source**

This package generates helper files that enable your IDE to provide accurate autocompletion.
Generation is done based on the files in your project, so they are always up-to-date.

- [Installation](#installation)
- [Usage](#usage)
  - [Automatic PHPDoc generation for Laravel Facades](#automatic-phpdoc-generation-for-laravel-facades)
  - [Automatic PHPDocs for models](#automatic-phpdocs-for-models)
    - [Model Directories](#model-directories)
    - [Ignore Models](#ignore-models)
    - [Model Hooks](#model-hooks)
  - [Automatic PHPDocs generation for Laravel Fluent methods](#automatic-phpdocs-generation-for-laravel-fluent-methods)
  - [Auto-completion for factory builders](#auto-completion-for-factory-builders)
  - [PhpStorm Meta for Container instances](#phpstorm-meta-for-container-instances)
- [Usage with Lumen](#usage-with-lumen)
  - [Enabling Facades](#enabling-facades)
  - [Adding the Service Provider](#adding-the-service-provider)
  - [Adding Additional Facades](#adding-additional-facades)
- [License](#license)

## Installation

Require this package with composer using the following command:

```bash
composer require --dev mikedevs/october-ide-helper
```

```
- Add the following class to the `providers` array in `config/app.php`:
  ```php
  Mikedevs\OctoberIdeHelper\IdeHelperServiceProvider::class,
  ```

> Note: Avoid caching the configuration in your development environment, it may cause issues after installing this package; respectively clear the cache beforehand via `php artisan cache:clear` if you encounter problems when running the commands

## Usage

You can re-generate the docs yourself (for future updates)

```bash
php artisan mikedevs:october-ide-helper:models --dir="path/to/plugin/models"
```
/**
 * App\Models\Post
 *
 * @property integer $id
 * @property integer $author_id
 * @property string $title
 * @property string $text
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\Comment[] $comments
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereTitle($value)
 * …
 */