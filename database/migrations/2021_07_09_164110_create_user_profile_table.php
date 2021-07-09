<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profile', function (Blueprint $table) {
            $table->id();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->string('is_smoking')->nullable();
            $table->string('is_alcohol')->nullable();
            $table->string('is_diet')->nullable();
            $table->string('last_medical_checkup')->nullable();
            $table->string('antibiotics')->nullable();
            $table->string('blood_presure')->nullable();
            $table->string('antacid')->nullable();
            $table->string('hormone_therapy')->nullable();
            $table->string('anti_asthma')->nullable();
            $table->string('arpirin')->nullable();
            $table->string('diet_pill')->nullable();
            $table->string('supplement')->nullable();
            $table->string('herbal_product')->nullable();
            $table->longText('exercise_level')->nullable();
            $table->longText('is_any_disease')->nullable();
            $table->longText('user_id');
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
        Schema::dropIfExists('user_profile');
    }
}
