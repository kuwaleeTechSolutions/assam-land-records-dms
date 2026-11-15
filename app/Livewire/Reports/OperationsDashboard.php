<?php
namespace App\Livewire\Reports;
use App\Domain\Reporting\Services\DashboardMetrics;use Livewire\Attributes\Layout;use Livewire\Component;
#[Layout('layouts.app')]
class OperationsDashboard extends Component{public function mount():void{abort_unless(app(\App\Domain\Authorization\Services\PermissionResolver::class)->allows(auth()->user(),'reports.view'),403);}public function render(DashboardMetrics $metrics){return view('livewire.reports.operations-dashboard',['m'=>$metrics->for(auth()->user())]);}}
