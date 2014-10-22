<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateFriendsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('friends', function(Blueprint $table)
		{
			$table->string('amount');
            $table->string('ip_address');
            $table->string('geo');
            $table->boolean('paid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('friends', function(Blueprint $table)
		{
			$table->dropColumn('amount');
            $table->dropColumn('ip_address');
            $table->dropColumn('geo');
            $table->dropColumn('paid');
        });
	}

}
