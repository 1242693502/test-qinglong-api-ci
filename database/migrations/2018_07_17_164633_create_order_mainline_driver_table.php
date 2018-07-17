<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderMainlineDriverTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_mainline_driver', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('order_uuid', 32)->nullable()->comment('订单UUID');
			$table->char('driver_uuid', 32)->nullable()->comment('司机UUID');
			$table->char('driver_name', 16)->nullable()->comment('司机名称');
			$table->char('driver_phone', 11)->nullable()->comment('司机手机号码');
			$table->boolean('type')->nullable()->default(0)->comment('类型(1：接单的驾驶员 2: 随车驾驶员 )');
			$table->boolean('status')->nullable()->default(0)->comment('状态');
			$table->string('note')->nullable()->comment('补充说明');
			$table->dateTime('confirm_time')->nullable()->comment('接单时间');
			$table->dateTime('complete_time')->nullable()->comment('完成时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_mainline_driver');
	}

}
