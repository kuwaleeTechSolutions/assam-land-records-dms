<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::table('users',function(Blueprint $t){$t->text('mfa_secret')->nullable()->after('mfa_required');$t->json('mfa_recovery_codes')->nullable()->after('mfa_secret');$t->timestamp('mfa_confirmed_at')->nullable()->after('mfa_recovery_codes');});}public function down():void{Schema::table('users',fn(Blueprint $t)=>$t->dropColumn(['mfa_secret','mfa_recovery_codes','mfa_confirmed_at']));}};
