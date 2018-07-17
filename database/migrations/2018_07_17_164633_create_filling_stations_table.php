<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFillingStationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('filling_stations', function(Blueprint $table)
		{
			$table->increments('id')->comment('加油站自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->string('name')->nullable()->comment('加油站名称');
			$table->char('area_code', 10)->nullable()->comment('最后一级地址编码');
			$table->string('area_name')->nullable()->comment('最后一级地址名称（只包含 省市区街道部分）');
			$table->string('address')->nullable()->comment('详细地址（不包括省市区街道）');
			$table->decimal('longitude', 10, 7)->nullable()->comment('位置经度');
			$table->decimal('latitude', 10, 7)->nullable()->comment('位置维度');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('filling_stations');
	}

}
