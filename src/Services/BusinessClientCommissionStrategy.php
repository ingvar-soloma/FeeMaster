<?php

namespace IngvarSoloma\FeeMaster\Services;

use IngvarSoloma\FeeMaster\Models\Operation;

class BusinessClientCommissionStrategy extends CommissionStrategyCalculator
{
    final protected function calculateWithdrawCommission(Operation $operation): float
    {
        return self::roundUpCommission($operation->amount * 0.005, 2);
    }
}
