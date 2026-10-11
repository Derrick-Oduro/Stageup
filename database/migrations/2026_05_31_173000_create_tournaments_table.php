<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('game');
            $table->enum('format', [
                'single_elimination',
                'double_elimination',
                'round_robin',
                'swiss',
                'group_knockout',
            ]);
            $table->enum('status', [
                'draft',
                'upcoming',
                'registration_open',
                'registration_closed',
                'scheduled',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('upcoming');
            $table->timestamp('registration_deadline')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->unsignedInteger('max_participants')->default(16);
            $table->unsignedInteger('participants_count')->default(0);
            $table->unsignedInteger('followers_count')->default(0);
            $table->boolean('is_public')->default(true);
            $table->text('rules')->nullable();
            $table->timestamps();

            $table->index(['is_public', 'status']);
            $table->index(['game', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
