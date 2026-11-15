// Merge these records into DatabaseSeeder::run() after the Release 1 seed calls.
$land = \App\Models\DocumentCategory::firstOrCreate(['code'=>'LAND_RECORD'],['name_en'=>'Land Record','name_as'=>'ভূমি অভিলেখ','requires_verification'=>true,'sort_order'=>10,'is_active'=>true]);
$types = [
 ['JAMABANDI','Jamabandi',true,true,true],['CHITHA','Chitha',true,true,true],['PATTA','Patta',true,false,true],['PERIODIC_PATTA','Periodic Patta',true,false,true],['ANNUAL_PATTA','Annual Patta',true,false,true],['DAG_RECORD','Dag-related Record',false,true,true]
];
foreach($types as [$code,$name,$patta,$dag,$year]) \App\Models\DocumentType::firstOrCreate(['code'=>$code],['document_category_id'=>$land->id,'name_en'=>$name,'requires_patta_no'=>$patta,'requires_dag_no'=>$dag,'requires_record_year'=>$year,'is_active'=>true]);
