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
            $table->uuid('id')->primary();  // Change from uuid to id
            $table->uuid('user_id');  // Change from uuid to id  // Change to foreignId
            $table->string('label');
            $table->text('image')->nullable();
            $table->text('location')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('detected_at');
            $table->uuid('created_by');  // Change to foreignId
            $table->uuid('updated_by');
            $table->foreign('created_by')->references('id')->on('users');  // Change to foreignId
            $table->foreign('updated_by')->references('id')->on('users');  // Change to foreignId
            $table->foreign('user_id')->references('id')->on('users');  // Change to foreignId
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
