<?php

/**
 * Laravel IDE Helper Generator
 */

namespace Mikedevs\OctoberIdeHelper;

use Mikedevs\OctoberIdeHelper\Console\EloquentCommand;
use Mikedevs\OctoberIdeHelper\Console\GeneratorCommand;
use Mikedevs\OctoberIdeHelper\Console\MetaCommand;
use Mikedevs\OctoberIdeHelper\Console\ModelsCommand;
use Mikedevs\OctoberIdeHelper\Listeners\GenerateModelHelper;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class IdeHelperServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->runningUnitTests() && $this->app['config']->get('october-ide-helper.post_migrate', [])) {
            $this->app['events']->listen(CommandFinished::class, GenerateModelHelper::class);
            $this->app['events']->listen(MigrationsEnded::class, function () {
                GenerateModelHelper::$shouldRun = true;
            });
        }

        if ($this->app->has('view')) {
            $viewPath = __DIR__ . '/../resources/views';
            $this->loadViewsFrom($viewPath, 'october-ide-helper');
        }

        $configPath = __DIR__ . '/../config/october-ide-helper.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('october-ide-helper.php');
        } else {
            $publishPath = base_path('config/october-ide-helper.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/october-ide-helper.php';
        $this->mergeConfigFrom($configPath, 'october-ide-helper');
        $localViewFactory = $this->createLocalViewFactory();

        $this->app->singleton(
            'command.october-ide-helper.generate',
            function ($app) use ($localViewFactory) {
                return new GeneratorCommand($app['config'], $app['files'], $localViewFactory);
            }
        );

        $this->app->singleton(
            'command.october-ide-helper.models',
            function ($app) {
                return new ModelsCommand($app['files']);
            }
        );

        $this->app->singleton(
            'command.october-ide-helper.meta',
            function ($app) use ($localViewFactory) {
                return new MetaCommand($app['files'], $localViewFactory, $app['config']);
            }
        );

        $this->app->singleton(
            'command.october-ide-helper.eloquent',
            function ($app) {
                return new EloquentCommand($app['files']);
            }
        );

        $this->commands(
            'command.october-ide-helper.generate',
            'command.october-ide-helper.models',
            'command.october-ide-helper.meta',
            'command.october-ide-helper.eloquent'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.october-ide-helper.generate', 'command.october-ide-helper.models'];
    }

    /**
     * @return Factory
     */
    private function createLocalViewFactory()
    {
        $resolver = new EngineResolver();
        $resolver->register('php', function () {
            return new PhpEngine($this->app['files']);
        });
        $finder = new FileViewFinder($this->app['files'], [__DIR__ . '/../resources/views']);
        $factory = new Factory($resolver, $finder, $this->app['events']);
        $factory->addExtension('php', 'php');

        return $factory;
    }
}
