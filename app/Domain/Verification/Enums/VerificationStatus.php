<?php
namespace App\Domain\Verification\Enums;
enum VerificationStatus:string{case Pending='PENDING';case InReview='IN_REVIEW';case Approved='APPROVED';case Rejected='REJECTED';case Returned='RETURNED';}
