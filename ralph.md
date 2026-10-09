# test-php-calculator

Minimal headless PHP calculator module (`src/Calculator.php` with `add`, `subtract`, `multiply`, `divide`), used as an end-to-end test fixture for HubLaunch's PHP sandbox support. PHP ^8.1, dependency management via Composer, tests via PHPUnit ^10.0.

## Setup

Install dependencies:

    make install

## Verification

Run `make lint` to syntax-check every file in `src/` with `php -l`, and `make test` to run the PHPUnit test suite in `tests/`. There is no build step — this is a pure PHP library with no compilation.

<!-- RALPH_CHECK_COMMANDS
find src -name "*.php" -print0 | xargs -0 -n1 php -l
RALPH_CHECK_COMMANDS_END -->

<!-- RALPH_BUILD_COMMANDS
RALPH_BUILD_COMMANDS_END -->

<!-- RALPH_REGRESSION_COMMANDS
vendor/bin/phpunit tests
RALPH_REGRESSION_COMMANDS_END -->
