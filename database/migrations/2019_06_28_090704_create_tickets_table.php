<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tickets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ticket_number', 191);
			$table->string('source', 191)->nullable();
			$table->integer('company_id')->nullable();
			$table->string('sender_name', 191)->nullable();
			$table->string('sender_email', 191)->nullable();
			$table->string('title', 191)->nullable();
			$table->text('content')->nullable();
			$table->string('attachment_id', 191)->nullable();
			$table->string('status', 20)->nullable();
			$table->boolean('read_by_admin')->default(0);
			$table->boolean('read_by_client_admin')->default(0);
			$table->boolean('read_by_user')->default(1);
			$table->integer('created_by')->nullable();
			$table->integer('assigned_to')->nullable();
			$table->dateTime('assigned_at')->nullable();
			$table->integer('assigned_by')->nullable();
			$table->dateTime('opened_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tickets');
	}

}
