# Add a `power` Method to the Calculator Class

## Plan Summary

- **What/why**: End-to-end regression probe for HubLaunch's hosted PHP sandbox support (ondrej/php PHP 8.3-cli, Composer, PHPUnit). Adds one small, real method to a genuinely bare repo (no committed `ralph.md`) so the launch exercises the full bare-repo detection pipeline for real.
- **Key decision**: Trivial, single-method addition — the launch pipeline itself is what's under test, not the change.
- **Most important file**: `src/Calculator.php`.
- **Priority/complexity**: Low priority, Simple.

## Problem Statement

`Calculator::add/subtract/multiply/divide` exist but there's no exponentiation. Add a `power(float $base, float $exponent): float` method, with a corresponding test in `tests/CalculatorTest.php`.

## Proposed Solution

Add `power(float $base, float $exponent): float` to `src/Calculator.php`, implemented as `return $base ** $exponent;`. Add `testPower()` to `tests/CalculatorTest.php` asserting `$this->calculator->power(2, 3) === 8.0`.

## Implementation Steps

- [ ] Add `power(float $base, float $exponent): float` to `src/Calculator.php`, following the same style as the existing `add`/`subtract`/`multiply`/`divide` methods (public method, type hints on params and return).
- [ ] Add `testPower(): void` to `tests/CalculatorTest.php`, following the same style as the existing test methods, asserting `$this->assertSame(8.0, $this->calculator->power(2, 3));`.

## Acceptance Criteria

- [ ] `src/Calculator.php` has a `power` method matching the signature above.
- [ ] `tests/CalculatorTest.php` has a passing `testPower` test.
- [ ] `make lint` (php -l) and `make test` (phpunit) both pass.
- [ ] No other file is changed.
