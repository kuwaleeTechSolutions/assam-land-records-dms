<?php
return [
    'tesseract_binary' => env('OCR_TESSERACT_BINARY', 'tesseract'),
    'pdftoppm_binary' => env('OCR_PDFTOPPM_BINARY', 'pdftoppm'),
    'languages' => env('OCR_LANGUAGES', 'asm+eng'),
    'dpi' => (int) env('OCR_DPI', 300),
    'page_segmentation_mode' => (int) env('OCR_PSM', 6),
    'auto_accept_confidence' => (float) env('OCR_AUTO_ACCEPT_CONFIDENCE', 97),
    'verify_confidence' => (float) env('OCR_VERIFY_CONFIDENCE', 85),
    'htr_url' => env('HTR_SERVICE_URL'),
    'htr_token' => env('HTR_SERVICE_TOKEN'),
    'htr_timeout' => (int) env('HTR_SERVICE_TIMEOUT', 600),
];
