<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();

            $table->date('loan_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('returned_at')->nullable();

            $table->string('status')->default('menunggu');
            $table->unsignedInteger('fine')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};