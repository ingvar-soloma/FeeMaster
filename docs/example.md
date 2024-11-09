## Example
```php
use IngvarSoloma\FeeMaster\Models\Operation;
use IngvarSoloma\FeeMaster\Services\CommissionCalculator;
use IngvarSoloma\FeeMaster\Services\CurrencyConverter;

$operation = new Operation('2024-11-07', 1, User::TYPE_PRIVATE, Operation::TYPE_DEPOSIT, 1000.00, 'EUR');
$currencyConverter = CurrencyConverter::getInstance();
$calculator = new CommissionCalculator($currencyConverter);
$commission = $calculator->calculate($operation);

echo "Commission: $commission";
```