<?php
namespace App\Domain\Ocr\Services;
class AssameseDigitNormalizer
{
    public function normalize(?string $value): ?string
    {
        if ($value === null) return null;
        return strtr($value, ['০'=>'0','১'=>'1','২'=>'2','৩'=>'3','৪'=>'4','৫'=>'5','৬'=>'6','৭'=>'7','৮'=>'8','৯'=>'9']);
    }
}
