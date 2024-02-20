<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('app_sumo_activation', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique('activation_email');
            $table->string('plan_id')->nullable();
            $table->string('uuid')->nullable();
            $table->string('invoice_item_uuid')->nullable();
            
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
};
