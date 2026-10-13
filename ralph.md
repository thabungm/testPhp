# test-php-calculator

A minimal, headless PHP calculator module (PHP ^8.1) used as an end-to-end test fixture for HubLaunch's PHP sandbox support. Logic lives in `src/Calculator.php` (add, subtract, multiply, divide) with no web server or CLI entry point. Dependencies are managed via Composer, and tests use PHPUnit 10.

## Setup

Install dependencies:

    make install

## Verification

Run `make lint` to syntax-check every PHP file in `src/`, and `make test` to run the PHPUnit test suite in `tests/`.

<!-- RALPH_CHECK_COMMANDS
make lint
RALPH_CHECK_COMMANDS_END -->

<!-- RALPH_BUILD_COMMANDS
RALPH_BUILD_COMMANDS_END -->

<!-- RALPH_REGRESSION_COMMANDS
make test
RALPH_REGRESSION_COMMANDS_END -->
