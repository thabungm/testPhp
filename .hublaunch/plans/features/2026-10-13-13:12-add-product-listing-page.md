# Add a Simple Static Product Listing Page for the E‑commerce Landing

## Plan Summary

- **What / why:** Add a single, self-contained static HTML product listing page for an e-commerce landing page. The repo is a deliberately headless PHP calculator fixture with no web server, framework, or frontend assets, so the page is delivered as pure static HTML with embedded CSS — nothing in the existing PHP is touched.
- **Key decisions:** (1) Static HTML + embedded CSS, **no PHP** (per the delivery-form decision); (2) product data **hard-coded directly in the HTML markup** (no data layer, no database, no build step); (3) **minimal** content — each product shows **name + price** only, laid out as a simple grid/list; (4) styling is a **small block of embedded CSS**, zero external dependencies/CDNs.
- **Most important file:** `public/index.html` (new) — the entire deliverable.
- **Priority / complexity:** Low complexity, additive-only change. No new Composer dependencies, no changes to `composer.json`, `src/`, `tests/`, or the `Makefile`.
- **Verification:** `php -l public/... ` is not applicable (HTML, not PHP); the page is validated by opening it in a browser (optionally via `php -S localhost:8000 -t public`). The existing `make lint` / `make test` must continue to pass unchanged.

## Problem Statement

The project (`thabungm/testPhp`) is a minimal, intentionally **headless** PHP calculator used as a HubLaunch test fixture. Its README states plainly: *"No web server, no CLI — logic lives in `src/Calculator.php`."* The only application code is `src/Calculator.php` (a `Calculator\Calculator` class) and its PHPUnit test `tests/CalculatorTest.php`. There is **no** HTTP entry point, router, template engine, data model, database, or any frontend asset (CSS/JS/images) anywhere in the tree. Composer requires only `php: ^8.1` and `phpunit/phpunit: ^10.0`.

The request is to add a **simple product listing page suitable for an e-commerce landing page**, with an explicit instruction to **"keep it simple."**

Because the shape of the deliverable cannot be inferred from a headless library, the key decisions below were settled before planning.

#### Planning Context

**Key requirements**
- A product listing page suitable for an e-commerce landing page, rendering a collection of products.
- "Keep it simple" is a hard constraint: minimal content, minimal tooling, no heavyweight framework.

**Decisions made (with rationale)**
- **Delivery form → a single static HTML (+ embedded CSS) file, no PHP.** The repo has no web server, router, or template engine (README: "No web server, no CLI"), and introducing any of those would contradict "keep it simple" and the repo's deliberately minimal, zero-web nature. A static file is the lowest-friction deliverable that is independently viewable.
- **Product data → hard-coded directly in the static HTML.** No database, API, config, or data model exists, and none is implied. Since the chosen delivery form is "no PHP," the products are written directly as HTML markup (rather than a PHP array), keeping the page fully self-contained. *(See Assumptions — this reconciles the "hard-coded/mock products" data decision with the "no PHP" delivery decision.)*
- **Content → minimal: name + price only**, presented as a simple list/grid of product cards. No image/placeholder, description, hero/header/footer chrome, or interactive elements (no "add to cart"), consistent with "keep it simple."
- **Styling → a small block of embedded CSS, no external dependency.** The repo has zero frontend assets or build tooling and no established styling approach; embedding a little CSS avoids a CDN dependency and a second served file, keeping the deliverable to one self-contained file.

**Out of scope**
- Any server-side rendering, routing, or PHP involvement in the page.
- A data layer: database, external API, JSON file, or `Product` class/repository.
- Richer UX: product images, descriptions, search, filtering, pagination, cart, checkout, admin, landing-page chrome (hero/banner).
- New Composer dependencies, framework (Laravel/Symfony/Slim), CSS framework (Tailwind/Bootstrap), or build tooling.
- Changes to `src/Calculator.php`, `tests/`, `composer.json`, or the `Makefile`.
- Production deployment/hosting setup.

## Detailed Requirements

**Functional**
- A single page displays a heading (e.g. the store name) and a collection of **at least 4–6** mock products.
- Each product displays its **name** and **price** (formatted with a currency symbol, e.g. `$19.99`).
- Products are arranged in a simple, readable grid or list layout.
- The page is valid, well-formed HTML5 and renders correctly by opening the file directly in a browser (no server required) and when served via a static server.

