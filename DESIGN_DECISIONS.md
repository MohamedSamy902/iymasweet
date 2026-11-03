# DESIGN_DECISIONS — Sweet Boutique

Visual redesign for Iyma Sweet (Laravel + Inertia + Vue 3 + Tailwind). Backend routes/API unchanged.

## Brand concept
Warm boutique sweets + modern delivery app clarity. Pink is the identity; denim assists; cocoa replaces cold black/gray text. Accents (coral/sun/mint/sky/lilac) appear only as micro-doses (sprinkles, step dots, save ribbons).

## Tokens
- Full palette lives in `resources/css/app.css` `:root` (`--pink-*`, `--denim-*`, `--cocoa-*`, radius, warm pink shadows, motion).
- Semantic `--color-*` aliases are injected from `SettingsService` / dashboard color pickers (`config/iyma.php` defaults updated to boutique values).
- No cold gray placeholders; missing product media uses category-tinted `ProductImage` + stroke icon + sparkles.

## Typography
- Display/headings: **Baloo Bhaijaan 2** (self-hosted woff2; weight 800 synthesized from 700 file).
- UI/body: **Cairo** 400/700; weight 600 mapped to the 700 file (no separate 600 asset).
- Arabic rules: no letter-spacing, no uppercase, body line-height 1.75.

## Assets
- Mascots: `/public/images/mascots.svg` (donut + cupcake characters) for hero, empty, auth, success — used until real logo-cutout characters exist.
- Sparkle + wave divider as Vue components coloring from tokens.
- Product cards use **4:5** media; sold-out = `saturate(.55)` + translucent ribbon (not dead gray).

## Shell
- Cream header with blur; bottom border only after scroll.
- Mobile bottom nav 64px + safe area; active = pink icon pill.
- Mini-cart pill above nav; WhatsApp FAB uses pink gradient + pulse (chat remains feature-flagged).
- Footer cocoa-900 with 4px rainbow hairline.

## Page notes
- Home: gradient hero, blob mascots + floating sparkles, floating status card overlapping hero, scalloped bundles band, wave dividers between surfaces.
- Shop: sticky filter bar; empty state with mascots.
- Product: mobile sheet overlapping gallery; sticky add bar above bottom nav.
- Auth: segmented login/register + mascot panel on desktop; phone field `dir=ltr` with +20 prefix.
- Styleguide: `/styleguide` in local/staging only.

## Filament / PWA
- Admin primary hex `#C9477A`, Cairo, full width, collapsible sidebar, no dark mode.
- PWA `theme_color` = `#FFF5F8` (pink-50) to match soft header chrome.

## Deferred / follow-ups
- Real transparent logo cutout when provided (header currently uses circular product/logo crop).
- Full Filament `viteTheme` CSS package + bottom-sheet filter drawer + chat panel polish can deepen in a later pass.
- Lighthouse scores to be re-measured after image CDN/WebP pipeline.
