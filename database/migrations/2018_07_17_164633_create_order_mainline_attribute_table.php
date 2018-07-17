<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderMainlineAttributeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_mainline_attribute', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('order_uuid', 32)->nullable()->comment('订单UUID');
			$table->char('contract_no', 32)->nullable()->comment('随车合同编号');
			$table->char('contract_image', 64)->nullable()->comment('随车合同编号照片');
			$table->string('receipt_images', 1024)->nullable()->comment('单据照片(最多30张照片)');
			$table->char('receipt_statuses', 64)->nullable()->comment('单据状态');
			$table->char('seal_first_no', 32)->nullable()->comment('封签号边门1');
			$table->char('seal_first_image', 64)->nullable()->comment('封签号边门1 照片（最多一张）');
			$table->char('seal_second_no', 32)->nullable()->comment('封签号 边门2');
			$table->char('seal_second_image', 64)->nullable()->comment('封签号边门2 照片（最多一张）');
			$table->char('seal_last_no', 32)->nullable()->comment('封签号 尾门');
			$table->char('seal_last_image', 64)->nullable()->comment('封签号 尾门照片（最多一张）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_mainline_attribute');
	}

}
