<?php

namespace IngvarSoloma\FeeMaster\Services;

use IngvarSoloma\FeeMaster\Models\Operation;
use IngvarSoloma\FeeMaster\Traits\HasRoundUpCommission;
use InvalidArgumentException;

abstract class CommissionStrategyCalculator implements CommissionCalculatorInterface
{
    use HasRoundUpCommission;

    final public function calculate(Operation $operation): float {
        return match ($operation->operationType) {
            Operation::TYPE_DEPOSIT => $this->calculateDepositCommission($operation),
            Operation::TYPE_WITHDRAW => $this->calculateWithdrawCommission($operation),
            default => throw new InvalidArgumentException('Invalid operation type'),
        };
    }

    protected function calculateDepositCommission(Operation $operation): float {
        return self::roundUpCommission($operation->amount * 0.003, 2);
    }
    abstract protected function calculateWithdrawCommission(Operation $operation): float;

}
