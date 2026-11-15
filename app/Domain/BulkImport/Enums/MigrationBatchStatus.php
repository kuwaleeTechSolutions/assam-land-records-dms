<?php
namespace App\Domain\BulkImport\Enums;
enum MigrationBatchStatus:string{case Uploaded='UPLOADED';case Parsing='PARSING';case Validating='VALIDATING';case Validated='VALIDATED';case ValidatedWithWarnings='VALIDATED_WITH_WARNINGS';case FailedValidation='FAILED_VALIDATION';case AwaitingApproval='AWAITING_APPROVAL';case Importing='IMPORTING';case PartiallyCompleted='PARTIALLY_COMPLETED';case Completed='COMPLETED';case Cancelled='CANCELLED';}
