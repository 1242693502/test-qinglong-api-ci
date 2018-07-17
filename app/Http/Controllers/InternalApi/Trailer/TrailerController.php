<?php

namespace App\Http\Controllers\InternalApi\Trailer;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Trailer;
use App\Http\Resources\InternalApi\Trailer\TrailerResource;
use App\Services\Trailer\TrailerService;

/**
 * Class TrailerController
 *
 * @package App\Http\Controllers\InternalApi\Truck
 */
class TrailerController extends BaseController
{

    /**
     * 创建挂车
     *
     * @param \App\Http\Requests\InternalApi\Trailer\CreateRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Trailer\TrailerResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     */
    public function store(Trailer\CreateRequest $request)
    {
        $trailer = (new TrailerService())->create($request->validated());

        return new TrailerResource($trailer);
    }

    /**
     * 更新挂车
     *
     * @param \App\Http\Requests\InternalApi\Trailer\UpdateRequest $request
     * @param string $trailerUUID
     *
     * @return \App\Http\Resources\InternalApi\Trailer\TrailerResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function update(Trailer\UpdateRequest $request, $trailerUUID)
    {
        $trailer = (new TrailerService())->update($trailerUUID, $request->validated());

        return new TrailerResource($trailer);
    }
}
