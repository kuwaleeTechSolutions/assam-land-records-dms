<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('htr_training_samples', function (Blueprint $t) {
            $t->id();
            $t->ulid('ulid')->unique();
            $t->foreignId('document_extracted_field_id')->constrained()->cascadeOnDelete();
            $t->foreignId('document_id')->constrained()->restrictOnDelete();
            $t->string('field_name', 100)->index();
            $t->string('language', 20)->default('asm');
            $t->string('source_disk', 50);
            $t->string('source_key', 1000);
            $t->text('model_prediction')->nullable();
            $t->text('verified_text');
            $t->decimal('model_confidence', 5, 2)->nullable();
            $t->string('sample_status', 30)->default('READY')->index();
            $t->string('split', 20)->nullable()->index();
            $t->string('writer_group', 100)->nullable()->index();
            $t->char('image_sha256', 64)->index();
            $t->foreignId('verified_by')->constrained('users')->restrictOnDelete();
            $t->timestamp('verified_at');
            $t->timestamp('exported_at')->nullable();
            $t->timestamps();
            $t->unique(['document_extracted_field_id', 'image_sha256'], 'htr_sample_field_image_unique');
        });

        Schema::create('recognition_benchmarks', function (Blueprint $t) {
            $t->id();
            $t->ulid('ulid')->unique();
            $t->string('name', 200);
            $t->string('engine', 120);
            $t->string('engine_version', 80)->nullable();
            $t->unsignedInteger('sample_count')->default(0);
            $t->decimal('exact_match_rate', 7, 4)->nullable();
            $t->decimal('character_error_rate', 7, 4)->nullable();
            $t->decimal('average_confidence', 7, 4)->nullable();
            $t->decimal('average_seconds_per_sample', 10, 4)->nullable();
            $t->json('field_metrics')->nullable();
            $t->json('configuration')->nullable();
            $t->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recognition_benchmarks');
        Schema::dropIfExists('htr_training_samples');
    }
};
