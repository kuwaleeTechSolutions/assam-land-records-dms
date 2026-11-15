<?php
namespace App\Livewire\Auth;
use App\Domain\Security\Services\{MfaService,SecurityEventRecorder};use Livewire\Attributes\Layout;use Livewire\Component;
#[Layout('layouts.guest')]
class MfaChallenge extends Component{public string $code='';public function mount():void{abort_unless(auth()->check(),401);if(!auth()->user()->mfa_required)$this->redirectRoute('dashboard');}public function verify(MfaService $mfa,SecurityEventRecorder $events):void{$this->validate(['code'=>['required','string','max:30']]);$user=auth()->user();$ok=$mfa->verify($user,$this->code)||$mfa->useRecoveryCode($user,$this->code);if(!$ok){$events->record('MFA_FAILED','HIGH','DENIED',$user);$this->addError('code','Invalid authentication code.');return;}session(['mfa_verified_at'=>now()->timestamp]);$events->record('MFA_SUCCESS','INFO','SUCCESS',$user);$this->redirectRoute('dashboard',navigate:true);}public function render(){return view('livewire.auth.mfa-challenge');}}
