<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTruckApprovalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('truck_approvals', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('truck_uuid', 32)->nullable()->comment('车辆UUID');
			$table->char('driver_uuid', 32)->nullable()->comment('司机UUID');
			$table->char('order_uuid', 32)->nullable()->comment('订单UUID');
			$table->boolean('type')->nullable()->comment('申请类型');
			$table->string('type_name')->nullable()->comment('申请类型的名称');
			$table->string('description')->nullable()->comment('申请描述');
			$table->text('images', 65535)->nullable()->comment('图片证明');
			$table->string('remark')->nullable()->comment('备注');
			$table->boolean('status')->nullable()->comment('状态');
			$table->char('approver_uuid', 32)->nullable()->comment('审批人UUID');
			$table->char('approver_name', 16)->nullable()->comment('审批人姓名');
			$table->dateTime('approver_time')->nullable()->comment('审批时间');
			$table->string('approver_reason')->nullable()->comment('审批原因');
			$table->text('contents', 65535)->nullable()->comment('申请内容');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('truck_approvals');
	}

}
