<?php
namespace Tests\Feature\Reporting;
use App\Models\User;use Database\Seeders\RolePermissionSeeder;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class ExportAuthorizationTest extends TestCase{use RefreshDatabase;public function test_document_viewer_does_not_automatically_have_export_permission():void{$this->seed(RolePermissionSeeder::class);$u=User::factory()->create();$u->assignRole('Document Viewer');$this->assertTrue($u->can('documents.preview'));$this->assertFalse($u->can('reports.export'));}}
