<?php
namespace App\Domain\Verification\Enums;
enum VerificationStage:string{case QualityCheck='QUALITY_CHECK';case DocumentVerification='DOCUMENT_VERIFICATION';case FinalApproval='FINAL_APPROVAL';}
