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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            
            // Todo fields
            $table->string('title');      // Required todo title
            $table->text('description')->nullable();  // Optional description
            $table->boolean('is_completed')->default(false);  // Completion status
            
            // User relationship (nullable for public todos)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
