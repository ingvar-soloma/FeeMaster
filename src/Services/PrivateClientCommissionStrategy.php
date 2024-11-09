<?php

namespace IngvarSoloma\FeeMaster\Services;

use DateTime;
use IngvarSoloma\FeeMaster\Models\Operation;
use IngvarSoloma\FeeMaster\Utils\CurrencyConverterInterface;
use IngvarSoloma\FeeMaster\Utils\Money;

class PrivateClientCommissionStrategy extends CommissionStrategyCalculator
{
    private const FREE_WITHDRAW_LIMIT = 1000.00;
    private const FREE_WITHDRAW_COUNT = 3;
    private const COMMISSION_RATE = 0.003;

    private array $weeklyWithdrawals = [];

    public function __construct(private readonly CurrencyConverterInterface $converter) {}

    /**
     * @throws \Exception
     */
    final protected function calculateWithdrawCommission(Operation $operation): float
    {
        $operationMoney = new Money($operation->amount, $operation->currency);
        $operationInBaseCurrency = $operationMoney->convertToBaseCurrency($this->converter);
        $withdrawals = $this->getWithdrawals($operation);

        if ($withdrawals->getCount() < self::FREE_WITHDRAW_COUNT
            && $withdrawals->getTotalAmount() < self::FREE_WITHDRAW_LIMIT) {
            $freeAmount = min(self::FREE_WITHDRAW_LIMIT - $withdrawals->getTotalAmount(), $operationInBaseCurrency->getAmount());
            $withdrawals->addWithdrawal($operationInBaseCurrency);

            $commissionInBaseCurrency = new Money($operationInBaseCurrency->getAmount() - $freeAmount, CurrencyConverterInterface::BASE_CURRENCY);
            $commission = $commissionInBaseCurrency->convertFromBaseCurrency($this->converter, $operation->currency);
            $commissionAmount = $commission->getAmount();
        } else {
            $commissionAmount = $operationMoney->getAmount();
        }

        return self::roundUpCommission($commissionAmount * self::COMMISSION_RATE, 2);
    }

    /**
     * @throws \Exception
     */
    private function getWithdrawals(Operation $operation): Withdrawals
    {
        $weekNumber = $this->getWeekNumber($operation->date);

        if (!isset($this->weeklyWithdrawals[$operation->userId][$weekNumber])) {
            $this->weeklyWithdrawals[$operation->userId][$weekNumber] = new Withdrawals();
        }

        return $this->weeklyWithdrawals[$operation->userId][$weekNumber];
    }

    /**
     * @throws \Exception
     */
    private function getWeekNumber(string $date): int
    {
        $dateTime = new DateTime($date);
        return (int)$dateTime->format('oW');
    }
}
