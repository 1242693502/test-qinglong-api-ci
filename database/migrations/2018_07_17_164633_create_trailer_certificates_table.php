<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrailerCertificatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trailer_certificates', function(Blueprint $table)
		{
			$table->increments('id')->comment('挂车证件照自增ID');
			$table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最后更新时间');
			$table->dateTime('delete_time')->nullable()->comment('删除时间');
			$table->string('memo', 128)->nullable()->default('')->comment('备注');
			$table->char('trailer_uuid', 32)->nullable()->comment('挂车UUID');
			$table->char('code', 3)->nullable()->comment('类型编码');
			$table->char('name', 6)->nullable()->comment('类型名称');
			$table->char('image', 32)->nullable()->comment('图片地址');
			$table->char('number', 32)->nullable()->comment('对应的主体的编码');
			$table->text('all_field', 65535)->nullable()->comment('全部字段json保存');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('trailer_certificates');
	}

}
