<?php

namespace IngvarSoloma\FeeMaster\Models;

class Operation
{
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';

    public function __construct(
        public readonly string $date,
        public readonly int $userId,
        public readonly string $userType,
        public readonly string $operationType,
        public readonly float $amount,
        public readonly string $currency
    ) {}
}
