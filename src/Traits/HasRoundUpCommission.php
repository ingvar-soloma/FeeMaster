<?php

namespace IngvarSoloma\FeeMaster\Traits;

trait HasRoundUpCommission
{
    protected function roundUpCommission(float $amount, int $exponent): float
    {
        $factor = pow(10, $exponent);
        return ceil($amount * $factor) / $factor;
    }
}
