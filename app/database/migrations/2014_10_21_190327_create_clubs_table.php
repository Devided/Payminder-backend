<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClubsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clubs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('naam');
            $table->string('url');
            $table->string('logo');
            $table->integer('mailchimp');
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
		Schema::drop('clubs');
	}

}
