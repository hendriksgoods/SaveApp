<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('bank_name');
            $table->string('account_number', 20);
            $table->string('account_name');
            $table->text('description')->nullable();  // keterangan penarikan
            $table->enum('status', ['pending','processed','rejected'])->default('pending');
            $table->text('rejection_note')->nullable();
            $table->timestamp('processed_at')->nullable(); // kapan diproses
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('withdrawals'); }
};
