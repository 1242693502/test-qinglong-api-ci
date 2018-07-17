<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_users', function(Blueprint $table)
		{
			$table->increments('id')->comment('主键ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->char('api_key', 16)->nullable()->comment('业务系统key');
			$table->char('api_secret', 32)->nullable()->comment('业务系统用secret');
			$table->char('name', 16)->nullable()->comment('业务系统标识，唯一');
			$table->string('nickname', 32)->nullable()->comment('昵称');
			$table->string('description')->nullable()->comment('描述');
			$table->boolean('status')->nullable()->comment('状态（0：未激活 1：正常 2：禁用）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('api_users');
	}

}
