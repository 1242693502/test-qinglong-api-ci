<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrucksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trucks', function(Blueprint $table)
		{
			$table->increments('id')->comment('车辆自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('truck_uuid', 32)->nullable()->default('')->unique('truck_uuid')->comment('车辆UUID');
			$table->char('license_plate_number', 8)->nullable()->comment('车牌号码');
			$table->boolean('belong_type')->nullable()->comment('归属类型');
			$table->string('brand', 12)->nullable()->comment('车辆品牌');
			$table->char('engine_number', 32)->nullable()->comment('发动机号码');
			$table->boolean('axle_number')->nullable()->default(0)->comment('车轴数');
			$table->char('type_code', 3)->nullable()->comment('车型编码');
			$table->string('type_name', 8)->nullable()->comment('车型名称');
			$table->char('length_code', 4)->nullable()->comment('车长编码');
			$table->string('length_name', 6)->nullable()->comment('车长名称');
			$table->boolean('truck_status')->nullable()->comment('车辆状态');
			$table->boolean('is_available')->nullable()->default(1)->comment('是否可用');
			$table->char('vin', 17)->nullable()->comment('车架号');
			$table->string('owner_name', 16)->nullable()->comment('车辆所有者');
			$table->char('body_color', 3)->nullable()->comment('车身颜色');
			$table->integer('approved_tonnage')->unsigned()->nullable()->comment('驾驶证上的准牵引货物吨位（单位：KG）');
			$table->integer('actual_tonnage')->unsigned()->nullable()->comment('实际运营中可以运输的货物吨位（单位：KG）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('trucks');
	}

}
