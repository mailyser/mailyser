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
        Schema::create('email_newsletter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_id')->constrained()->cascadeOnDelete();
            $table->foreignId('newsletter_id')->constrained()->cascadeOnDelete();
            $table->string('status')->nullable();
            $table->string('found_at_mailbox')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_newsletter');
    }
};
