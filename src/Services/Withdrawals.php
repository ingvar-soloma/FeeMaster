<?php

namespace IngvarSoloma\FeeMaster\Services;

use IngvarSoloma\FeeMaster\Utils\Money;
use InvalidArgumentException;

class Withdrawals
{
    private int $count;
    private Money $total;

    public function __construct(int $count = 0, Money $total = new Money(0.0, 'EUR'))
    {
        $this->count = $count;
        $this->total = $total;
    }

    final public function getCount(): int
    {
        return $this->count;
    }

    final public function getTotalAmount(): float
    {
        return $this->total->getAmount();
    }

    final public function addWithdrawal(Money $amount): void
    {
        if ($this->total->getCurrency() !== $amount->getCurrency()) {
            throw new InvalidArgumentException('Currency mismatch');
        }

        $this->count++;
        $this->total = new Money(
            amount: $this->total->getAmount() + $amount->getAmount(),
            currency: $this->total->getCurrency()
        );
    }
}
