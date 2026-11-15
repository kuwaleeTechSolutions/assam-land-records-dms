<?php
namespace Tests\Feature\PdfProcessing;
use App\Models\CompressionProfile;use Database\Seeders\CompressionProfileSeeder;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class CompressionProfileTest extends TestCase{use RefreshDatabase;public function test_processing_profiles_are_versioned_and_seeded():void{$this->seed(CompressionProfileSeeder::class);$this->assertDatabaseHas('compression_profiles',['code'=>'ARCHIVAL_HIGH','version'=>1,'status'=>'PUBLISHED']);$this->assertDatabaseHas('compression_profiles',['code'=>'BALANCED','version'=>1]);$this->assertSame(4,CompressionProfile::count());}}
