<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {

    $table->string('full_name_ktp');
    $table->string('ktp_number', 16);
    $table->string('phone', 15);
    $table->string('occupation');

    $table->string('facebook')->nullable();
    $table->string('instagram')->nullable();
    $table->string('twitter')->nullable();

    $table->text('fund_purpose');
    $table->string('location');

    $table->text('fund_detail');

    $table->longText('reason');

    $table->text('rejection_reason')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            //
        });
    }
};
