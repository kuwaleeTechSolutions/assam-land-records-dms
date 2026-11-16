<?php
namespace App\Console\Commands;

use App\Models\HtrTrainingSample;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportHtrTrainingDataset extends Command
{
    protected $signature = 'htr:export-dataset {output=storage/app/htr-training-dataset.zip} {--limit=10000}';
    protected $description = 'Export verified Assamese HTR samples as a secured ZIP manifest';

    public function handle(): int
    {
        $samples = HtrTrainingSample::where('sample_status', 'READY')
            ->whereNull('exported_at')
            ->orderBy('id')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($samples->isEmpty()) {
            $this->warn('No unexported verified HTR samples are available.');
            return self::SUCCESS;
        }

        $output = base_path($this->argument('output'));
        @mkdir(dirname($output), 0700, true);
        $zip = new ZipArchive();
        if ($zip->open($output, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('Unable to create dataset ZIP.');
            return self::FAILURE;
        }

        $manifest = fopen('php://temp', 'w+');
        fputcsv($manifest, ['sample_id','field_name','image_path','verified_text','prediction','confidence','split']);

        foreach ($samples as $sample) {
            $source = Storage::disk($sample->source_disk)->path($sample->source_key);
            if (! is_file($source)) continue;
            $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION)) ?: 'pdf';
            $inside = 'sources/'.$sample->ulid.'.'.$ext;
            $zip->addFile($source, $inside);
            fputcsv($manifest, [$sample->ulid,$sample->field_name,$inside,$sample->verified_text,$sample->model_prediction,$sample->model_confidence,$sample->split]);
        }

        rewind($manifest);
        $zip->addFromString('manifest.csv', stream_get_contents($manifest));
        fclose($manifest);
        $zip->addFromString('README.txt', "Authorized Assamese HTR training export. Contains verified government-record samples. Handle as sensitive data.\n");
        $zip->close();

        HtrTrainingSample::whereKey($samples->pluck('id'))->update(['exported_at' => now(), 'sample_status' => 'EXPORTED']);
        $this->info('Dataset exported: '.$output);
        $this->info('Samples: '.$samples->count());
        return self::SUCCESS;
    }
}
