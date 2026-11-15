<?php

namespace Database\Factories;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Models\District;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\RevenueCircle;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $user = User::factory()->create();

        $state = State::firstOrCreate(
            ['code' => 'AS'],
            [
                'ulid' => (string) Str::ulid(),
                'name_en' => 'Assam',
                'name_as' => 'অসম',
                'is_active' => true,
            ],
        );

        $district = District::create([
            'ulid' => (string) Str::ulid(),
            'state_id' => $state->id,
            'code' => 'TST-' . Str::upper(Str::random(8)),
            'name_en' => fake()->city() . ' District',
            'is_active' => true,
        ]);

        $circle = RevenueCircle::create([
            'ulid' => (string) Str::ulid(),
            'district_id' => $district->id,
            'code' => 'CIR-' . Str::upper(Str::random(8)),
            'name_en' => fake()->city() . ' Revenue Circle',
            'is_active' => true,
        ]);

        $category = DocumentCategory::create([
            'code' => 'CAT-' . Str::upper(Str::random(10)),
            'name_en' => 'Test Land Record',
            'requires_verification' => true,
            'is_active' => true,
        ]);

        $type = DocumentType::create([
            'document_category_id' => $category->id,
            'code' => 'TYPE-' . Str::upper(Str::random(10)),
            'name_en' => 'Test Document Type',
            'requires_patta_no' => false,
            'requires_dag_no' => false,
            'requires_record_year' => false,
            'is_active' => true,
        ]);

        return [
            'ulid' => (string) Str::ulid(),

            'reference_no' =>
                'AS-LR-TEST-' . Str::upper(Str::random(12)),

            'document_category_id' => $category->id,
            'document_type_id' => $type->id,

            'state_id' => $state->id,
            'district_id' => $district->id,
            'revenue_circle_id' => $circle->id,

            'title' => fake()->sentence(4),

            'patta_no' => null,
            'dag_no' => null,

            'record_year' => fake()->numberBetween(1950, 2026),

            'status' => DocumentStatus::AwaitingUpload,

            'registered_by' => $user->id,
            'registered_at' => now(),
        ];
    }
}
