<?php
namespace App\Domain\Documents\Enums;
enum DocumentStatus:string{case Draft='DRAFT';case AwaitingUpload='AWAITING_UPLOAD';case Processing='PROCESSING';case ProcessingFailed='PROCESSING_FAILED';case PendingQc='PENDING_QC';case QcRejected='QC_REJECTED';case PendingVerification='PENDING_VERIFICATION';case VerificationRejected='VERIFICATION_REJECTED';case Verified='VERIFIED';case PendingApproval='PENDING_APPROVAL';case Approved='APPROVED';case Available='AVAILABLE';case Archived='ARCHIVED';}
