<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTruckRefuelLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('truck_refuel_logs', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('truck_uuid', 32)->nullable()->comment('车辆UUID');
			$table->char('driver_uuid', 32)->nullable()->comment('司机UUID');
			$table->char('order_uuid', 32)->nullable()->comment('订单UUID');
			$table->integer('filling_station_id')->nullable()->comment('加油站主键ID');
			$table->integer('per_price')->nullable()->comment('单价（分）');
			$table->integer('liter_number')->nullable()->comment('加油量（毫升）');
			$table->integer('total_price')->nullable()->comment('费用总价(分)');
			$table->boolean('pay_type_id')->nullable()->comment('付款方式');
			$table->char('gas_card_no', 32)->nullable()->comment('油卡卡号');
			$table->integer('current_mileage')->nullable()->comment('当前里程（米）');
			$table->char('current_mileage_image', 32)->nullable()->comment('里程照片');
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
		Schema::drop('truck_refuel_logs');
	}

}