**Technical**
- Pure static HTML5 with a single embedded `<style>` block in the `<head>`. No JavaScript required. No external resources (no CDN links, web fonts, or remote images).
- Product data is hard-coded as HTML markup within the page.
- File lives under a new `public/` directory so it is cleanly separable from library code and servable with `php -S <host> -t public`.
- Must not require or modify Composer autoloading; must not affect `make lint` (which scans only `*.php` files under `src/`) or `make test`.

**Non-Functional**
- Simplicity first: one file, no dependencies, no build step.
- Accessible, semantic markup: a single `<h1>`, a `lang` attribute, and a `<meta name="viewport">` for basic responsiveness.
- Self-contained and portable: the file works with no network access.

## Proposed Solution

Create one new file, `public/index.html`, containing a complete HTML5 document:

- `<head>` with `charset`, `viewport`, a `<title>`, and a single embedded `<style>` block providing a lightweight responsive card grid (CSS `grid` or `flex`), basic typography, and spacing.
- `<body>` with an `<h1>` store heading and a product grid. Each product is a small card (`<article>` or `<li>`) containing the product **name** and **price**.
- Products are written inline as static markup (4–6 sample items).

No other files are created or modified. This is an additive, single-file change — no ASCII architecture diagram is warranted given it does not span multiple components.

<details><summary><b>Implementation Detail</b></summary>

## Implementation Steps

### Phase 1 — Create the page
1. Create directory `public/` at the repo root (sibling of `src/` and `tests/`). No PHP there; it is purely a static-asset home. (It is not currently gitignored — verified against `.gitignore`, which only ignores `/vendor/`, `.phpunit.cache/`, `.env`, `hublaunch.config.js`, `.hula-worktrees/`, and `.hublaunch/tmp/` — so the file will be tracked by git.)
2. Create `public/index.html` with the structure below.

