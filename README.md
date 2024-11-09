# FeeMaster

## Table of Contents
- [Overview](docs/overview.md)
- [Installation](docs/installation.md)
- [Usage](docs/usage.md)
- [Code Structure](docs/code_structure.md)
- [Environment Variables](docs/environment_variables.md)
- [Example](docs/example.md)
- [License](docs/license.md)

## Commission Rules

The commission rules for private clients are as follows:

1. **Free Withdraw Limit**: Each private client can withdraw up to 1000.00 EUR per week without any commission.
2. **Free Withdraw Count**: Each private client can make up to 3 free withdrawals per week.
3. **Commission Rate**: If the withdrawal amount exceeds the free limit or the number of withdrawals exceeds the free count, a commission of 0.3% (0.003) is applied to the excess amount.
4. **Currency Conversion**: All operations are converted to a base currency (EUR) for the purpose of calculating the commission. The commission is then converted back to the original currency of the operation.
5. **Rounding**: The commission amount is rounded up to the nearest cent (2 decimal places).

These rules are implemented in the `PrivateClientCommissionStrategy` class.