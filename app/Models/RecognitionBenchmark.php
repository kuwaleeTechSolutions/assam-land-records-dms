<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class RecognitionBenchmark extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array
    {
        return [
            'field_metrics' => 'array',
            'configuration' => 'array',
            'completed_at' => 'datetime',
        ];
    }
    public function uniqueIds(): array { return ['ulid']; }
}
