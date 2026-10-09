# test-php-calculator

A minimal PHP calculator class (`add`, `subtract`, `multiply`, `divide`) plus a static "Coming Soon" landing page. Logic lives in `src/Calculator.php` and is exercised by `tests/CalculatorTest.php` (PHPUnit); the landing page is `public/index.html`. Dependency management via Composer.

## Setup

```bash
make install
```

## Verification

```bash
make lint   # php -l syntax check on every file in src/
make test   # vendor/bin/phpunit tests
```

## Landing page

A static "Coming Soon" page lives at `public/index.html`. To view it locally:

```bash
make serve   # php -S localhost:8000 -t public
```

Then open http://localhost:8000/ in a browser.
