<?php

namespace QingLong\Platform\GasCard;

use \JsonRPC\Client;
use Urland\Exceptions\Client\BadRequestException;

class GasCard
{
    protected $client = null;

    /**
     * 设置RPC URL
     *
     * @param string $path
     *
     * @return Client
     */
    public function setClient($path)
    {
        $baseUrl      = env('GAS_RPC_URL');
        $client       = new Client($baseUrl . '/' . $path);
        $this->client = $client;
        return $client;
    }

    /**
     * 设置RPC请求方法
     *
     * @param string $function 请求方法
     * @param array  $param    请求参数
     *
     * @return mixed
     * @throws BadRequestException
     */
    public function execute($function = '', $param = [])
    {
        $userInfo = [
            'u_id'      => -1,
            'u_name'    => 'nobody',
            'from'      => 'undefind',
            'app_id'    => (int)env('GAS_APP_ID'),
            'client_ip' => request()->ip(),
        ];

        $reqattrs = [
            'user_info' => $userInfo
        ];
        $result   = $this->client->execute($function, $param, $reqattrs);
        list($info, $code, $message) = $result;
        if ($code) {
            throw new BadRequestException($message);
        }
        return $info;
    }
}