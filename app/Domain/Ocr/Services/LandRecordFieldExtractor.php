<?php

namespace App\Domain\Ocr\Services;

class LandRecordFieldExtractor
{
    public function __construct(
        private AssameseDigitNormalizer $digits
    ) {
    }

    public function extract(
        string $text,
        float $confidence
    ): array {
        /*
         * Normalize common OCR formatting noise while
         * preserving Assamese characters.
         */
        $clean = preg_replace('/[ \t]+/u', ' ', $text);

        $clean = str_replace(
            ['：', '–', '—', '।'],
            [':', '-', '-', ' '],
            $clean
        );

        $results = [];

        /*
         * PATTA NUMBER
         *
         * Tesseract may recognize:
         *
         * পট্টা নং
         * পটা নং
         * পাষ্টা নং
         * পাষ্টানং
         * patta no
         */
        $pattaPatterns = [
            '/(?:পট্টা|পটা|পাষ্টা|পাষ্টা|পাষ্টা|patta)\s*(?:নং|নম্বৰ|no\.?)?\s*[:._-]*\s*([০-৯0-9]+(?:[\/.-][০-৯0-9]+)*)/iu',

            '/(?:পট্টানং|পটানং|পাষ্টানং)\s*[:._-]*\s*([০-৯0-9]+(?:[\/.-][০-৯0-9]+)*)/iu',
        ];

        $this->captureNumeric(
            results: $results,
            field: 'patta_no',
            text: $clean,
            patterns: $pattaPatterns,
            confidence: $confidence,
        );

        /*
         * DAG NUMBER
         *
         * Allows noise between the label and number:
         *
         * দাগ নং: ১২৩
         * দাগ নং: _ ১২৩
         * দাগনং ১২৩
         * dag no 123
         */
        $dagPatterns = [
            '/(?:দাগ|dag)\s*(?:নং|নম্বৰ|no\.?)?\s*[:|._-]*\s*([০-৯0-9]+(?:[\/.-][০-৯0-9]+)*)/iu',

            '/(?:দাগনং)\s*[:|._-]*\s*([০-৯0-9]+(?:[\/.-][০-৯0-9]+)*)/iu',
        ];

        $this->captureNumeric(
            results: $results,
            field: 'dag_no',
            text: $clean,
            patterns: $dagPatterns,
            confidence: $confidence,
        );

        /*
         * RECORD YEAR
         *
         * Assamese uses words such as:
         * বছৰ, চন, সন
         *
         * Both Assamese and English numerals are accepted.
         */
        $yearPatterns = [
            '/(?:বছৰ|চন|সন|year)\s*[:|._-]*\s*([০-৯0-9]{4})/iu',

            /*
             * Conservative standalone-year fallback.
             */
            '/(?<![০-৯0-9])((?:১৮|১৯|২০|21)[০-৯0-9]{2})(?![০-৯0-9])/u',
        ];

        $this->captureNumeric(
            results: $results,
            field: 'record_year',
            text: $clean,
            patterns: $yearPatterns,
            confidence: $confidence,
            validateYear: true,
        );

        /*
         * Keep Assamese personal names in Assamese.
         *
         * These should remain verification-sensitive until
         * the dedicated HTR model performs reliably.
         */
        $pattadarPatterns = [
            '/(?:পট্টাদাৰৰ?\s*নাম|পট্টাদাৰ|pattadar\s*name)\s*[:._-]*\s*([^\r\n|]{2,150})/iu',
        ];

        $this->captureText(
            results: $results,
            field: 'landholder_name',
            text: $clean,
            patterns: $pattadarPatterns,
            confidence: $confidence,
        );

        $guardianPatterns = [
            '/(?:অভিভাৱকৰ?\s*নাম|অভিভাৱক|পিতাৰ?\s*নাম|guardian\s*name|father\s*name)\s*[:._-]*\s*([^\r\n|]{2,150})/iu',
        ];

        $this->captureText(
            results: $results,
            field: 'guardian_name',
            text: $clean,
            patterns: $guardianPatterns,
            confidence: $confidence,
        );

        /*
         * Do not return duplicate fields.
         */
        $unique = [];

        foreach ($results as $result) {
            if (! isset($unique[$result['field_name']])) {
                $unique[$result['field_name']] = $result;
            }
        }

        return array_values($unique);
    }

    private function captureNumeric(
        array &$results,
        string $field,
        string $text,
        array $patterns,
        float $confidence,
        bool $validateYear = false,
    ): void {
        foreach ($patterns as $pattern) {
            if (! preg_match($pattern, $text, $match)) {
                continue;
            }

            $source = trim($match[1]);

            $normalized = trim(
                (string) $this->digits->normalize($source)
            );

            /*
             * Remove common recognition spacing around
             * otherwise numeric values.
             */
            $normalized = preg_replace(
                '/\s+/u',
                '',
                $normalized
            );

            if ($validateYear) {
                $year = (int) $normalized;

                if (
                    $year < 1700 ||
                    $year > (int) now()->year
                ) {
                    continue;
                }
            }

            $results[] = [
                'field_name' => $field,
                'source_value' => $source,
                'normalized_value' => $normalized,
                'confidence' => round($confidence, 2),
            ];

            return;
        }
    }

    private function captureText(
        array &$results,
        string $field,
        string $text,
        array $patterns,
        float $confidence,
    ): void {
        foreach ($patterns as $pattern) {
            if (! preg_match($pattern, $text, $match)) {
                continue;
            }

            $value = trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    $match[1]
                )
            );

            if ($value === '') {
                continue;
            }

            $results[] = [
                'field_name' => $field,
                'source_value' => $value,

                /*
                 * Personal names remain in the recognized
                 * Assamese script.
                 */
                'normalized_value' => $value,

                'confidence' => round($confidence, 2),
            ];

            return;
        }
    }
}