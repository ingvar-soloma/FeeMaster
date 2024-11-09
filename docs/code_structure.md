## Code Structure
### Directories
- `src/`: Contains the main application code.
- `tests/`: Contains the test cases.
- `vendor/`: Contains the Composer dependencies.

### Key Files
- `bootstrap.php`: Initializes the application and loads environment variables.
- `CommissionCalculator.php`: Main class for calculating commissions.
- `CommissionStrategyCalculator.php`: Abstract class for commission calculation strategies.
- `PrivateClientCommissionStrategy.php`: Strategy for calculating commissions for private clients.
- `BusinessClientCommissionStrategy.php`: Strategy for calculating commissions for business clients.

## Classes and Methods
### `CommissionCalculator`
- `calculate(Operation $operation): float`: Calculates the commission for a given operation.

### `PrivateClientCommissionStrategy`
- `calculateWithdrawCommission(Operation $operation): float`: Calculates the withdraw commission for private clients.

### `CommissionStrategyCalculator`
- `calculate(Operation $operation): float`: Calculates the commission based on the operation type.
- `calculateDepositCommission(Operation $operation): float`: Calculates the deposit commission.
- `calculateWithdrawCommission(Operation $operation): float`: Abstract method for calculating withdraw commission.

### `User`
- Constants `TYPE_PRIVATE` and `TYPE_BUSINESS`: Represent user types.