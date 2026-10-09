# Add a static "Coming Soon" landing page

## Plan Summary

- **What/why**: Adds a single static HTML "Coming Soon" landing page at `public/index.html` so the repository has a web-facing placeholder, as requested in issue #1.
- **Key decision**: Plain static HTML with inline CSS (no PHP, no JS, no external assets) because the issue asks for HTML and the repository has no web framework or asset pipeline to build on.
- **Most important file**: `public/index.html` (new). Supporting changes: `tests/LandingPageTest.php` (new), `Makefile`, `README.md`.
- **Priority/complexity**: Low priority, Simple.

## Source

Issue #1 — https://github.com/thabungm/testPhp/issues/1

Issue title: "landing page". Issue body: "Add a landing page with coming soon in html". Extra instruction given at planning time: keep it simple.

## Problem Statement

The repository is a headless PHP calculator fixture (`src/Calculator.php`, exercised by `tests/CalculatorTest.php`). It has no web entrypoint, no `public/` directory, and no HTML anywhere. Issue #1 asks for a landing page that says "Coming Soon", written in HTML.

### Planning Context

**Key Requirements Discussed:**

- The page is a static `.html` file, not a PHP script.
- It lives at `public/index.html`, the conventional PHP web root, so it can be served with PHP's built-in server (`php -S localhost:8000 -t public`).
- Content is minimal: a `<title>`, one "Coming Soon" heading, a one-line subtitle naming the project, and a small inline `<style>` block for centering. No JavaScript, no external stylesheets, fonts, or images.
- A tiny PHPUnit test guards the page so `make test` remains a meaningful regression gate for this change.
- `Makefile` gains a `serve` target and `README.md` documents how to view the page.

**Decisions Made:**

- **Static HTML over `public/index.php`**: the issue says "in html"; a PHP entrypoint adds nothing for a static placeholder and would need `make lint` extended to cover it.
- **`public/` subdirectory over repo root**: keeps the web root separate from source, tests, and tooling, and matches the standard PHP layout that `php -S -t public` expects.
- **Inline CSS over separate stylesheet**: one file is simpler to review and serve; the styling is a few lines.
- **PHPUnit test over no test**: this repository is a HubLaunch end-to-end fixture whose regression step runs `make test`; a file-existence and content test makes that step verify the change.

**Out of Scope:**

- Any backend, form handling, email capture, or analytics.
- Any build step, bundler, or CSS framework.
- Deployment configuration or hosting.
- Changes to `src/Calculator.php` or `tests/CalculatorTest.php`.

### Background & Context

**Current Behavior**:

- `README.md` states: "No web server, no CLI — logic lives in `src/Calculator.php`".
- There is no `public/` directory and no HTML file in the repository.
- `Makefile` has three targets: `install`, `lint`, `test`.

**Desired Behavior**:

- `public/index.html` exists and renders a centered "Coming Soon" message in a browser.
- `make serve` starts PHP's built-in server on `localhost:8000` with `public/` as the document root, and `http://localhost:8000/` shows the page.
- `make test` includes a passing `LandingPageTest`.
- `README.md` explains how to view the page.

## Detailed Requirements

### Functional Requirements

1. **Landing page**
   - File path: `public/index.html`.
   - Valid HTML5 document: `<!DOCTYPE html>`, `<html lang="en">`, `<meta charset="UTF-8">`, `<meta name="viewport" content="width=device-width, initial-scale=1">`.
   - `<title>` is `Coming Soon — test-php-calculator`.
   - Body contains exactly one `<h1>` with the text `Coming Soon` and one `<p>` subtitle with the text `test-php-calculator is under construction.`
   - Inline `<style>` centers the content vertically and horizontally using flexbox. No external resources of any kind.

2. **Test**
   - File path: `tests/LandingPageTest.php`, namespace `Calculator\Tests`, class `LandingPageTest extends TestCase`.
   - Asserts the file `public/index.html` exists (resolved relative to the test file via `__DIR__ . '/../public/index.html'`).
   - Asserts the file content contains the string `Coming Soon`.

3. **Tooling**
   - `Makefile`: add `serve` to the `.PHONY` list and a `serve` target that runs `php -S localhost:8000 -t public`.

4. **Documentation**
   - `README.md`: update the intro sentence so it no longer claims there is no web server, and add a `## Landing page` section describing `make serve` and the URL.

### Technical Requirements

