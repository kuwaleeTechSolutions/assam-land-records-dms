<?php
namespace App\Http\Middleware;
use Closure;use Illuminate\Http\Request;use Symfony\Component\HttpFoundation\Response;
class RequireRecentMfa{public function handle(Request $request,Closure $next):Response{$user=$request->user();if(!$user)return redirect()->route('login');if(!$user->mfa_required)return abort(403,'Multi-factor authentication enrollment is required for this privileged operation.');$at=(int)$request->session()->get('mfa_verified_at',0);$window=config('security.privileged_mfa_minutes',15)*60;if(!$at||time()-$at>$window)return redirect()->route('mfa.challenge');return $next($request);}}
