# HubLaunch Lessons Learned

This file persists context across agent sessions. Update it as you work.

## Current Status
- Phase: COMPLETE
- Last action: Implemented coming-soon landing page plan; lint + tests pass; serve verified.
- Blockers: None

## Key Discoveries
- `make` is NOT installed in this container. Run the recipe commands directly instead:
  - lint:  `find src -name "*.php" -print0 | xargs -0 -n1 php -l`
  - test:  `vendor/bin/phpunit tests`
  - serve: `php -S localhost:8000 -t public`
- PHP 8.3.33 at /usr/bin/php; composer at /usr/local/bin/composer.
- `composer install` creates composer.lock (previously untracked). Left it OUT of the
  feature commit to respect the plan's "only 4 files" scope.

## Solutions That Worked
- Created all 4 files/edits verbatim from the plan. All tests (7: 5 calc + 2 landing) pass.
- Verified serve: curl http://localhost:8000/ returns the Coming Soon page correctly.

## Things to Avoid
- Don't try to run `make` — it's absent. Use raw commands.

## Files Modified
- public/index.html (new) — landing page
- tests/LandingPageTest.php (new) — 2 assertions
- Makefile — added `serve` target + .PHONY
- README.md — updated intro + added `## Landing page` section

## Open Questions
- None.

## Next Steps
- Done. Nothing outstanding.
