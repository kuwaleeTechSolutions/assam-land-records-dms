<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class HtrTrainingSample extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array
    {
        return [
            'model_confidence' => 'decimal:2',
            'verified_at' => 'datetime',
            'exported_at' => 'datetime',
        ];
    }
    public function uniqueIds(): array { return ['ulid']; }
    public function field() { return $this->belongsTo(DocumentExtractedField::class, 'document_extracted_field_id'); }
    public function document() { return $this->belongsTo(Document::class); }
}
