<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGasCardOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gas_card_orders', function(Blueprint $table)
		{
			$table->increments('id')->comment('车辆油卡自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('gas_card_order_uuid', 32)->nullable();
			$table->char('truck_uuid', 32)->nullable()->comment('车辆UUID');
			$table->char('gas_card_no', 32)->nullable()->comment('油卡卡号');
			$table->integer('total_price')->nullable()->comment('总金额');
			$table->boolean('status')->nullable()->comment('状态');
			$table->char('approver_uuid', 32)->nullable()->comment('审批人UUID');
			$table->string('approver_name', 16)->nullable()->comment('审批人姓名');
			$table->dateTime('approver_time')->nullable()->comment('审批人时间');
			$table->string('approver_reason')->nullable()->comment('审批原因');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gas_card_orders');
	}

}
