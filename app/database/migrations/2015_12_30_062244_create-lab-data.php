<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('results');
		Schema::dropIfExists('experiments');
		Schema::dropIfExists('users');
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('user_id');
			$table->string('device_id',100);
			$table->timestamps();
		});

		Schema::create('experiments', function(Blueprint $table)
		{
			$table->increments('exp_id');
			$table->string('title', 200);
			$table->longtext('specifications');
			$table->timestamps();
		});

		Schema::create('results', function(Blueprint $table){
			$table->increments('result_id');
			$table->integer('exp_id')->unsigned();
			$table->foreign('exp_id')->references('exp_id')->on('experiments')->onDelete('cascade');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
			$table->longtext('data');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
