<?php

namespace Tests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    static $firstStart = true;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->checkAppEnv();

        $this->migrateFresh($app);

        Hash::driver('bcrypt')->setRounds(4);

        return $app;
    }

    private function migrateFresh($app)
    {
        if (self::$firstStart) {
            $app[Kernel::class]->call('migrate:fresh');
            self::$firstStart = false;
        }
    }

    private function checkAppEnv()
    {
        if (env('APP_ENV') !== 'testing') {
            dd('环境变量:APP_ENV 为非测试环境，请指定配置文件 phpunit.xml 后再使用');
        }
    }
}
