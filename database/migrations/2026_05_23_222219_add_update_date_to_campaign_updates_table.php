<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('jejak_kebaikans', function (Blueprint $table) {
            $table->date('update_date')->nullable()->after('image_path');
        });
    }

    public function down(): void {
        Schema::table('jejak_kebaikans', function (Blueprint $table) {
            $table->dropColumn('update_date');
        });
    }
};