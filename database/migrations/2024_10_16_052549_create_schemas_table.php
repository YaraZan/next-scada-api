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
        Schema::create('schemas', function (Blueprint $table) {
            $table->id();
            $table->binary('uuid', 16)->unique();
            $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schemas');
    }
};
