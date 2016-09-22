<?php

namespace Sarav\Admin\Providers;

use Illuminate\Support\ServiceProvider;
use Sarav\Admin\Console\AdminInitializeCommand;
use Sarav\Admin\Console\ModuleMakeCommand;

class AdminServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAdminModuleCommand();
    }

    /**
     * Publishes admin configuration
     * 
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/admin.php');

        $this->publishes([$source => config_path('admin.php')]);
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerAdminModuleCommand()
    {
        $this->app->singleton('command.module.make', function ($app) {
            return new ModuleMakeCommand($app['files']);
        });

        $this->app->singleton('command.admin.initialize', function ($app) {
            return new AdminInitializeCommand($app['files']);
        });

        $this->commands(['command.module.make']);
        $this->commands(['command.admin.initialize']);

        $this->commands('Sarav\Admin\Console\Generators\ModelMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\ControllerMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\ProviderMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\RouteServiceProviderMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\InterfaceMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\RepositoryMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\RouteMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\RequestMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\ViewMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\BreadcrumbMakeCommand');
        $this->commands('Sarav\Admin\Console\Generators\TestMakeCommand');
    }
}
