<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('drivers', function(Blueprint $table)
		{
			$table->increments('id')->comment('司机自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('driver_uuid', 32)->nullable()->unique('driver_uuid')->comment('司机UUID');
			$table->char('name', 16)->nullable()->comment('姓名');
			$table->char('job_number', 32)->nullable()->comment('工号');
			$table->char('phone', 11)->nullable()->comment('手机号码');
			$table->boolean('phone_is_auth')->nullable()->comment('手机号码是否已认证');
			$table->char('id_number', 18)->nullable()->comment('身份证号');
			$table->boolean('id_number_is_auth')->nullable()->comment('身份证是否认证');
			$table->char('driver_license_type', 2)->nullable()->comment('驾照类型');
			$table->char('qualification', 32)->nullable()->comment('从业资格证号码');
			$table->char('contact_address_code', 10)->nullable()->comment('联系地址镇/街道级编码');
			$table->string('contact_address_name')->nullable()->comment('联系地址镇级/街道级全称');
			$table->string('contact_address')->nullable()->comment('联系地址');
			$table->boolean('audit_status')->nullable()->default(0)->comment('审核状态');
			$table->dateTime('open_account_time')->nullable()->comment('开户日期');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('drivers');
	}

}
