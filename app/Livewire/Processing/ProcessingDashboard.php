<?php
namespace App\Livewire\Processing;
use App\Models\{DocumentFile,ProcessingRun};use Livewire\Attributes\Layout;use Livewire\Component;use Livewire\WithPagination;
#[Layout('layouts.app')]
class ProcessingDashboard extends Component{use WithPagination;public string $status='';public function mount():void{abort_unless(app(\App\Domain\Authorization\Services\PermissionResolver::class)->allows(auth()->user(),'documents.upload'),403);}public function render(){return view('livewire.processing.processing-dashboard',['queued'=>DocumentFile::whereIn('processing_status',['MASTER_ACCEPTED','READY_FOR_DERIVATIVES'])->count(),'failed'=>DocumentFile::where('processing_status','FAILED')->count(),'ready'=>DocumentFile::where('processing_status','PROCESSED')->count(),'runs'=>ProcessingRun::query()->when($this->status,fn($q)=>$q->where('status',$this->status))->latest('started_at')->paginate(25)]);}}
