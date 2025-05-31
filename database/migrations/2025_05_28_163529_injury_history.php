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
        Schema::create('InjuryHistory', function (Blueprint $table) {
            $table->id();  // Change from uuid to id
            $table->foreignId('user_id')->constrained('users');  // Change to foreignId
            $table->string('label');
            $table->text('image')->nullable();
            $table->text('location')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('detected_at');
            $table->foreignId('created_by')->nullable()->constrained('users');  // Change to foreignId
            $table->foreignId('updated_by')->nullable()->constrained('users');  // Change to foreignId
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
                Schema::dropIfExists('InjuryHistory');

    }
};
