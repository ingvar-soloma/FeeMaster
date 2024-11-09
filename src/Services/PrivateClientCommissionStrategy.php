<?php

namespace IngvarSoloma\FeeMaster\Services;

use DateTime;
use IngvarSoloma\FeeMaster\Models\Operation;
use IngvarSoloma\FeeMaster\Repositories\WithdrawalsRepository;
use IngvarSoloma\FeeMaster\Repositories\InMemoryWithdrawalsRepository;
use IngvarSoloma\FeeMaster\Utils\CurrencyConverterInterface;
use IngvarSoloma\FeeMaster\Utils\Money;

class PrivateClientCommissionStrategy extends CommissionStrategyCalculator
{
    private const FREE_WITHDRAW_LIMIT = 1000.00;
    private const FREE_WITHDRAW_COUNT = 3;
    private const COMMISSION_RATE = 0.003;

    public function __construct(
        private readonly CurrencyConverterInterface $converter,
        private readonly WithdrawalsRepository $withdrawalsRepository = new InMemoryWithdrawalsRepository()
    ) {}

    /**
     * @throws \Exception
     */
    final protected function calculateWithdrawCommission(Operation $operation): float
    {
        $operationMoney = new Money($operation->amount, $operation->currency);
        $operationInBaseCurrency = $operationMoney->convertToBaseCurrency($this->converter);
        $withdrawals = $this->withdrawalsRepository->getWithdrawals($operation->userId, $operation->date);

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

        $this->withdrawalsRepository->saveWithdrawals($operation->userId, $operation->date, $withdrawals);

        return self::roundUpCommission($commissionAmount * self::COMMISSION_RATE, 2);
    }
}
