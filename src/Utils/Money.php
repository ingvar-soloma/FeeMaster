<?php

namespace IngvarSoloma\FeeMaster\Utils;

class Money
{
    public function __construct(
        private readonly float $amount,
        private readonly string $currency
    ) {}

    final public function getAmount(): float
    {
        return $this->amount;
    }

    final public function getCurrency(): string
    {
        return $this->currency;
    }

    public function convertToBaseCurrency(CurrencyConverterInterface $converter): Money
    {
        $convertedAmount = $converter->convertToBaseCurrency($this->amount, $this->currency);
        return new Money($convertedAmount, CurrencyConverterInterface::BASE_CURRENCY);
    }

    public function convertFromBaseCurrency(CurrencyConverterInterface $converter, string $currency)
    {
        $convertedAmount = $converter->convertFromBaseCurrency($this->amount, $currency);
        return new Money($convertedAmount, $currency);
    }
}
