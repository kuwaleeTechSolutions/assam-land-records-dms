<?php
return [
 'qpdf_binary'=>env('DMS_QPDF_BINARY','qpdf'),
 'ghostscript_binary'=>env('DMS_GHOSTSCRIPT_BINARY','gs'),
 'pdfinfo_binary'=>env('DMS_PDFINFO_BINARY','pdfinfo'),
 'timeout'=>(int)env('DMS_PROCESS_TIMEOUT',900),
 'temp_path'=>base_path(env('DMS_PROCESS_TEMP','storage/app/private/dms-processing')),
];
