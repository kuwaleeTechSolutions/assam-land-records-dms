<?php
namespace App\Livewire\Admin;
use App\Models\AuditLog;use Livewire\Attributes\Layout;use Livewire\Component;use Livewire\WithPagination;
#[Layout('layouts.app')]
class AuditLogViewer extends Component{use WithPagination;public string $action='',$result='';public function mount():void{abort_unless(app(\App\Domain\Authorization\Services\PermissionResolver::class)->allows(auth()->user(),'audit.view'),403);}public function render(){return view('livewire.admin.audit-log-viewer',['events'=>AuditLog::query()->when($this->action,fn($q)=>$q->where('action','like','%'.$this->action.'%'))->when($this->result,fn($q)=>$q->where('result',$this->result))->latest('occurred_at')->paginate(50)]);}}
