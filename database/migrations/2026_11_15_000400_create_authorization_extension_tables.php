<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_permission_overrides', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('permission_name', 150);
            $table->string('effect', 10);

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();

            $table->foreignId('revoked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('reason', 500)->nullable();
            $table->timestamps();

            $table->index(
                ['user_id', 'permission_name', 'revoked_at'],
                'user_perm_override_lookup_idx'
            );
        });

        Schema::create('user_geographic_scopes', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('scope_level', 30);

            $table->foreignId('state_id')
                ->nullable()
                ->constrained('states')
                ->restrictOnDelete();

            $table->foreignId('district_id')
                ->nullable()
                ->constrained('districts')
                ->restrictOnDelete();

            $table->foreignId('subdivision_id')
                ->nullable()
                ->constrained('subdivisions')
                ->restrictOnDelete();

            $table->foreignId('revenue_circle_id')
                ->nullable()
                ->constrained('revenue_circles')
                ->restrictOnDelete();

            $table->foreignId('mouza_id')
                ->nullable()
                ->constrained('mouzas')
                ->restrictOnDelete();

            $table->foreignId('village_id')
                ->nullable()
                ->constrained('villages')
                ->restrictOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();

            $table->foreignId('revoked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['user_id', 'scope_level', 'revoked_at'],
                'user_geo_scope_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_geographic_scopes');
        Schema::dropIfExists('user_permission_overrides');
    }
};
