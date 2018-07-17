<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTruckGasCardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('truck_gas_cards', function(Blueprint $table)
		{
			$table->increments('id')->comment('车辆油卡自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('truck_uuid', 32)->nullable()->comment('车辆UUID');
			$table->char('gas_card_no', 32)->nullable()->comment('油卡卡号');
			$table->dateTime('bind_time')->nullable()->comment('绑定时间');
			$table->char('channel', 16)->nullable()->comment('渠道商');
			$table->boolean('status')->nullable()->comment('状态');
			$table->dateTime('unbind_time')->nullable()->comment('解绑时间');
			$table->string('unbind_reason')->nullable()->comment('解绑原因');
			$table->dateTime('loss_time')->nullable()->comment('挂失时间');
			$table->string('loss_reason')->nullable()->comment('挂失原因');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('truck_gas_cards');
	}

}
