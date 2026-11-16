<?php
return [
    'documents_disk' => env('DMS_DOCUMENTS_DISK', 'local'),
    'quarantine_disk' => env('DMS_QUARANTINE_DISK', 'local'),
    'quarantine_prefix' => env('DMS_QUARANTINE_PREFIX', 'dms-quarantine'),
    'documents_prefix' => env('DMS_DOCUMENTS_PREFIX', 'dms-documents'),
    'max_upload_kb' => (int) env('DMS_MAX_UPLOAD_KB', 262144), // 256 MiB initial web-upload ceiling
    'pdfinfo_binary' => env('DMS_PDFINFO_BINARY', 'pdfinfo'),
    'bulk_zip_prefix' => env('DMS_BULK_ZIP_PREFIX', 'dms-bulk-zips'),
    'max_zip_upload_kb' => (int) env('DMS_MAX_ZIP_UPLOAD_KB', 1048576), // 1 GiB initial ceiling
    'max_zip_files' => (int) env('DMS_MAX_ZIP_FILES', 5000),
];
