<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocumentFile>
 */
class DocumentFileFactory extends Factory
{
    protected $model = DocumentFile::class;

    public function definition(): array
    {
        $user = User::factory()->create();

        $document = Document::factory()->create([
            'registered_by' => $user->id,
        ]);

        $version = DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => 1,
            'change_reason' => 'TEST_FIXTURE',
            'created_by' => $user->id,
        ]);

        $document->update([
            'current_version_id' => $version->id,
        ]);

        return [
            'ulid' => (string) Str::ulid(),

            'document_version_id' => $version->id,

            'file_role' => 'MASTER',

            'storage_disk' => 'local',

            'storage_key' =>
                'tests/documents/' .
                Str::lower((string) Str::ulid()) .
                '/master.pdf',

            'original_filename' => 'test-document.pdf',

            'stored_filename' =>
                Str::lower((string) Str::ulid()) . '.pdf',

            'mime_type' => 'application/pdf',

            'size_bytes' => 1024,

            'page_count' => 1,

            'sha256' => str_repeat('a', 64),

            'processing_status' => 'MASTER_ACCEPTED',

            'uploaded_by' => $user->id,

            'uploaded_at' => now(),

            'processed_at' => null,
        ];
    }
}
