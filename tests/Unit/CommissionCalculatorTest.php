<?php

namespace IngvarSoloma\FeeMaster\Tests\Unit;

use IngvarSoloma\FeeMaster\Models\Operation;
use IngvarSoloma\FeeMaster\Models\User;
use IngvarSoloma\FeeMaster\Services\CommissionCalculator;
use IngvarSoloma\FeeMaster\Utils\CurrencyConverter;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    private CommissionCalculator $calculator;

    final public function commissionDataProvider(): array
    {
        return [
            'deposit commission' => [
                new Operation('2024-11-07', 1, User::TYPE_PRIVATE, Operation::TYPE_DEPOSIT, 1000.00, 'EUR'),
                3
            ],
            'withdraw commission for private client within free limit' => [
                new Operation('2024-11-07', 1, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 500.00, 'EUR'),
                0.00
            ],
            'withdraw commission for private client exceeding free limit' => [
                new Operation('2024-11-07', 1, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 1500.00, 'EUR'),
                1.50
            ],
            'withdraw commission for business client' => [
                new Operation('2024-11-07', 2, User::TYPE_BUSINESS, Operation::TYPE_WITHDRAW, 1000.00, 'EUR'),
                5.00
            ],
            'withdraw commission in other currency' => [
                new Operation('2024-11-07', 1, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 1500.00, 'USD'),
                1.09
            ],
        ];
    }

    final public function allCommissionDataProvider(): array
    {
        $data = [
            ['2014-12-31', 4, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 1200.00, 'EUR', 0.60],
            ['2015-01-01', 4, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 1000.00, 'EUR', 3],
            ['2016-01-05', 4, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 1000.00, 'EUR', 0],
            ['2016-01-05', 1, User::TYPE_PRIVATE, Operation::TYPE_DEPOSIT, 200.00, 'EUR', 0.6],  // 0.06 is incorrect
            ['2016-01-06', 2, User::TYPE_BUSINESS, Operation::TYPE_WITHDRAW, 300.00, 'EUR', 1.5],
            ['2016-01-06', 1, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 30000, 'JPY', 0],
            ['2016-01-07', 1, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 1000.00, 'EUR', 0.7],
            ['2016-01-07', 1, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 100.00, 'USD', 0.3],
            ['2016-01-10', 1, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 100.00, 'EUR', 0.3],
            ['2016-01-10', 2, User::TYPE_BUSINESS, Operation::TYPE_DEPOSIT, 10000.00, 'EUR', 30], // 3 is incorrect
            ['2016-01-10', 3, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 1000.00, 'EUR', 0],
            ['2016-02-15', 1, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 300.00, 'EUR', 0],
            ['2016-02-19', 5, User::TYPE_PRIVATE, Operation::TYPE_WITHDRAW, 3000000, 'JPY', 8613]  // 8612 do not match my rate
        ];

        return array_map(fn($value) => [new Operation(...array_slice($value, 0, 6)), $value[6]], $data);
    }

    /**
     * @dataProvider commissionDataProvider
     */
    final public function testCalculateCommission(Operation $operation, float $expectedCommission): void
    {
        $commission = $this->calculator->calculate($operation);
        $this->assertEquals($expectedCommission, $commission);
    }

    final public function testCalculateCommissionForAllTransactions(): void
    {
        $data = $this->allCommissionDataProvider();

        foreach ($data as [$operation, $expectedCommission]) {
            $commission = $this->calculator->calculate($operation);
            $this->assertEquals(
                $expectedCommission,
                $commission,
                print_r($operation, true)
            );
        }
    }

    final protected function setUp(): void
    {
        $currencyConverter = $this->createMock(CurrencyConverter::class);
        $currencyConverter->method('convertToBaseCurrency')->willReturnCallback(function ($amount, $currency) {
            $rates = ['USD' => 1.1, 'JPY' => 129,33];
            return $currency === 'EUR' ? $amount : $amount / $rates[$currency];
        });

        $currencyConverter->method('convertFromBaseCurrency')->willReturnCallback(function ($amount, $currency) {
            $rates = ['USD' => 1.1, 'JPY' => 129,33];
            return $currency === 'EUR' ? $amount : $amount * $rates[$currency];
        });
        $this->calculator = new CommissionCalculator($currencyConverter);
    }
}
