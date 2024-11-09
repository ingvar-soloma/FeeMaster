<?php
namespace IngvarSoloma\FeeMaster\Tests\Feature;

use IngvarSoloma\FeeMaster\Utils\CurrencyConverter;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    /**
     * @dataProvider conversionDataProvider
     */
    final public function testConvertToEUR(float $amount, string $currency): void
    {
        $converter = CurrencyConverter::getInstance();
        $amountInEur = $converter->convertToBaseCurrency($amount, $currency);
        $this->assertIsFloat($amountInEur);
        $this->assertGreaterThan(0, $amountInEur);
    }

    final public function conversionDataProvider(): array
    {
        return [
            [110, 'USD'],
            [13000, 'JPY'],
            [100, 'EUR'],
        ];
    }
}
