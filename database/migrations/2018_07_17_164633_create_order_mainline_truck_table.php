<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderMainlineTruckTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_mainline_truck', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('order_uuid', 32)->comment('订单UUID');
			$table->char('truck_uuid', 32)->comment('车辆UUID');
			$table->char('truck_plate', 8)->nullable()->comment('货车车牌');
			$table->boolean('status')->nullable()->comment('状态');
			$table->string('note')->nullable()->comment('补充说明');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_mainline_truck');
	}

}
