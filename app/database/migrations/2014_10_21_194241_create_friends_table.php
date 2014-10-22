<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFriendsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('friends', function(Blueprint $table)
		{
			$table->increments('id');
            $table->bigInteger('payminder_id')->unsigned();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phonenumber');
            $table->string('emailaddress');
            $table->string('secondary_phonenumber');

            $table->string('amount');
            $table->string('ip_address');
            $table->string('geo');
            $table->boolean('paid');

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
		Schema::drop('friends');
	}

}
