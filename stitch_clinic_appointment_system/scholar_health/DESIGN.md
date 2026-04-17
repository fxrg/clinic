# Design System Specification: The Academic Clinical Standard

## 1. Overview & Creative North Star: "The Disciplined Curator"
The Creative North Star for this design system is **"The Disciplined Curator."** While many medical systems feel sterile and corporate, this system adopts the persona of a high-end academic journal or a well-organized research portfolio. It moves beyond "student-level" by replacing standard grid lines with sophisticated tonal layering and editorial-inspired whitespace.

The goal is to provide a sense of "Institutional Trust." We achieve this through **Intentional Asymmetry**: large, clear typography on the left balanced by breathable, layered information cards on the right. We reject the "boxed-in" look of traditional software in favor of an expansive, open-canvas feel that prioritizes cognitive ease for patients and practitioners alike.

---

## 2. Colors: Tonal Architecture
We move beyond a simple "blue and white" palette by utilizing a sophisticated range of surface tiers. This allows us to define hierarchy without the "visual noise" of borders.

### The "No-Line" Rule
**Explicit Instruction:** You are prohibited from using 1px solid borders to section content. Boundaries must be defined solely through background color shifts.
*   **Base Layer:** Use `surface` (#f8f9fa) for the overall page background.
*   **Sectioning:** Use `surface-container-low` (#f1f4f6) to define large functional areas.
*   **Nested Content:** Place `surface-container-lowest` (#ffffff) cards inside those areas to create natural focus.

### Tonal Hierarchy
*   **Primary Identity:** `primary` (#115cb9) is reserved for high-level actions and core brand moments.
*   **Signature Textures:** For Hero sections or primary call-to-actions, use a subtle linear gradient from `primary` (#115cb9) to `primary_dim` (#0050a7) at a 135-degree angle. This adds "soul" and depth that flat color cannot provide.
*   **Glassmorphism:** For floating elements like navigation bars or pop-overs, use `surface` at 80% opacity with a `16px` backdrop-blur. This keeps the clinical environment feeling light and interconnected.

---

## 3. Typography: The Editorial Voice
This system pairs **Public Sans** (Display/Headlines) with **Inter** (Body/UI) to create a clear distinction between "Instruction" and "Information."

*   **Display & Headline (Public Sans):** Used for page titles and section headers. The slightly wider stance of Public Sans provides an authoritative, academic feel.
    *   *Example:* `headline-lg` (2rem) for "Book an Appointment."
*   **Body & Title (Inter):** Used for all functional UI text. Inter is optimized for readability at small sizes, crucial for medical data.
    *   *Example:* `body-md` (0.875rem) for form labels and patient details.
*   **Hierarchy Note:** Use `on_surface_variant` (#586064) for secondary information (like timestamps or help text) to reduce visual competition with the `on_surface` (#2b3437) primary content.

---

## 4. Elevation & Depth: Layering Over Lines
Depth in this system is a physical property. We treat the UI as a series of stacked "Fine Paper" sheets.

*   **The Layering Principle:** To lift a card, do not reach for a shadow first. Instead, place a `surface_container_lowest` (#ffffff) card on top of a `surface_container` (#eaeff1) background. The 4% shift in luminance is enough for the human eye to perceive depth.
*   **Ambient Shadows:** If a floating element (like a modal) requires a shadow, use: `box-shadow: 0 12px 32px -4px rgba(43, 52, 55, 0.08)`. The shadow color is derived from `on_surface`, creating a natural, diffused light effect rather than a "dirty" grey.
*   **The Ghost Border Fallback:** If accessibility requirements demand a container edge, use a "Ghost Border": `outline_variant` (#abb3b7) at **15% opacity**. It should be felt, not seen.

---

## 5. Components: Functional Elegance

### Buttons
*   **Primary:** Solid `primary` with `on_primary` text. Use `xl` (0.75rem) roundedness. No icons unless they represent a specific action (e.g., a "Plus" for new appointments).
*   **Secondary:** `secondary_container` background. These should feel grounded and less urgent.
*   **Tertiary:** No background. Use `primary` text weight `600`. Reserved for "Cancel" or "Back" actions.

### Input Fields
*   **Architecture:** Avoid the "four-sided box." Use a `surface_container_highest` background with a subtle bottom-stroke of `outline` (#737c7f) at 30% opacity.
*   **States:** On focus, the bottom-stroke transitions to `primary` (2px thickness), and the background shifts to `surface_container_lowest`.

### Appointment Cards & Lists
*   **Strict Rule:** No dividers. Use `24px` of vertical whitespace to separate list items. 
*   **Visual Cue:** Use a vertical "Status Pillar" (4px wide, `lg` roundedness) on the far left of the card using `primary` (Confirmed), `tertiary` (Pending), or `error` (Cancelled).

### Selection Chips
*   **Behavior:** Use `secondary_fixed` (#e2e2e9) for unselected states. When selected, transition to `primary_container` (#d7e2ff) with `on_primary_container` (#004fa6) text. This provides high-contrast feedback without needing a "checkmark" icon.

---

## 6. Do’s and Don’ts

### Do:
*   **Do** use extreme whitespace (32px, 48px, 64px) to separate unrelated functional groups.
*   **Do** use `title-lg` for card titles to give the user a clear "entry point" to the data.
*   **Do** align all text to a strict baseline grid to maintain the academic, disciplined feel.

### Don’t:
*   **Don't** use pure black (#000). Always use `on_background` (#2b3437) for text to keep the interface soft.
*   **Don't** use "Alert Red" for everything. Use `error` (#9f403d) only for critical system failures; use `tertiary` (#5e5c78) for neutral warnings or "pending" states.
*   **Don't** use standard 400ms animations. If you must use motion, use a very fast 150ms "Fade & Slide Up" (4px) to make the system feel responsive and surgical.