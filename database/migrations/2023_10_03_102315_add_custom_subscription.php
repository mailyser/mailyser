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
            $table->integer("is_private")->nullable()->default(0)->index();
            
        });
        
        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->name = 'Tier 2 (App Sumo)';
        $subscriptionPlan->stripe_id = 'tier_2';
        $subscriptionPlan->active = 1;
        $subscriptionPlan->trial_days = 0;
        $subscriptionPlan->price = 119;
        $subscriptionPlan->monthly_credits = 10000;
        $subscriptionPlan->no_of_email_tests = 40;
        $subscriptionPlan->no_of_email_domains = 40;
        $subscriptionPlan->is_private = 1;
        
        $subscriptionPlan->save();
        
        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->name = 'Tier 3 (App Sumo)';
        $subscriptionPlan->stripe_id = 'tier_3';
        $subscriptionPlan->active = 1;
        $subscriptionPlan->trial_days = 0;
        $subscriptionPlan->price = 197;
        $subscriptionPlan->monthly_credits = 10000;
        $subscriptionPlan->no_of_email_tests = 99999;
        $subscriptionPlan->no_of_email_domains = 99999;
        $subscriptionPlan->is_private = 1;
        
        $subscriptionPlan->save();
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
