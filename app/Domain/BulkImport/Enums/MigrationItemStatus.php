<?php
namespace App\Domain\BulkImport\Enums;
enum MigrationItemStatus:string{case Pending='PENDING';case Validating='VALIDATING';case Valid='VALID';case Warning='WARNING';case Invalid='INVALID';case Registering='REGISTERING';case Registered='REGISTERED';case Success='SUCCESS';case Failed='FAILED';}
