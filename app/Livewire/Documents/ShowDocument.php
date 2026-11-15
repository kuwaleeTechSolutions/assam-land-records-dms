<?php
namespace App\Livewire\Documents;
use App\Domain\Barcodes\Services\BarcodeRenderer;use App\Models\Document;use Livewire\Attributes\Layout;use Livewire\Component;
#[Layout('layouts.app')]
class ShowDocument extends Component{public Document $document;public function mount(Document $document){$this->document=$document->load('barcode','category','documentType','district','revenueCircle','mouza','village');abort_unless(app(\App\Domain\Authorization\Services\PermissionResolver::class)->allows(auth()->user(),'documents.view'),403);}public function render(BarcodeRenderer $renderer){return view('livewire.documents.show-document',['barcodeSvg'=>$renderer->svg($this->document->barcode->barcode_value)]);}}
