# test-php-calculator

A minimal, headless PHP calculator class (`add`, `subtract`, `multiply`, `divide`). No web server, no CLI — logic lives in `src/Calculator.php` and is exercised by `tests/CalculatorTest.php` (PHPUnit). Dependency management via Composer.

## Setup

```bash
make install
```

## Verification

```bash
make lint   # php -l syntax check on every file in src/
make test   # vendor/bin/phpunit tests
```
