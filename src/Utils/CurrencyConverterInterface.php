<?php

namespace IngvarSoloma\FeeMaster\Utils;

interface CurrencyConverterInterface
{
    const BASE_CURRENCY = 'EUR';

    public static function getInstance(): CurrencyConverterInterface;
    public function convertToBaseCurrency(float $amount, string $currency): float;

    public function convertFromBaseCurrency(float $amount, string $currency): float;
}
