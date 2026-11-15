<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CompressionProfileSeeder::class,
        ]);

        State::firstOrCreate(
            ['code' => 'AS'],
            [
                'ulid' => (string) Str::ulid(),
                'name_en' => 'Assam',
                'name_as' => 'অসম',
                'is_active' => true,
            ],
        );

        $landRecords = DocumentCategory::firstOrCreate(
            ['code' => 'LAND_RECORD'],
            [
                'name_en' => 'Land Record',
                'name_as' => 'ভূমি অভিলেখ',
                'description' => 'Core Assam land-record documents.',
                'requires_verification' => true,
                'sort_order' => 10,
                'is_active' => true,
            ],
        );

        $types = [
            [
                'code' => 'JAMABANDI',
                'name' => 'Jamabandi',
                'patta' => true,
                'dag' => true,
                'year' => true,
            ],
            [
                'code' => 'CHITHA',
                'name' => 'Chitha',
                'patta' => true,
                'dag' => true,
                'year' => true,
            ],
            [
                'code' => 'PATTA',
                'name' => 'Patta',
                'patta' => true,
                'dag' => false,
                'year' => true,
            ],
            [
                'code' => 'PERIODIC_PATTA',
                'name' => 'Periodic Patta',
                'patta' => true,
                'dag' => false,
                'year' => true,
            ],
            [
                'code' => 'ANNUAL_PATTA',
                'name' => 'Annual Patta',
                'patta' => true,
                'dag' => false,
                'year' => true,
            ],
            [
                'code' => 'DAG_RECORD',
                'name' => 'Dag-related Record',
                'patta' => false,
                'dag' => true,
                'year' => true,
            ],
        ];

        foreach ($types as $index => $type) {
            DocumentType::updateOrCreate(
                ['code' => $type['code']],
                [
                    'document_category_id' => $landRecords->id,
                    'name_en' => $type['name'],
                    'requires_patta_no' => $type['patta'],
                    'requires_dag_no' => $type['dag'],
                    'requires_record_year' => $type['year'],
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ],
            );
        }
    }
}
