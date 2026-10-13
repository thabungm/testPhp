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

## Product listing page

`public/index.html` is a standalone static product listing page (self-contained HTML5 with embedded CSS, no PHP, no JavaScript, no external resources). It is an optional static asset and does not change the headless calculator library. View it by opening the file directly in a browser, or serve it locally:

```bash
php -S localhost:8000 -t public   # then visit http://localhost:8000/
```