- **Technology**: HTML5, CSS (inline), PHP ^8.1 built-in server for local viewing, PHPUnit ^10 for the test. No new Composer dependencies.
- **Location**: `public/index.html` (new), `tests/LandingPageTest.php` (new), `Makefile`, `README.md`.
- **Dependencies**: None beyond what `composer.json` already requires.
- **Constraints**: No JavaScript. No network requests when the page loads. The page must render correctly when opened directly from the filesystem as well as when served by `php -S`.

### Non-Functional Requirements

- **Performance**: Single HTML file under 2 KB. No external fetches.
- **Security**: Static content only; no user input, no server-side code, no secrets.
- **Backwards Compatibility**: Existing `Calculator` class and tests are untouched. `make lint` and `make test` keep working; `make test` gains two assertions.
- **Error Handling**: Not applicable to a static page. The test fails with PHPUnit's standard `assertFileExists` / `assertStringContainsString` messages if the file is missing or the text is absent.

## Proposed Solution

**High-level approach**: Create one static HTML file in a new `public/` directory, add a two-assertion PHPUnit test next to the existing calculator test, add a `make serve` convenience target, and document it in the README.

### Key Components

1. **`public/index.html`** (new)
   - The landing page itself. Full content is given verbatim in Implementation Steps, Phase 1.

2. **`tests/LandingPageTest.php`** (new)
   - Follows the structure of `tests/CalculatorTest.php` lines 1-8 (namespace `Calculator\Tests`, `use PHPUnit\Framework\TestCase`, class extends `TestCase`). Full content is given verbatim in Implementation Steps, Phase 2.

3. **`Makefile`**
   - Add `serve` target. Existing targets unchanged.

4. **`README.md`**
   - Correct the "no web server" claim and add a short viewing section.

### Files Likely to Change

- `public/index.html` — new file, the landing page.
- `tests/LandingPageTest.php` — new file, guards existence and content of the page.
- `Makefile` — add `serve` target and extend `.PHONY`.
- `README.md` — update intro and add `## Landing page` section.

### Code Patterns to Follow

- **For the test class**: Follow `tests/CalculatorTest.php` lines 1-15 (namespace, `use` statements, class declaration, `TestCase` base). PHPUnit discovers tests under `tests/` via the `make test` command `vendor/bin/phpunit tests`, so no configuration change is needed for the new class to run.
- **For the Makefile target**: Follow the existing `lint` and `test` targets in `Makefile` lines 6-10 (tab-indented recipe, one command per target).

**Anti-Patterns to Avoid:**

- Do not add a `phpunit.xml` or change Composer autoload settings; PSR-4 `Calculator\Tests\` → `tests/` already covers the new test class.
- Do not add `public/` to `make lint`; `php -l` is for PHP files and the page is HTML.
- Do not link to external fonts, CDNs, or scripts.

## Implementation Steps

### Phase 1: Landing page

- [ ] Create directory `public/`.
- [ ] Create `public/index.html` with exactly this content:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Coming Soon — test-php-calculator</title>
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
      background: #f6f7f9;
      color: #1f2933;
      text-align: center;
    }
    h1 {
      font-size: 3rem;
      margin: 0 0 0.5rem;
    }
    p {
      font-size: 1.125rem;
      margin: 0;
      color: #52606d;
    }
  </style>
</head>
<body>
  <main>
    <h1>Coming Soon</h1>
    <p>test-php-calculator is under construction.</p>
  </main>
</body>
</html>
```

### Phase 2: Test

- [ ] Create `tests/LandingPageTest.php` with exactly this content:

```php
<?php

namespace Calculator\Tests;

use PHPUnit\Framework\TestCase;

class LandingPageTest extends TestCase
{
    private const LANDING_PAGE = __DIR__ . '/../public/index.html';

    public function testLandingPageExists(): void
    {
        $this->assertFileExists(self::LANDING_PAGE);
    }

    public function testLandingPageSaysComingSoon(): void
    {
        $this->assertStringContainsString('Coming Soon', file_get_contents(self::LANDING_PAGE));
    }
}
```

### Phase 3: Tooling and docs

- [ ] In `Makefile`, change the first line from `.PHONY: install lint test` to `.PHONY: install lint test serve`, and append this target at the end of the file (recipe line must be tab-indented):

```makefile
serve:
	php -S localhost:8000 -t public
```

- [ ] In `README.md`, replace the intro sentence

  `A minimal, headless PHP calculator class (`add`, `subtract`, `multiply`, `divide`). No web server, no CLI — logic lives in `src/Calculator.php` and is exercised by `tests/CalculatorTest.php` (PHPUnit). Dependency management via Composer.`

  with

  `A minimal PHP calculator class (`add`, `subtract`, `multiply`, `divide`) plus a static "Coming Soon" landing page. Logic lives in `src/Calculator.php` and is exercised by `tests/CalculatorTest.php` (PHPUnit); the landing page is `public/index.html`. Dependency management via Composer.`

