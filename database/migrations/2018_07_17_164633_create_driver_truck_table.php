<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverTruckTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_truck', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('driver_uuid', 32)->nullable()->comment('司机UUID');
			$table->boolean('is_driving')->nullable()->comment('是否驾驶');
			$table->char('truck_uuid', 32)->nullable()->comment('车辆UUID');
			$table->dateTime('reg_time')->nullable()->comment('登记时间');
			$table->string('remark')->nullable()->comment('备注');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_truck');
	}

}
