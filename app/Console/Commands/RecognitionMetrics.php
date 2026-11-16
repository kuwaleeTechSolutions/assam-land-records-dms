<?php
namespace App\Console\Commands;

use App\Models\DocumentExtractedField;
use Illuminate\Console\Command;

class RecognitionMetrics extends Command
{
    protected $signature = 'ocr:metrics';
    protected $description = 'Show field-level OCR/HTR acceptance and correction metrics';

    public function handle(): int
    {
        $rows = DocumentExtractedField::query()
            ->selectRaw('field_name, COUNT(*) total')
            ->selectRaw("SUM(verification_status = 'AUTO_ACCEPTED') auto_accepted")
            ->selectRaw("SUM(verification_status = 'VERIFIED') verified")
            ->selectRaw("SUM(verification_status = 'CORRECTED') corrected")
            ->selectRaw("SUM(verification_status = 'MISSING') missing")
            ->selectRaw('ROUND(AVG(confidence),2) avg_confidence')
            ->groupBy('field_name')
            ->orderBy('field_name')
            ->get();

        $this->table(
            ['Field','Total','Auto','Verified','Corrected','Missing','Avg confidence'],
            $rows->map(fn ($r) => [$r->field_name,$r->total,$r->auto_accepted,$r->verified,$r->corrected,$r->missing,$r->avg_confidence])->all(),
        );
        return self::SUCCESS;
    }
}
