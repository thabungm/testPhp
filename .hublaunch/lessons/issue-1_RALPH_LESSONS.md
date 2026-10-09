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

## 2026-10-09 Re-check #5 (found the real source of the stale snapshot)
- Same report again (line number now 3166, was 2703 before — confirms each report comes
  from a freshly re-uploaded `ralph-run.sh`, not a cached script).
- Traced `get_ralph_commands`/`run_regression_step` in `ralph-run.sh`: they read
  `RALPH_MD_ABS = realpath("./ralph.md")` — i.e. THIS repo's real, already-fixed
  `ralph.md`. Confirmed its `RALPH_CHECK_COMMANDS`/`RALPH_REGRESSION_COMMANDS` blocks
  still contain the direct commands (no `make`), exactly as fixed in `7304946`.
- Found the actual stale artifact: `.hublaunch/ralph.effective.md`. It's a worker-bundled
  file ("maintained by the Hula team... Do not edit directly in user repositories") that
  concatenates the harness system prompt with a SNAPSHOT of ralph.md's repo-supplied
  section taken at container build time — and that snapshot still has `make lint` /
  `make test`. BUT: grepped `ralph-run.sh` and confirmed this file is never read by
  `get_ralph_commands`/`run_regression_step`/`run_check_step` — it's excluded from git
  (`.hublaunch/ralph.effective.md` is added to `.git/info/exclude`) and is purely
  informational context bundled for the agent, not a command source. So it is NOT the
  cause of the `make: command not found` error and must NOT be edited (per its own
  "do not edit" notice) — editing it would not fix anything and violates the file's
  stated ownership.
- Re-verified directly: `which make` → absent (confirmed). Lint via `find src -name
  "*.php" -print0 | xargs -0 -n1 php -l` → clean. `vendor/bin/phpunit tests` → 7/7 pass.
  `git status --porcelain` → clean, no code changes needed.
- Updated conclusion: the `make: command not found` line comes from somewhere in the
  HubLaunch worker's OWN invocation path (outside this repo's `ralph.md`/`ralph-run.sh`
  command-extraction logic verified above) — possibly a harness-side step not driven by
  `RALPH_CHECK_COMMANDS`/`RALPH_REGRESSION_COMMANDS` at all, or leftover from an older
  cached run. There is nothing left to fix inside this repository; every command path
  this repo controls (`ralph.md`, `Makefile`, `src/`, `tests/`) is already make-free
  where it matters and passes cleanly. Future occurrences of this identical message
  should be treated as a harness/worker-side issue, not a repo regression, unless the
  harness's own source (not available in this repo) is inspectable.

## 2026-10-09 Re-check #6 (code-level proof it can't come from ralph.md path)
- Same report again, empty `FAILED REGRESSION OUTPUT` block, `FULL OUTPUT` just the one
  `make: command not found` line (line number drifts each time — 2703 → 3166 → ...,
  confirming it's a freshly fetched `ralph-run.sh` each run, not a cached copy).
- This time traced the actual bash logic instead of just re-running the test suite:
  `get_ralph_commands()` (ralph-run.sh ~2680) extracts commands by `sed`-slicing between
  the `<!-- RALPH_*_COMMANDS` / `_END -->` fences in `$RALPH_MD_ABS` (= `realpath
  ./ralph.md`) and `_run_command_block()` (~2699) just `eval`s each resolved line — there
  is NO hardcoded `make` anywhere in that path, and `grep -n make ralph-run.sh` finds only
  comments (e.g. "makes the cap win", "makes them exit 1"), never an invoked command.
  `ralph.md`'s `RALPH_CHECK_COMMANDS`/`RALPH_REGRESSION_COMMANDS` blocks (fixed in
  `7304946`) still contain only `php -l` / `vendor/bin/phpunit tests` — no `make`. So by
  construction this repo's check/regression steps cannot be the source of a `make`
  invocation; the string must originate from a harness/worker-side step this repo's
  source doesn't contain (e.g. a pre-flight or setup step outside `ralph.md`'s command
  blocks).
- Direct re-verification, unchanged result: `which make` → not found; lint
  (`find src -name "*.php" -print0 | xargs -0 -n1 php -l`) → clean; `vendor/bin/phpunit
  tests` → 7/7 pass, 7 assertions, OK; `git status --porcelain` → clean.
- No repo change made (6th time this exact non-actionable report has recurred). If seen
  again, this is almost certainly not fixable from inside the repo — only from the
  HubLaunch worker/harness side that invokes `ralph-run.sh`.

## 2026-10-09 Re-check #7
- Identical recurrence of the same non-actionable `make: command not found` report
  (empty `FAILED REGRESSION OUTPUT`, single-line `FULL OUTPUT`). Same as re-checks
  #1-6.
- Re-verified from scratch: `which make` → absent. `ralph.md`'s
  `RALPH_CHECK_COMMANDS`/`RALPH_REGRESSION_COMMANDS` fences still contain only the
  direct `php -l` / `vendor/bin/phpunit tests` commands (fixed in `7304946`), no
  `make`. Lint clean, `vendor/bin/phpunit tests` → 7/7 pass, 7 assertions. `git status
  --porcelain` → clean. No code changes made.
- Standing conclusion unchanged: this repo's command paths are make-free and passing;
  the `make` invocation originates outside this repo's control (harness/worker side).
