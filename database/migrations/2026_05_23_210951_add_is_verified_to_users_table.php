<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('role');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            $table->text('verification_note')->nullable()->after('verified_at');
            // Data yang diinput penggalang saat minta verifikasi
            $table->string('verify_full_name')->nullable()->after('verification_note');
            $table->string('verify_ktp_number', 16)->nullable()->after('verify_full_name');
            $table->enum('verification_status', ['none','pending','approved','rejected'])->default('none')->after('verify_ktp_number');
            $table->text('rejection_reason')->nullable()->after('verification_status');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_verified','verified_at','verification_note','verify_full_name','verify_ktp_number','verification_status','rejection_reason']);
        });
    }
};
