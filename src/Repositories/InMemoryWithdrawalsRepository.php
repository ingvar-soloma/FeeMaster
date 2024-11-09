<?php

namespace IngvarSoloma\FeeMaster\Repositories;

use DateTime;
use IngvarSoloma\FeeMaster\Models\Withdrawals;

class InMemoryWithdrawalsRepository implements WithdrawalsRepository
{
    private array $storage = [];

    final public function getWithdrawals(int $userId, string $operationDate): Withdrawals
    {
        $weekNumber = $this->getWeekNumber($operationDate);
        return $this->storage[$userId][$weekNumber] ?? new Withdrawals();
    }

    final public function saveWithdrawals(int $userId, string $operationDate, Withdrawals $withdrawals): void
    {
        $weekNumber = $this->getWeekNumber($operationDate);
        $this->storage[$userId][$weekNumber] = $withdrawals;
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
