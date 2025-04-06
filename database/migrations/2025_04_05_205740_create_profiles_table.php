<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique()->index();
            $table->string('name')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedBigInteger('likes')->default(0)->index();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->timestamp('last_scraped_at')->nullable();

            $table->fullText(['username', 'name', 'bio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
