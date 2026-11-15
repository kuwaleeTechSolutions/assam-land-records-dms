<?php
return [
 'privileged_mfa_minutes'=>(int)env('DMS_PRIVILEGED_MFA_MINUTES',15),
 'clamav_binary'=>env('DMS_CLAMAV_BINARY','clamscan'),
 'clamav_timeout'=>(int)env('DMS_CLAMAV_TIMEOUT',120),
 'clamav_required'=>(bool)env('DMS_CLAMAV_REQUIRED',true),
 'csp'=>env('DMS_CSP',"default-src 'self'; img-src 'self' data: blob:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; font-src 'self' data:; connect-src 'self'; frame-src 'self' blob:; object-src 'none'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'"),
];
