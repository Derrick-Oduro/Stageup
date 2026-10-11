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
        Schema::create('fixtures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->string('round_name')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('reporting_deadline')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('fixture_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('side')->default('home');
            $table->timestamps();

            $table->unique(['fixture_id', 'user_id']);
        });

        Schema::create('score_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('score_for')->default(0);
            $table->unsignedTinyInteger('score_against')->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('submitted');
            $table->timestamps();

            $table->unique(['fixture_id', 'submitted_by_user_id'], 'score_submissions_fixture_user_unique');
        });

        Schema::create('official_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fixture_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('winner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('loser_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('score_for')->default(0);
            $table->unsignedTinyInteger('score_against')->default(0);
            $table->foreignId('approved_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_results');
        Schema::dropIfExists('score_submissions');
        Schema::dropIfExists('fixture_participants');
        Schema::dropIfExists('fixtures');
    }
};
