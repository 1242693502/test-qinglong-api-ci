<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTruckLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('truck_logs', function(Blueprint $table)
		{
			$table->increments('id')->comment('日志自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('truck_log_uuid', 32)->nullable()->comment('车辆日志UUID');
			$table->char('truck_uuid', 32)->nullable()->comment('车辆UUID');
			$table->char('driver_uuid', 32)->nullable()->comment('司机UUID');
			$table->char('order_uuid', 32)->nullable()->comment('订单UUID');
			$table->dateTime('reg_time')->nullable()->comment('登记时间');
			$table->boolean('type')->nullable()->comment('日志类型');
			$table->string('title', 64)->nullable()->comment('日志标题');
			$table->string('description')->nullable()->comment('日志详细描述');
			$table->text('images', 65535)->nullable()->comment('图片内容（json）');
			$table->boolean('status')->nullable()->comment('日志状态');
			$table->string('remark', 128)->nullable()->comment('备注');
			$table->integer('current_mileage')->nullable()->comment('当前里程（米）');
			$table->char('current_mileage_image', 32)->nullable()->comment('里程照片');
			$table->decimal('longitude', 10, 7)->nullable()->comment('位置经度');
			$table->decimal('latitude', 10, 7)->nullable()->comment('位置维度');
			$table->text('contents', 65535)->nullable()->comment('日志内容（json）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('truck_logs');
	}

}
