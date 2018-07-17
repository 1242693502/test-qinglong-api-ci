<?php

namespace App\Http\Controllers\InternalApi\GasCard;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\GasCard\IndexRequest;
use App\Http\Resources\InternalApi\GasCard\GasCardResource;
use Illuminate\Pagination\LengthAwarePaginator;
use QingLong\Platform\GasCard\GasCard;

class GasCardController extends BaseController
{
    /**
     * 获取油卡库存列表
     *
     * @param IndexRequest $request
     *
     * @return \App\Http\Resources\InternalApi\GasCard\GasCardResource[]
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function index(IndexRequest $request)
    {
        $inputs  = $request->validated();
        $gasCard = new GasCard();
        $gasCard->setClient('GasCard/GasCard');
        $where = ['gc_src_code' => env('GAS_APP_ID')];
        if (!empty($inputs['gas_card_no'])) {
            $where['gc_card_no'] = $inputs['gas_card_no'];
        }
        $page     = isset($inputs['page']) ? $inputs['page'] : 1;
        $limit    = 20;
        $response = $gasCard->execute('getListByWhere', [
            $where,
            $page,
            $limit,
            [
                'gc_card_no',
                'gc_prov_name',
                'gc_city_name',
                'gc_com_code',
                'gc_status',
                't_sid',
                't_front_plate',
            ],
        ]);
        return GasCardResource::collection(new LengthAwarePaginator($response['list'], $response['page']['count'],
            $response['page']['limit'], $response['page']['cur_page']));
    }
}