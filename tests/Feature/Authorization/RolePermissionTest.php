<?php
namespace Tests\Feature\Authorization;
use App\Models\User;use Database\Seeders\RolePermissionSeeder;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class RolePermissionTest extends TestCase{use RefreshDatabase;public function test_registration_operator_has_expected_permission():void{$this->seed(RolePermissionSeeder::class);$u=User::factory()->create();$u->assignRole('Document Registration Operator');$this->assertTrue($u->can('documents.register'));$this->assertFalse($u->can('documents.approve'));}}
