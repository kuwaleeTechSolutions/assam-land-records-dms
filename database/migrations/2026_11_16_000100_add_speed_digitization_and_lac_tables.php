<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lacs', function (Blueprint $t) {
            $t->id(); $t->ulid('ulid')->unique();
            $t->foreignId('district_id')->constrained()->restrictOnDelete();
            $t->string('code', 30); $t->unsignedSmallInteger('constituency_no')->nullable();
            $t->string('name_en', 200); $t->string('name_as', 255)->nullable();
            $t->date('valid_from')->nullable(); $t->date('valid_to')->nullable();
            $t->boolean('is_active')->default(true); $t->timestamps();
            $t->unique(['district_id','code']);
        });
        Schema::create('village_circle_assignments', function (Blueprint $t) {
            $t->id(); $t->foreignId('village_id')->constrained()->cascadeOnDelete();
            $t->foreignId('revenue_circle_id')->constrained()->restrictOnDelete();
            $t->date('valid_from')->nullable(); $t->date('valid_to')->nullable();
            $t->boolean('is_current')->default(true); $t->timestamps();
            $t->unique(['village_id','revenue_circle_id','valid_from'],'vca_unique');
        });
        Schema::create('village_lac_assignments', function (Blueprint $t) {
            $t->id(); $t->foreignId('village_id')->constrained()->cascadeOnDelete();
            $t->foreignId('lac_id')->constrained()->restrictOnDelete();
            $t->date('valid_from')->nullable(); $t->date('valid_to')->nullable();
            $t->boolean('is_current')->default(true); $t->timestamps();
            $t->unique(['village_id','lac_id','valid_from'],'vla_unique');
        });
        Schema::table('digitization_batches', function (Blueprint $t) {
            $t->foreignId('lac_id')->nullable()->after('district_id')->constrained()->restrictOnDelete();
            $t->foreignId('mouza_id')->nullable()->after('revenue_circle_id')->constrained()->restrictOnDelete();
            $t->foreignId('village_id')->nullable()->after('mouza_id')->constrained()->restrictOnDelete();
            $t->smallInteger('default_record_year')->nullable();
            $t->string('source_disk',50)->nullable(); $t->string('source_zip_key',1000)->nullable();
            $t->string('source_zip_name',500)->nullable(); $t->char('source_zip_sha256',64)->nullable()->index();
            $t->unsignedBigInteger('source_zip_size')->nullable();
            $t->unsignedInteger('total_files')->default(0); $t->unsignedInteger('processed_files')->default(0);
            $t->unsignedInteger('failed_files')->default(0); $t->unsignedInteger('verification_files')->default(0);
            $t->text('failure_message')->nullable(); $t->timestamp('processing_started_at')->nullable();
            $t->timestamp('processing_finished_at')->nullable();
        });
        Schema::table('documents', function (Blueprint $t) {
            $t->foreignId('lac_id')->nullable()->after('district_id')->constrained()->restrictOnDelete();
        });
        Schema::create('digitization_batch_items', function (Blueprint $t) {
            $t->id();
        
            $t->ulid('ulid')->unique();
        
            $t->foreignId('digitization_batch_id')
                ->constrained()
                ->cascadeOnDelete();
        
            $t->foreignId('document_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        
            $t->string('archive_path', 500);
        
            $t->char('archive_path_hash', 64);
        
            $t->string('original_filename', 500);
        
            $t->unsignedBigInteger('size_bytes')->nullable();
        
            $t->char('sha256', 64)
                ->nullable()
                ->index();
        
            $t->string('status', 40)
                ->default('PENDING')
                ->index();
        
            $t->string('ocr_status', 30)
                ->default('NOT_STARTED');
        
            $t->string('htr_status', 30)
                ->default('NOT_STARTED');
        
            $t->decimal('overall_confidence', 5, 2)
                ->nullable();
        
            $t->string('failure_code', 80)
                ->nullable();
        
            $t->text('failure_message')
                ->nullable();
        
            $t->timestamp('started_at')
                ->nullable();
        
            $t->timestamp('finished_at')
                ->nullable();
        
            $t->timestamps();
        
            $t->unique(
                ['digitization_batch_id', 'archive_path_hash'],
                'batch_archive_path_hash_unique'
            );
        });
        Schema::create('document_extractions', function (Blueprint $t) {
            $t->id(); $t->ulid('ulid')->unique();
            $t->foreignId('digitization_batch_item_id')->constrained()->cascadeOnDelete();
            $t->foreignId('document_id')->nullable()->constrained()->nullOnDelete();
            $t->string('engine',120); $t->string('engine_version',60)->nullable();
            $t->longText('raw_text')->nullable(); $t->string('language',30)->nullable();
            $t->decimal('overall_confidence',5,2)->nullable(); $t->string('status',30)->default('STAGED');
            $t->json('metadata')->nullable(); $t->timestamps();
        });
        Schema::create('document_extracted_fields', function (Blueprint $t) {
            $t->id(); $t->foreignId('document_extraction_id')->constrained()->cascadeOnDelete();
            $t->string('field_name',100); $t->text('source_value')->nullable(); $t->text('normalized_value')->nullable();
            $t->decimal('confidence',5,2)->nullable(); $t->boolean('requires_verification')->default(true);
            $t->string('verification_status',30)->default('PENDING');
            $t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('verified_at')->nullable(); $t->text('verification_note')->nullable(); $t->timestamps();
            $t->unique(
                ['document_extraction_id', 'field_name'],
                'extraction_field_unique'
            );
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('document_extracted_fields'); Schema::dropIfExists('document_extractions');
        Schema::dropIfExists('digitization_batch_items');
        Schema::table('documents', fn(Blueprint $t) => $t->dropConstrainedForeignId('lac_id'));
        Schema::table('digitization_batches', function (Blueprint $t) {
            foreach (['lac_id','mouza_id','village_id'] as $key) $t->dropConstrainedForeignId($key);
            $t->dropColumn(['default_record_year','source_disk','source_zip_key','source_zip_name','source_zip_sha256','source_zip_size','total_files','processed_files','failed_files','verification_files','failure_message','processing_started_at','processing_finished_at']);
        });
        Schema::dropIfExists('village_lac_assignments'); Schema::dropIfExists('village_circle_assignments'); Schema::dropIfExists('lacs');
    }
};