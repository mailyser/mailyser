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
        
        Schema::create('newsletter_spam_score', function (Blueprint $table) {
                $table->id();
                $table->foreignId('newsletter_id')->constrained()->cascadeOnDelete();
                $table->longText("full_content");
                $table->float("spam_score")->nullable();
                $table->text("spam_report")->nullable();
                $table->integer('is_processed')->nullable()->default(0);
                
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
