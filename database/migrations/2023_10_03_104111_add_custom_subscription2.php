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
        
        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->name = 'Tier 1 (App Sumo)';
        $subscriptionPlan->stripe_id = 'tier_1';
        $subscriptionPlan->active = 1;
        $subscriptionPlan->trial_days = 0;
        $subscriptionPlan->price = 59;
        $subscriptionPlan->monthly_credits = 10000;
        $subscriptionPlan->no_of_email_tests = 10;
        $subscriptionPlan->no_of_email_domains = 10;
        $subscriptionPlan->is_private = 1;
        $subscriptionPlan->app_sumo_tier_id = 'mailyser_tier1';
        
        $subscriptionPlan->save();
        
        $subscriptionPlan = SubscriptionPlan::where("stripe_id", 'price_1N42HrGU0H08SQCLRmXUGBMs')->first();
        if($subscriptionPlan) {
            $subscriptionPlan->app_sumo_tier_id = '';
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