**Reference markup/skeleton for `public/index.html`:**

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shop — Product Listing</title>
  <style>
    :root { --gap: 1rem; }
    * { box-sizing: border-box; }
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
           margin: 0; padding: 2rem; color: #1a1a1a; background: #fafafa; }
    h1 { font-size: 1.75rem; margin: 0 0 1.5rem; }
    .products { list-style: none; margin: 0; padding: 0;
                display: grid; gap: var(--gap);
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
    .product { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px;
               padding: 1rem; }
    .product__name { font-size: 1rem; font-weight: 600; margin: 0 0 .5rem; }
    .product__price { font-size: 1.125rem; color: #0a7d2c; margin: 0; }
  </style>
</head>
<body>
  <h1>Our Products</h1>
  <ul class="products">
    <li class="product">
      <p class="product__name">Classic T-Shirt</p>
      <p class="product__price">$19.99</p>
    </li>
    <li class="product">
      <p class="product__name">Canvas Tote Bag</p>
      <p class="product__price">$12.50</p>
    </li>
    <li class="product">
      <p class="product__name">Ceramic Mug</p>
      <p class="product__price">$9.00</p>
    </li>
    <li class="product">
      <p class="product__name">Baseball Cap</p>
      <p class="product__price">$15.75</p>
    </li>
    <li class="product">
      <p class="product__name">Water Bottle</p>
      <p class="product__price">$22.00</p>
    </li>
    <li class="product">
      <p class="product__name">Notebook</p>
      <p class="product__price">$6.25</p>
    </li>
  </ul>
</body>
</html>
```

The sample names/prices are placeholders; the implementer may adjust them but should keep to **name + price only** (no images/descriptions) per the content decision.

### Phase 2 — Documentation
3. Add a short note to `README.md` describing the new page and how to view it (see Documentation Updates).

## Edge Cases
- **Opening directly vs. served:** The file uses only relative, self-contained content (embedded CSS, no external assets), so it renders identically whether opened via `file://` or served over HTTP. No absolute paths or server-side includes.
- **Empty/overflowing grid:** The `auto-fill, minmax(...)` grid degrades gracefully to a single column on narrow screens and expands on wide ones; no fixed column count to break.
- **Price formatting:** Prices are static strings; ensure a consistent currency symbol and two decimal places across all items for visual consistency.
- **No JS / no network:** Page must not depend on JavaScript or any remote resource (no CDN, no web fonts, no remote images), so it works offline.

## Error Handling
Not applicable — a static HTML document has no runtime error paths, no I/O, and no user input. There is nothing to validate, catch, or fall back from.

## Testing Strategy
- **Unit:** None applicable. The project's PHPUnit convention (`tests/CalculatorTest.php:8`, one test class per source class, `setUp()` + `assertSame`) applies to PHP logic; this change introduces **no PHP logic**, so no PHPUnit test is added. (If the data decision were later changed to a PHP renderer class, a `Calculator\Tests\` test under `tests/` asserting the rendered markup contains each product would be the matching convention.)
- **Integration:** None — no components interact.
- **Manual (primary verification):**
  1. Open `public/index.html` directly in a browser → confirm the heading and all product cards render with name + price.
  2. Serve it: `php -S localhost:8000 -t public` then visit `http://localhost:8000/` → confirm identical rendering.
  3. Resize the window / use responsive dev tools → confirm the grid reflows (single column on narrow, multiple columns on wide).
- **Regression:** Run `make lint` and `make test` → both must still pass unchanged. `make lint` globs only `src/**/*.php`, so the new HTML file is outside its scope and cannot break it.

## Performance Considerations
Negligible: a single small static HTML file with a few hundred bytes of embedded CSS and no external requests. No optimization needed.

## Security Considerations
- No server-side code, no user input, no data source → no injection, auth, or data-exposure surface.
- No external/CDN resources → no third-party supply-chain or privacy exposure, and the page works with no network egress.
- Content is static and trusted (hard-coded by the author).

## Dependencies & Related Work
- **Dependencies:** None added. `composer.json` is unchanged; no npm/build tooling introduced.
- **Related files (for context, not modified unless noted):**
  - `src/Calculator.php:5` — existing `Calculator\Calculator` class; establishes the PSR-4 `Calculator\` → `src/` convention. Not touched.
  - `tests/CalculatorTest.php:8` — PHPUnit convention reference. Not touched.
  - `composer.json:11-14` — PSR-4 autoload map. Not touched.
  - `Makefile:5-10` — `lint` (globs `src/**/*.php`) and `test` targets; the new static file falls outside both. Not touched.
  - `README.md` — updated per Documentation Updates.
  - `.gitignore` — confirmed it does **not** ignore `public/`, so the new file is tracked.

</details>

## Technical Considerations
- **Dependencies/config:** None. No Composer, npm, framework, or CDN additions. `composer.json`, `Makefile`, and autoloading are untouched.
- **File placement:** New `public/` directory keeps static assets separate from PSR-4 library code and enables `php -S <host> -t public` for local viewing. `public/` is not gitignored, so the file is committed.
- **Security:** Fully static, no user input, no data layer, no external resources → effectively zero runtime attack surface.

## Documentation Updates
- `README.md`: add a brief "Product listing page" note stating that `public/index.html` is a standalone static product listing page and can be viewed by opening the file directly or by running `php -S localhost:8000 -t public` and visiting `http://localhost:8000/`. Keep the existing "No web server, no CLI" framing accurate by phrasing this as an optional static asset, not a change to the calculator library.

## Acceptance Criteria
- [ ] A new file `public/index.html` exists and is a valid, well-formed HTML5 document.
- [ ] The page renders an `<h1>` store heading and **at least 4** product cards, each showing a **name** and a **price** (currency symbol + two decimals).
- [ ] Each product shows **only** name and price — no images, descriptions, buttons, or landing-page chrome.
- [ ] Styling is provided by a single embedded `<style>` block; the page loads **no** external resources (no CDN, web fonts, or remote images) and requires **no** JavaScript.
- [ ] The page renders correctly both when opened directly (`file://`) and when served via `php -S localhost:8000 -t public`, and the grid reflows responsively on narrow and wide viewports.
- [ ] No PHP files, `composer.json`, `tests/`, or the `Makefile` are modified; `make lint` and `make test` still pass unchanged.
- [ ] `README.md` notes the new page and how to view it.

## Assumptions
- **Reconciling the delivery-form and data decisions:** The delivery form was fixed to "static HTML, no PHP," while the data decision was "hard-coded/mock products in a PHP array or fixture." Because "no PHP" takes precedence for the deliverable's shape, the mock products are hard-coded **directly as HTML markup** in `public/index.html` rather than in a PHP array. This preserves both intents (static, dependency-free page + hard-coded mock data) and keeps the page a single self-contained file.
- The page lives at `public/index.html` (new `public/` directory at repo root). If a different location/filename is preferred, only the path and the README note change; the content is identical.
- Sample product names/prices are placeholders chosen to look like a plausible merch store; exact wording is not significant to acceptance.
- No production deployment/hosting is in scope; "viewable locally" (direct open or `php -S`) satisfies the request.
- The repository checkout matches the planning snapshot commit `bd44bce666201062e89c58d64e315c95435d87d6` exactly — no drift was detected during planning.
