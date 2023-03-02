<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Newsletter;

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
        
        Schema::table('newsletters', function (Blueprint $table) {
            $table->integer('has_mail_tester')->default(1)->index();
        });
        
        Newsletter::where('has_mail_tester', 1)->update(  ['has_mail_tester' => 0] );
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
