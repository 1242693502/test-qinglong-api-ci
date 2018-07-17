<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTruckStatusesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('truck_statuses', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('truck_uuid', 32)->nullable()->comment('车辆UUID');
			$table->boolean('truck_status')->nullable()->comment('车辆状态');
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
		Schema::drop('truck_statuses');
	}

}
