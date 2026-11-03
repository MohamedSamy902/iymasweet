# UI Audit — Iyma Sweet (before Sweet Boutique redesign)

Captured: Nov 3, 2026 · Viewports: **390×900** and **1280×900**  
Artifacts: `/opt/cursor/artifacts/audit-before/`

## Pages reviewed
Home, Shop, Product, Cart, Register (+ layout shell on all)

## Issues found

### Spacing & hierarchy
1. Section gaps feel uneven (~40–56px mixed) — not on a strict 4px grid.
2. Hero has no clear “one hero element”: brand + CTAs compete with muted photo wash.
3. Product cards: title/price/CTA same visual weight — no clear primary action hierarchy.
4. Home stacks many equal sections without wave/surface rhythm.

### Color
5. Token set incomplete (only a few `--color-*`); hard-coded hex in Home hero overlays (`#fffaf7`).
6. Pink identity diluted — denim/cream compete equally.
7. Status chips use generic gray for sold-out instead of warm cocoa palette.
8. Shadows lean generic black rather than warm pink-tinted shadows.

### Typography
9. Heading font is Cairo/Tajawal — brief asks **Baloo Bhaijaan 2** for display.
10. Missing Cairo weight 600; too many competing sizes/weights on product page.
11. Caption/label sizes inconsistently 11–14px; some under 12px on bottom nav.

### Imagery
12. Hero uses low-opacity stock photo — not a character/blob brand anchor.
13. Product aspect ratios square (1:1) instead of boutique **4:5**.
14. Missing-image fallback historically logo gray; category-tinted fallbacks incomplete.
15. Sold-out treatment not clearly desaturated + ribbon.

### Mobile / shell
16. Bottom nav previously clipped by overflow ancestors (partially fixed via Teleport).
17. WhatsApp FAB can crowd mini-cart / product sticky CTA.
18. Sticky product add-to-cart and bottom nav stacking needs clearer z-index + spacing.
19. Horizontal chip rows OK, but page-level overflow risk remains with full-bleed abs elements.

### Components
20. Buttons look “default pill” — not boutique (height/shadow/press states incomplete).
21. Form fields lack consistent 52px height / focus ring / error chrome.
22. No shared Badge / StatusPill / SegmentedTabs / QuantityStepper system.
23. Empty states plain text — no character illustration treatment.
24. Chat FAB currently replaced by WhatsApp — visual pulse/brand donut icon missing when chat returns.

### Accessibility
25. Focus rings inconsistent.
26. Touch targets sometimes < 44px (chip close, icon buttons).
27. Price currency not always wrapped in isolated LTR.

## Priority for redesign
P0 tokens + shell + ProductCard + Home hero · P1 shop/product · P2 cart/checkout/auth · P3 styleguide + QA shots
