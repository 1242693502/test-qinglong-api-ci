<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('to_uuid', 32)->nullable()->comment('接收者UUID');
			$table->boolean('to_type')->nullable()->comment('接收者类型');
			$table->string('to_name', 64)->nullable()->comment('接收者名称');
			$table->char('from_uuid', 32)->nullable()->comment('来源对象UUID');
			$table->boolean('from_type')->nullable()->comment('来源类型(1: 系统 2：司机 3：车队长)');
			$table->string('from_name', 64)->nullable()->comment('来源用户名称');
			$table->boolean('type')->nullable()->comment('通知类型');
			$table->string('title')->nullable()->comment('标题');
			$table->string('description', 1024)->nullable()->comment('描述');
			$table->boolean('status')->nullable()->comment('通知状态（1：新通知 2：已读 ）');
			$table->text('contents', 65535)->nullable()->comment('内容（json）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('notifications');
	}

}
