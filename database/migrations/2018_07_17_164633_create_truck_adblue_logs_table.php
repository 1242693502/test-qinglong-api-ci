<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTruckAdblueLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('truck_adblue_logs', function(Blueprint $table)
		{
			$table->increments('id')->comment('车辆领用尿素自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('truck_log_uuid', 32)->nullable()->comment('车辆日志UUID');
			$table->char('truck_uuid', 32)->nullable()->comment('车辆UUID');
			$table->char('driver_uuid', 32)->nullable()->comment('司机UUID');
			$table->char('order_uuid', 32)->nullable()->comment('订单UUID');
			$table->integer('liter_number')->nullable()->comment('升数（单位：毫升）');
			$table->dateTime('reg_time')->nullable()->comment('登记时间');
			$table->boolean('status')->nullable()->comment('状态');
			$table->string('images', 256)->nullable()->comment('照片证明');
			$table->boolean('has_invoice')->nullable()->comment('有无发票');
			$table->char('merchant_name', 32)->nullable()->comment('商户名称');
			$table->decimal('longitude', 10, 7)->nullable()->comment('位置经度');
			$table->decimal('latitude', 10, 7)->nullable()->comment('位置维度');
			$table->string('remark', 128)->nullable()->comment('备注');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('truck_adblue_logs');
	}

}
