<?php

namespace IngvarSoloma\FeeMaster\Services;

use IngvarSoloma\FeeMaster\Models\Operation;

interface CommissionCalculatorInterface
{
    public function calculate(Operation $operation): float;
}
