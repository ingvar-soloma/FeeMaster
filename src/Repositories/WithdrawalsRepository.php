<?php

namespace IngvarSoloma\FeeMaster\Repositories;

use IngvarSoloma\FeeMaster\Models\Withdrawals;

interface WithdrawalsRepository
{
    public function getWithdrawals(int $userId, string $operationDate): Withdrawals;
    public function saveWithdrawals(int $userId, string $operationDate, Withdrawals $withdrawals): void;
}