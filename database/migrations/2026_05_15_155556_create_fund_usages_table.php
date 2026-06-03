<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fund_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('budget', 15, 2)->default(0);   // anggaran
            $table->decimal('used', 15, 2)->default(0);     // terpakai
            $table->date('usage_date')->nullable();
            $table->enum('status', ['ongoing', 'done'])->default('ongoing');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fund_usages'); }
};
