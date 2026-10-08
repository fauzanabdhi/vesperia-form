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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('form_sections')
                ->cascadeOnDelete();

            $table->string('external_id')->unique();
            $table->string('label');
            $table->string('type');
            $table->string('sub_type')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_orm_only')->default(false);
            $table->unsignedInteger('position');
            $table->json('definition_json');
            $table->timestamps();

            $table->index(['section_id', 'position']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
