<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class HtrHealthCheck extends Command
{
    protected $signature = 'htr:health';
    protected $description = 'Check the configured Assamese HTR inference service';

    public function handle(): int
    {
        $url = rtrim((string) config('ocr.htr_url'), '/');

        if ($url === '') {
            $this->error('HTR_SERVICE_URL is not configured.');
            return self::FAILURE;
        }

        try {
            $response = Http::timeout(15)->get($url.'/health');
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        if (! $response->successful()) {
            $this->error('HTR service is unavailable: '.$response->body());
            return self::FAILURE;
        }

        $this->info('HTR service is ready.');
        $this->line(json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return self::SUCCESS;
    }
}
