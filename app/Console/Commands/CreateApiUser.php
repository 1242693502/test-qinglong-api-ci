<?php

namespace App\Console\Commands;

use App\Models\Api\ApiUser;
use Illuminate\Console\Command;

class CreateApiUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '添加接口调用方用户信息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function handle()
    {
        $name = $this->ask('name 系统名称 (example: order)');
        if (!$this->checkName($name)) {
            return;
        }

        $nickname = $this->ask('nickname 系统中文名称 (example: 订单系统)');
        if (empty($nickname)) {
            $this->warn('请输入nickname');
            return;
        }

        $description = $this->ask('description 系统用途描述 (example: 用于导入订单到青龙系统)');

        $apiUserAttributes = [
            'name'        => $name,
            'nickname'    => $nickname,
            'description' => $description,
            'api_key'     => str_random(16),
            'api_secret'  => str_random(32),
            'status'      => 1,
        ];

        $apiUser = ApiUser::forceCreate($apiUserAttributes);
        if ($apiUser->exists) {
            $apiUserInfo = $apiUserAttributes;

            $this->info('创建成功');
            $this->table(array_keys($apiUserInfo), [$apiUserInfo]);
        } else {
            $this->error('创建失败');
        }
    }


    /**
     * 检查系统名称是否合法
     *
     * @param string $name
     *
     * @return bool
     */
    protected function checkName($name)
    {
        if (empty($name)) {
            $this->warn('请输入系统名称');
            return false;
        }

        // 判断name是否存在
        if (ApiUser::where('name', $name)->exists()) {
            $this->warn('已存在相同的系统名称 ' . $name);
            return false;
        }

        return true;
    }
}
