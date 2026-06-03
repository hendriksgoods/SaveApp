<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');           
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', [
                         'Pendidikan',
                         'Kesehatan',
                         'Bencana Alam',
                         'Alam & Lingkungan',
                         'Kebutuhan Sehari-hari',
            ]);
            $table->text('description');            // short summary
            $table->longText('story');              // full story
            $table->decimal('target_amount', 15, 2);
            $table->decimal('raised_amount', 15, 2)->default(0);
            $table->unsignedInteger('duration_days');
            $table->date('deadline');
            $table->string('image')->nullable();
   //         $table->string('bank_name');
  //          $table->string('account_number', 20);
  //          $table->string('account_name');
            $table->enum('status', ['pending', 'active', 'completed', 'rejected'])->default('pending');
            $table->boolean('is_urgent')->default(false);
            $table->timestamps();

            $table->index(['status', 'category']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
