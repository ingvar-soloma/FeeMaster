<?php

require_once(__DIR__ . '/bootstrap.php');

use IngvarSoloma\FeeMaster\Models\Operation;
use IngvarSoloma\FeeMaster\Services\CommissionCalculator;
use IngvarSoloma\FeeMaster\Utils\CurrencyConverter;

function readOperationsFromCsv(string $filePath): \Generator
{
    if (($handle = fopen($filePath, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            yield new Operation($data[0], (int)$data[1], $data[2], $data[3], (float)$data[4], $data[5]);
        }
        fclose($handle);
    }
}

function main(string $csvFilePath): void
{
    $operations = readOperationsFromCsv($csvFilePath);

    $currencyConverter = CurrencyConverter::getInstance();
    $calculator = new CommissionCalculator($currencyConverter);

    foreach ($operations as $operation) {
        $commission = $calculator->calculate($operation);
        echo $commission . PHP_EOL;
    }
}

main(__DIR__ . '/operations.csv');