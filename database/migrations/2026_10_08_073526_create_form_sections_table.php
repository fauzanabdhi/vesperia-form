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
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('external_id')->unique();
            $table->string('name');
            $table->unsignedInteger('position');
            $table->json('definition_json');
            $table->timestamps();

            $table->index(['form_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_sections');
    }
};
