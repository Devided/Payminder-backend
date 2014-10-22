<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymindersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payminders', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('sender_name');
            $table->string('sender_iban');
            $table->integer('start_time');
            $table->integer('end_time');
            $table->string('ip_address');
            $table->string('geo');
            $table->string('description');
            $table->string('hash');
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
		Schema::drop('payminders');
	}

}
