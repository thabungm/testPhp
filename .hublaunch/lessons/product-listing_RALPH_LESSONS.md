# HubLaunch Lessons Learned

This file persists context across agent sessions. Update it as you work.

## Current Status
- Phase: COMPLETE
- Last action: Implemented public/index.html + README note; all checks pass
- Blockers: None

## Key Discoveries
- `make` is NOT installed in this container. Run the Makefile targets directly:
  - lint: `find src -name "*.php" -print0 | xargs -0 -n1 php -l`
  - test: `vendor/bin/phpunit tests` (run `composer install` first; vendor/ absent initially)
- php 8.3 and composer available at /usr/bin/php and /usr/local/bin/composer.

## Solutions That Worked
- Static single-file deliverable per plan: public/index.html with embedded CSS, no PHP/JS/external resources.
- Verified well-formedness via PHP DOMDocument (0 fatal parse errors).
- Verified served bytes identical to source via `php -S localhost:8000 -t public` + curl diff.

## Things to Avoid
- Don't call `make` — not present.

## Files Modified
- public/index.html (new) — product listing page, 6 products (name + price only)
- README.md — added "Product listing page" section

## Open Questions
- None

## Next Steps
- None — plan 100% complete, lint + test green.
