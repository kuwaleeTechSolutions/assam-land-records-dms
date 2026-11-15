<?php
namespace App\Livewire\BulkImport;
use App\Domain\BulkImport\Services\CsvMigrationParser;use App\Jobs\ValidateMigrationBatch;use Livewire\Attributes\Layout;use Livewire\Component;use Livewire\WithFileUploads;
#[Layout('layouts.app')]
class ImportMetadata extends Component{use WithFileUploads;public $csv;public function mount():void{abort_unless(app(\App\Domain\Authorization\Services\PermissionResolver::class)->allows(auth()->user(),'bulk.import'),403);}public function upload(CsvMigrationParser $parser):void{$this->validate(['csv'=>['required','file','mimes:csv,txt','max:51200']]);$batch=$parser->createBatch($this->csv,auth()->id());ValidateMigrationBatch::dispatch($batch->id);$this->redirectRoute('bulk.show',$batch,navigate:true);}public function render(){return view('livewire.bulk-import.import-metadata');}}
