<?php
namespace Tests\Feature\Governance;
use App\Domain\Governance\Services\LegalHoldService;use App\Models\{Document,User};use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class LegalHoldTest extends TestCase{use RefreshDatabase;public function test_legal_hold_sets_document_protection_flag():void{config(['app.dms_audit_hmac_key'=>'test-secret']);$this->seed();$u=User::factory()->create();$this->actingAs($u);$doc=Document::factory()->create(['registered_by'=>$u->id]);app(LegalHoldService::class)->place($doc,'Court direction','CASE-1',$u->id);$this->assertTrue((bool)$doc->fresh()->legal_hold);$this->assertDatabaseHas('document_legal_holds',['document_id'=>$doc->id,'authority_reference'=>'CASE-1']);}}
