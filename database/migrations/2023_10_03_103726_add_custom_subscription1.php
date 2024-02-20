<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SubscriptionPlan;

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
        
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string("app_sumo_tier_id")->nullable()->index();
        });
        
        $subscriptionPlan = SubscriptionPlan::where("stripe_id", 'price_1N42HrGU0H08SQCLRmXUGBMs')->first();
        if($subscriptionPlan) {
            $subscriptionPlan->app_sumo_tier_id = 'mailyser_tier1';
            $subscriptionPlan->save();
        }
        
        $subscriptionPlan = SubscriptionPlan::where("stripe_id", 'tier_2')->first();
        if($subscriptionPlan) {
            $subscriptionPlan->app_sumo_tier_id = 'mailyser_tier2';
            $subscriptionPlan->save();
        }
        
        $subscriptionPlan = SubscriptionPlan::where("stripe_id", 'tier_3')->first();
        if($subscriptionPlan) {
            $subscriptionPlan->app_sumo_tier_id = 'mailyser_tier3';
            $subscriptionPlan->save();
        }
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
