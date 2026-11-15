<?php
namespace Tests\Feature\Audit;
use App\Domain\Audit\Services\AuditRecorder;use App\Models\User;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class AuditRecorderTest extends TestCase{use RefreshDatabase;public function test_audit_events_are_chained_and_secrets_redacted():void{config(['app.dms_audit_hmac_key'=>'test-audit-secret']);$u=User::factory()->create();$r=app(AuditRecorder::class);$a=$r->record('USER_UPDATED','IDENTITY','SUCCESS',$u,['new_values'=>['name'=>'Officer','password'=>'secret']]);$b=$r->record('ROLE_ASSIGNED','AUTHORIZATION','SUCCESS',$u);$this->assertSame('[REDACTED]',$a->new_values['password']);$this->assertSame($a->event_hash,$b->previous_hash);$this->assertSame(64,strlen($b->event_hash));}}
