<?php

namespace IngvarSoloma\FeeMaster\Services;

use IngvarSoloma\FeeMaster\Models\Operation;
use IngvarSoloma\FeeMaster\Models\User;
use IngvarSoloma\FeeMaster\Utils\CurrencyConverterInterface;
use InvalidArgumentException;

class CommissionCalculator implements CommissionCalculatorInterface
{
    /**
     * @var \IngvarSoloma\FeeMaster\Services\PrivateClientCommissionStrategy
     */
    private readonly PrivateClientCommissionStrategy $privateClientStrategy;

    public function __construct(private CurrencyConverterInterface $converter) {
        $this->privateClientStrategy = new PrivateClientCommissionStrategy($this->converter);
    }

    final public function calculate(Operation $operation): float
    {
        $strategy = $this->getStrategy($operation->userType);
        return $strategy->calculate($operation);
    }

    private function getStrategy(string $userType): CommissionCalculatorInterface
    {
        return match ($userType) {
            User::TYPE_PRIVATE => $this->privateClientStrategy,
            User::TYPE_BUSINESS => new BusinessClientCommissionStrategy(),
            default => throw new InvalidArgumentException('Invalid user type'),
        };
    }
}
