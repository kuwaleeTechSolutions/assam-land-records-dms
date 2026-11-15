<?php
namespace App\Domain\PdfProcessing\Services;
use RuntimeException;use Symfony\Component\Process\Process;
class PdfCommandRunner{public function run(array $command,int $timeout=null):string{$process=new Process($command);$process->setTimeout($timeout??config('pdf_processing.timeout'));$process->run();if(!$process->isSuccessful())throw new RuntimeException(trim($process->getErrorOutput()?:$process->getOutput())?:'PDF processing command failed.');return $process->getOutput();}}
