<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;
use QingLong\Validator\Rules as UrlandValidatorRules;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 设置日期的语言
        Carbon::setLocale('zh');
        CarbonInterval::setLocale('zh');

        // 记录sql语句到日志
        $this->databaseDebug();

        // 扩展验证规则
        UrlandValidatorRules::addTo($this->app->make('validator'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 开发用ide helper
        $ideHelperClass = \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class;
        if ($this->app->environment() !== 'production' && class_exists($ideHelperClass)) {
            $this->app->register($ideHelperClass);
        }
    }

    /**
     * 记录sql语句到日志
     */
    protected function databaseDebug()
    {
        if (!config('database.debug')) {
            return;
        }

        \DB::listen(function (QueryExecuted $event) {
            // Format binding data for sql insertion
            $bindings = $event->bindings;
            foreach ($event->bindings as $i => $binding) {
                if ($binding instanceof \DateTime) {
                    $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } elseif (is_string($binding)) {
                    $bindings[$i] = "'$binding'";
                }
            }

            // Insert bindings into query
            $query      = str_replace(['%', '?'], ['%%', '%s'], $event->sql);
            $finalQuery = vsprintf($query, $bindings);

            \Log::debug($finalQuery);
        });
    }
}
