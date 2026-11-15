<?php
namespace App\Domain\Documents\Services;
use App\Models\Document;use Illuminate\Support\Collection;
class DuplicateCandidateService{public function find(array $data):Collection{$q=Document::query()->where('document_type_id',$data['document_type_id'])->where('district_id',$data['district_id'])->where('revenue_circle_id',$data['revenue_circle_id']);foreach(['mouza_id','village_id','patta_no','dag_no','record_year'] as $field){if(!empty($data[$field]))$q->where($field,$data[$field]);}return $q->limit(10)->get();}}
