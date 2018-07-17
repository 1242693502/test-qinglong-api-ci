<?php

namespace QingLong\Action;

use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application urland constant.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // 注册action
        $this->app->singleton('ql.action', function ($app) {
            return new Factory();
        });

        // 添加解析来源
        $this->app->make('ql.action')->setResolver(function () {
            return $this->app->make('file-db')->load('order.mainline.actions');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ql.action'];
    }
}