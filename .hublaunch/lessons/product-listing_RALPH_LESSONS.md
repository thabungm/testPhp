# HubLaunch Lessons Learned

This file persists context across agent sessions. Update it as you work.

## Current Status
- Phase: COMPLETE
- Last action: Implemented public/index.html + README note; all checks pass
- Blockers: None

## Key Discoveries
- Regression run on 2026-10-13 failed with `make: command not found` because
  `make` was not installed in that container, NOT because of a code
  regression. The regression harness (ralph-run.sh) invokes `make` directly,
  so a missing binary surfaces as a full suite failure.
  Fix: `sudo apt-get update && sudo apt-get install -y make` — the package
  was available in apt, just not installed. After install, `make lint` and
  `make test` both pass cleanly (5/5 phpunit tests, 0 lint errors).
- If `make` is ever missing again and can't be installed, Makefile targets
  can be run directly:
  - lint: `find src -name "*.php" -print0 | xargs -0 -n1 php -l`
  - test: `vendor/bin/phpunit tests` (run `composer install` first; vendor/ absent initially)
- php 8.3 and composer available at /usr/bin/php and /usr/local/bin/composer.

## Solutions That Worked
- Static single-file deliverable per plan: public/index.html with embedded CSS, no PHP/JS/external resources.
- Verified well-formedness via PHP DOMDocument (0 fatal parse errors).
- Verified served bytes identical to source via `php -S localhost:8000 -t public` + curl diff.
- Installing `make` via apt resolved the "regression" — no source changes were needed.

## Things to Avoid
- Don't assume a missing-`make` failure means the code regressed — check
  `which make` first. Try installing it (`sudo apt-get install -y make`)
  before falling back to running the underlying lint/test commands manually.

## Files Modified
- public/index.html (new) — product listing page, 6 products (name + price only)
- README.md — added "Product listing page" section

## Open Questions
- None

## Next Steps
- None — plan 100% complete, lint + test green.
