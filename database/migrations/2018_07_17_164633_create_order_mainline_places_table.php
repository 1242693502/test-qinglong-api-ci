<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderMainlinePlacesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_mainline_places', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('place_uuid', 32)->nullable()->comment('地址UUID');
			$table->char('order_uuid', 32)->nullable()->comment('订单UUID');
			$table->boolean('type')->nullable()->comment('地址类型( 1: 装货地 2：卸货地)');
			$table->char('address_contact_name', 8)->nullable()->comment('联系人姓名');
			$table->char('address_contact_phone', 16)->nullable()->comment('联系人手机号码');
			$table->char('area_code', 10)->nullable()->comment('最后一级地址编码');
			$table->string('area_name')->nullable()->comment('最后一级地址名称（只包含 省市区街道部分）');
			$table->string('address', 256)->nullable()->comment('详细地址（不包括省市区街道）');
			$table->dateTime('arrival_time')->nullable()->comment('到达时间');
			$table->dateTime('departure_time')->nullable()->comment('离开时间');
			$table->dateTime('count_begin_time')->nullable()->comment('（装/卸货）计时开始');
			$table->dateTime('count_end_time')->nullable()->comment('（装/卸货)计时结束');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_mainline_places');
	}

}