- [ ] In `README.md`, append this section after the existing `## Verification` section:

````markdown
## Landing page

A static "Coming Soon" page lives at `public/index.html`. To view it locally:

```bash
make serve   # php -S localhost:8000 -t public
```

Then open http://localhost:8000/ in a browser.
````

### Phase 4: Verify

- [ ] Run `make lint` — must pass (unchanged scope: `src/*.php`).
- [ ] Run `make test` — must pass, including the two new `LandingPageTest` tests.

<details>
<summary><b>Implementation Detail</b></summary>

### 6. Edge Cases & Considerations

#### Edge Cases to Handle

1. **`public/` directory does not exist when the test runs**: `assertFileExists` fails with a clear path in the message. This is the intended failure mode; no special handling.
2. **Page opened directly from the filesystem (`file://`)**: works, because there are no relative asset references.
3. **Port 8000 already in use when running `make serve`**: PHP's built-in server prints `Failed to listen on localhost:8000` and exits. The user picks another port by running `php -S localhost:<port> -t public` directly. No change to the Makefile for this.

#### Potential Challenges

- ⚠️ **Makefile tab indentation**: the `serve` recipe line must start with a literal tab character, not spaces, or `make` reports `*** missing separator`. Match the existing `lint` and `test` recipes.
- ⚠️ **Nested code fence in README**: the `## Landing page` section contains a `bash` fenced block. When editing, ensure the fence is closed so the following content is not swallowed.

#### Security Considerations

- Static content only. No input handling, no server-side execution, no credentials.
- `php -S` is a development server; the README does not suggest using it for production.

### 7. Technical Considerations

#### Dependencies

- None added. PHP ^8.1 and phpunit ^10 are already required by `composer.json`.

#### Configuration Changes

- None. No `phpunit.xml`, no Composer autoload changes, no `.hublaunch/hublaunch.config.js` changes.

#### Environment Variables

- None.

#### Error Handling Strategies

- Not applicable to a static page. Test failures use PHPUnit's default assertion messages.

### 8. Testing Requirements

#### Unit Tests

- [ ] `LandingPageTest::testLandingPageExists` — `public/index.html` exists.
- [ ] `LandingPageTest::testLandingPageSaysComingSoon` — file content contains `Coming Soon`.

#### Integration Tests

- None. There is no application code interacting with the page.

#### Manual Testing Checklist

1. **Setup**: `make install` (installs PHPUnit via Composer).
2. **Serve**: run `make serve`, open http://localhost:8000/ in a browser.
   - Expected result: a centered "Coming Soon" heading with the subtitle "test-php-calculator is under construction." on a light grey background. No console errors, no network requests other than the page itself.
3. **Direct open**: open `public/index.html` from the filesystem.
   - Expected result: identical rendering.
4. **Tests**: run `make test`.
   - Expected result: all tests pass, including two `LandingPageTest` tests.

#### Test Data Requirements

- None.

### 9. Documentation Updates

#### User-Facing Documentation

- [ ] `README.md`: intro sentence updated and `## Landing page` section added (exact text in Implementation Steps, Phase 3).

#### Code Documentation

- [ ] None required. The HTML and the test are self-explanatory.

### 10. Acceptance Criteria

- [ ] **AC1**: `public/index.html` exists with the exact content given in Implementation Steps, Phase 1.
- [ ] **AC2**: Opening the page in a browser shows an `<h1>` reading "Coming Soon" and a subtitle "test-php-calculator is under construction.", centered on the viewport.
- [ ] **AC3**: `tests/LandingPageTest.php` exists with the exact content given in Implementation Steps, Phase 2, and both of its tests pass under `make test`.
- [ ] **AC4**: `make serve` runs `php -S localhost:8000 -t public` and serves the page at http://localhost:8000/.
- [ ] **AC5**: `README.md` no longer says there is no web server and contains the `## Landing page` section.
- [ ] **AC6**: `make lint` passes.
- [ ] **AC7**: `src/Calculator.php` and `tests/CalculatorTest.php` are unchanged.

#### Definition of Done

- All acceptance criteria met.
- `make lint` and `make test` pass.
- No files outside `public/index.html`, `tests/LandingPageTest.php`, `Makefile`, and `README.md` are modified.

### 11. Dependencies & Related Work

#### Dependencies

- [ ] Depends on: nothing.
- [ ] Required external setup: none.

#### Blockers

- [ ] None.

#### Related Issues/PRs

- Fixes #1

</details>
