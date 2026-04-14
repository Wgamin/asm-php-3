```markdown
# Design System Document: High-End Editorial Admin

## 1. Overview & Creative North Star: "The Modern Agrarian"
This design system moves away from the sterile, "template-like" feel of generic SaaS dashboards. Our Creative North Star is **"The Modern Agrarian."** It represents a marriage between high-tech logistics and the grounded, organic nature of Vietnamese agriculture.

To achieve this, we move beyond basic grids. We utilize **Intentional Asymmetry** and **Tonal Depth** to guide the user’s eye. The interface shouldn't feel like a spreadsheet; it should feel like a premium digital ledger. We emphasize high-contrast typography scales and "breathable" layouts that prioritize operational efficiency without sacrificing an editorial aesthetic.

---

## 2. Colors & Surface Architecture
We utilize a sophisticated palette that avoids the "flatness" of standard admin panels. 

### The "No-Line" Rule
**Explicit Instruction:** Sectioning via 1px solid borders is prohibited. In this system, boundaries are defined through background color shifts or tonal transitions. Use `surface_container_low` for a section sitting on a `surface` background to create a clean, modern break without visual clutter.

### Surface Hierarchy & Nesting
Treat the UI as layered sheets of premium paper. 
- **Base Layer:** `surface` (#f7f9fb)
- **Secondary Sectioning:** `surface_container_low` (#f2f4f6)
- **Actionable Cards:** `surface_container_lowest` (#ffffff)
- **Deep Insets:** `surface_container_high` (#e6e8ea)

### The "Glass & Soul" Rule
While we avoid "flashy" marketing, we use **Glassmorphism** for floating elements (like toast notifications or mobile navigation anchors). Use `surface_container_lowest` at 80% opacity with a 12px backdrop-blur. 
To provide visual "soul," use a subtle linear gradient for main CTAs: 
*From `primary` (#206223) to `primary_container` (#3a7b3a).*

---

## 3. Typography: Editorial Authority
We pair **Manrope** (Display/Headlines) with **Inter** (Body/UI) to create a balance between character and utility.

| Level | Token | Font | Size | Weight | Intent |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Display** | `display-md` | Manrope | 2.75rem | 700 | Monthly revenue, Hero metrics |
| **Headline** | `headline-sm` | Manrope | 1.5rem | 600 | Page titles, Section headers |
| **Title** | `title-sm` | Inter | 1rem | 600 | Card titles, Modal headers |
| **Body** | `body-md` | Inter | 0.875rem | 400 | Data tables, Descriptions |
| **Label** | `label-md` | Inter | 0.75rem | 500 | Metadata, Status badges |

**Hierarchy Note:** Use `on_surface_variant` (#40493d) for secondary text to maintain a high-end, soft-contrast look that reduces eye strain during long operational sessions.

---

## 4. Elevation & Depth: Tonal Layering
Traditional structural lines are replaced by the **Layering Principle**. 

- **Ambient Shadows:** For "floating" elements like dropdowns, use a shadow with a 20px blur, 0px spread, and 4% opacity using a tinted version of `on_surface`. It should feel like a soft glow, not a dark drop shadow.
- **The "Ghost Border" Fallback:** If a border is required for accessibility, use the `outline_variant` (#bfcaba) at 20% opacity. **Never use 100% opaque borders.**
- **Glassmorphism:** Apply to global headers or floating filters. This allows the agricultural greens and amber accents to bleed through slightly, softening the layout and making it feel integrated.

---

## 5. Components

### Buttons & CTAs
- **Primary:** Gradient from `primary` to `primary_container`. White text. Border radius: `md` (0.375rem).
- **Secondary:** `secondary_container` background with `on_secondary_container` text.
- **Tertiary:** Transparent background, `on_surface` text, `surface_variant` hover state.

### High-Density Data Tables
- **Forbid Divider Lines:** Use `8pt` spacing system to separate rows. 
- **Alternating Tones:** Use `surface_container_low` for every second row instead of lines.
- **Status Badges:** 
    - *Pending/Draft:* `tertiary_fixed` background with `on_tertiary_fixed_variant` text.
    - *Success/Paid:* `primary_fixed` background with `on_primary_fixed_variant` text.
    - *Danger/Cancelled:* `error_container` background with `on_error_container` text.

### Input Fields
- **Default State:** `surface_container_highest` background. No border.
- **Focus State:** 2px solid `primary`. 
- **Labeling:** Use `label-md` floating above the field in `on_surface_variant`.

### Contextual "Agri-Cards"
Specific to this system: Use a vertical accent bar (4px wide) on the left of cards using the `primary` color to denote "Active Shipments" or `tertiary` for "Low Stock" alerts.

---

## 6. Do’s and Don’ts

### Do
- **Do** use whitespace as a functional tool. If two elements feel too close, increase padding rather than adding a line.
- **Do** use `manrope` for numbers. It provides a more custom, "premium" look to financial data.
- **Do** ensure all "Success" states use our professional forest green (`primary`), not a neon "web" green.

### Don’t
- **Don't** use pure black (#000000) for text. Always use `on_surface` (#191c1e) for a softer, editorial feel.
- **Don't** use standard "Drop Shadows." If an element needs to pop, use a background color shift first.
- **Don't** use high-saturation gradients. Transitions should be subtle and tonal, moving within the same color family. 

---

## 7. Spacing Scale
Based on an **8pt system** to ensure mathematical harmony.
- **XS:** 4px (tight grouping)
- **SM:** 8px (internal component padding)
- **MD:** 16px (standard spacing between elements)
- **LG:** 32px (section spacing)
- **XL:** 64px (container margins)```