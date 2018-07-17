<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderMainlinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_mainlines', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('order_uuid', 32)->nullable()->comment('订单UUID');
			$table->char('out_trade_no', 32)->nullable()->comment('外部订单编号');
			$table->char('contract_no', 32)->nullable()->comment('合同编号');
			$table->char('shipper_name', 32)->nullable()->comment('发货方名称');
			$table->char('shipper_user_name', 32)->nullable()->comment('发货方联系人姓名');
			$table->char('shipper_user_phone', 16)->nullable()->comment('发货方联系人电话');
			$table->char('origin_city_code', 10)->nullable()->comment('线路起点城市标识');
			$table->char('origin_city_name', 32)->nullable()->comment('线路起点城市名称');
			$table->char('destination_city_code', 10)->nullable()->comment('线路终点城市标识');
			$table->char('destination_city_name', 32)->nullable()->comment('线路终点城市名称');
			$table->char('transport_no', 32)->nullable()->default('')->comment('运输批次');
			$table->char('goods_name', 32)->nullable()->comment('货物名称');
			$table->integer('goods_weight_appointment')->nullable()->comment('预约货物重量(千克 kg)');
			$table->integer('goods_volume_appointment')->nullable()->comment('预约货物体积（立方分米 dm^3）');
			$table->string('order_notes')->nullable()->comment('订单留言-补充说明');
			$table->dateTime('order_time')->nullable()->comment('下单时间');
			$table->dateTime('departure_time_appointment')->nullable()->comment('预约发车时间');
			$table->char('truck_plate_appointment', 8)->nullable()->comment('预约车辆车牌');
			$table->char('trailer_plate_appointment', 8)->nullable()->comment('预约挂车车牌');
			$table->integer('order_status')->nullable()->comment('订单状态');
			$table->integer('goods_weight')->nullable()->comment('实际货物重量');
			$table->integer('goods_volume')->nullable()->comment('实际货物体积');
			$table->char('truck_uuid', 32)->nullable()->comment('货车UUID');
			$table->char('trailer_uuid', 32)->nullable()->comment('挂车UUID');
			$table->char('truck_plate', 8)->nullable()->comment('货车车牌');
			$table->char('trailer_plate', 8)->nullable()->comment('挂车车牌');
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
		Schema::drop('order_mainlines');
	}

}
