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

## 2026-10-09 Re-check
- Reported error was just `ralph-run.sh: line 2703: make: command not found` — not an actual
  lint/code error, just the harness trying to invoke the absent `make` binary.
- Ran the real lint recipe directly (`find src -name "*.php" -print0 | xargs -0 -n1 php -l`):
  zero errors/warnings. Also ran `vendor/bin/phpunit tests`: 7/7 pass.
- No code changes were needed. If this keeps recurring, the fix belongs in ralph-run.sh /
  CI config (install `make` or call the underlying commands directly), not in this repo's code.

## 2026-10-09 Re-check #2
- Identical recurrence: `make` still not installed (`which make` → not found). Confirmed this
  is purely an environment gap, not a regression — ralph-run.sh calls `make lint` without
  checking for the binary first.
- Direct lint + test rerun: same zero-error, 7/7-pass result as before. No src/public/tests
  changes made this pass either.

## 2026-10-09 Root-cause fix (3rd occurrence)
- Confirmed `make` is not installable in this sandbox either (`apt-get install make` →
  "Package 'make' has no installation candidate"). This container will never have `make`,
  so re-verifying each time just wastes a cycle.
- Real fix: the `RALPH_CHECK_COMMANDS` / `RALPH_REGRESSION_COMMANDS` blocks in `ralph.md`
  (which the harness actually `eval`s) were hardcoded to `make lint` / `make test`. Changed
  them to call the underlying commands directly:
  - `RALPH_CHECK_COMMANDS`: `find src -name "*.php" -print0 | xargs -0 -n1 php -l`
  - `RALPH_REGRESSION_COMMANDS`: `vendor/bin/phpunit tests`
  The human-readable `## Setup` / `## Verification` prose and the `Makefile` itself are left
  alone — `make install/lint/test/serve` still works for humans with `make` on PATH; only the
  automated harness blocks were switched to not depend on it.
- Re-ran both new commands directly: lint clean, 7/7 tests pass. No src/public/tests changes.
- If `make` ever IS available in this environment and this needs to change back, confirm
  `which make` succeeds before reverting.

## 2026-10-09 Re-check #4 (post-fix verification)
- This regression report (`make: command not found` at an `eval "$CMD"` line inside
  ralph-run.sh's regression step) is the SAME stale signal as the prior 3 occurrences —
  it was already root-caused and fixed in commit `7304946` by rewriting the
  `RALPH_CHECK_COMMANDS` / `RALPH_REGRESSION_COMMANDS` blocks in `ralph.md` to call
  `php -l` / `vendor/bin/phpunit tests` directly instead of `make lint` / `make test`.
- Verified current `ralph.md` state: the command blocks already contain the direct
  commands (no `make` inside the `<!-- RALPH_*_COMMANDS -->` fences). Only the
  human-facing `## Setup`/`## Verification` prose and `Makefile` still mention `make`,
  which is intentional (for humans with `make` on PATH) and not read by the harness.
- Re-ran both commands directly: lint clean (`No syntax errors detected in
  src/Calculator.php`), `vendor/bin/phpunit tests` → 7/7 tests, 7 assertions, OK.
  `git status` clean — no code changes were needed.
- Conclusion: no regression exists in the repo. If this exact report keeps recurring,
  the fix already landed in this repo's `ralph.md`; any further recurrence points to the
  harness re-running against a pre-fix snapshot/cache rather than this branch's HEAD.
