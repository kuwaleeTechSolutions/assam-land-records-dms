<?php
namespace App\Domain\Barcodes\Services;
use Picqer\Barcode\Types\TypeCode128;use Picqer\Barcode\Renderers\SvgRenderer;
class BarcodeRenderer{public function svg(string $value,int $width=360,int $height=80):string{$barcode=(new TypeCode128())->getBarcode($value);return (new SvgRenderer())->render($barcode,$width,$height);}}
