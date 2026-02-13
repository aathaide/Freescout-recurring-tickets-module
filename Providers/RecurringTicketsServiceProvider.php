<?php

namespace Modules\RecurringTickets\Providers;

use Illuminate\Support\ServiceProvider;

class RecurringTicketsServiceProvider extends ServiceProvider
{
    const MODULE_ALIAS = 'recurringtickets';

    protected $moduleName = 'RecurringTickets';
    protected $moduleNameLower = 'recurringtickets';

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');
        $this->registerCommands();

        // Schedule the module command using FreeScout's scheduler hook pattern.
        if (class_exists('\Eventy')) {
            \Eventy::addFilter('schedule', function ($schedule) {
                $schedule->command('freescout:recurringtickets-process')->cron('* * * * *');
                return $schedule;
            });
        }
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path($this->moduleNameLower.'.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', $this->moduleNameLower);
    }

    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge(
            array_map(function ($path) {
                return $path . '/modules/' . $this->moduleNameLower;
            }, \Config::get('view.paths')),
            [$sourcePath]
        ), $this->moduleNameLower);
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\RecurringTickets\Console\ProcessRecurringTickets::class,
            ]);
        }
    }
}
