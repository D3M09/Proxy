---
name: Obsidian Messenger
colors:
  surface: '#0b1326'
  surface-dim: '#0b1326'
  surface-bright: '#31394d'
  surface-container-lowest: '#060e20'
  surface-container-low: '#131b2e'
  surface-container: '#171f33'
  surface-container-high: '#222a3d'
  surface-container-highest: '#2d3449'
  on-surface: '#dae2fd'
  on-surface-variant: '#c7c4d7'
  inverse-surface: '#dae2fd'
  inverse-on-surface: '#283044'
  outline: '#908fa0'
  outline-variant: '#464554'
  surface-tint: '#c0c1ff'
  primary: '#c0c1ff'
  on-primary: '#1000a9'
  primary-container: '#8083ff'
  on-primary-container: '#0d0096'
  inverse-primary: '#494bd6'
  secondary: '#c3c0ff'
  on-secondary: '#1d00a5'
  secondary-container: '#3626ce'
  on-secondary-container: '#b3b1ff'
  tertiary: '#4edea3'
  on-tertiary: '#003824'
  tertiary-container: '#00885d'
  on-tertiary-container: '#000703'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#e1e0ff'
  primary-fixed-dim: '#c0c1ff'
  on-primary-fixed: '#07006c'
  on-primary-fixed-variant: '#2f2ebe'
  secondary-fixed: '#e2dfff'
  secondary-fixed-dim: '#c3c0ff'
  on-secondary-fixed: '#0f0069'
  on-secondary-fixed-variant: '#3323cc'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#0b1326'
  on-background: '#dae2fd'
  surface-variant: '#2d3449'
typography:
  headline-lg:
    fontFamily: Manrope
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Manrope
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.015em
  headline-sm:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
    letterSpacing: -0.01em
  title-md:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 22px
  body-lg:
    fontFamily: Hanken Grotesk
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Hanken Grotesk
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Hanken Grotesk
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
  label-md:
    fontFamily: Hanken Grotesk
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Hanken Grotesk
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.04em
  code-sm:
    fontFamily: Hanken Grotesk
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
rounded:
  sm: 0.5rem
  DEFAULT: 1rem
  md: 1.5rem
  lg: 2rem
  xl: 3rem
  full: 9999px
spacing:
  gutter: 0.75rem
  margin: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 0.75rem
  space-lg: 1rem
  space-xl: 1.5rem
---

## Brand & Style

The design system embodies the focused precision of high-velocity developer tools merged with the attentive warmth of premier enterprise concierge services. Built for mobile web viewports, it transforms standard support interactions into a high-density, low-latency console experience. 

The aesthetic is anchored in an obsidian foundation: matte dark slate surfaces layered under hyper-focused violet illuminations, subtle edge highlights, and fluid micro-transitions. Rather than presenting a generic chat overlay, the interface projects authoritative reliability and modern luxury. Glassmorphic depth cues separate conversational layers, while high-legibility typography ensures immediate clarity during urgent technical triaging or mission-critical customer interventions.

## Colors

The palette establishes visual priority through deep, light-absorbing slate backdrops punctuated by calculated spectral accents:

- **Primary (`#6366F1`) & Secondary (`#4F46E5`):** The primary brand voice and interactive signature. Used for active user message bubbles, focused field rings, primary trigger buttons, and subtle structural gradients that trace card headers.
- **Tertiary (`#10B981`):** The live status pulse. Reserved strictly for operational states: active agent signals, system operational status, and real-time connectivity validation.
- **Neutral Foundations (`#0F172A`, `#1E293B`, `#334155`):** The canvas tiers.
  - `Canvas Deep (#0F172A)` forms the viewport baseline.
  - `Surface Layer (#1E293B)` houses inbound messages, sheet modals, and input docks.
  - `Border Muted (#334155)` defines crisp 1px structural separation without harsh contrast.
- **Content Neutral (`#F8FAFC`, `#94A3B8`):** Off-white typography minimizes ocular fatigue on deep dark backgrounds while surpassing WCAG AAA contrast ratios for body and metadata.

## Typography

The type system blends the structural stability of Manrope for display hierarchy with the modern geometric cadence of Hanken Grotesk for body reading and interactive meta-labels:

- **Headlines & Conversational Titles (Manrope):** Geometric, slightly wide apertures convey stability. Letter spacing is compressed to keep multi-word headers compact within constrained mobile panels.
- **Chat Streams & System Responses (Hanken Grotesk):** Engineered for continuous stream readability. Neutral vertical metrics prevent bubble clipping, while distinct glyph differentiation prevents ambiguity across email addresses, ticket IDs, and technical stack traces.
- **Labels & Micro-copy:** Rendered with elevated weights (`500` to `600`) and slight tracking expansion to maintain scanning efficiency at small viewport bounds.

## Layout & Spacing

This design system targets touch-first mobile web environments using an edge-to-edge flexible column system anchored to browser safe areas (`env(safe-area-inset-*)`).

- **Grid & Safe Geometry:** The interface operates within a single-column fluid frame constrained horizontally to `480px` maximum width when displayed on tablets or desktop panels. The default horizontal screen margin is fixed at `1rem` (16px), with a standard intra-card gutter of `0.75rem` (12px).
- **Rhythm Scale:** Spacing derives from a base-4 metric.
  - `space-xs` (4px): Inline indicator separation and bubble tail offsets.
  - `space-sm` (8px): Stacked bubble spacing within the same sender cluster.
  - `space-md` (12px): Distinct sender message transitions and button padding.
  - `space-lg` (16px): Structural card interior padding and scroll offsets.
  - `space-xl` (24px): Section divides, modal sheet tops, and onboarding headers.
- **Input Fixed Anchor:** The conversational entry console docks rigidly to the viewport base, preserving padding above virtual keyboards while maintaining active view of incoming quick-replies.

## Elevation & Depth

Depth is established not through heavy opaque shadows, but through tonal illumination, calibrated internal borders, and subtle luminescent violet glows:

- **Base Layer (Level 0):** Hex `#0F172A`. Flat, non-interactive conversational canvas.
- **Elevated Surfaces (Level 1):** Hex `#1E293B` backed by a 1px border of `rgba(255, 255, 255, 0.08)`. Applied to agent message cards, bot carousels, and attachment blocks.
- **Floating Controls (Level 2):** Floating Action Buttons (FABs) and interactive popovers leverage a dual-effect elevation: an ambient drop shadow of `0 12px 32px -4px rgba(0, 0, 0, 0.65)` layered with a subtle violet underglow of `0 0 20px -2px rgba(99, 102, 241, 0.25)`.
- **Top Sheet & Overlays (Level 3):** Frosted glass surface using backdrop filter blur (`backdrop-filter: blur(16px)`), a fill of `rgba(15, 23, 42, 0.85)`, and a top-edge highlight of `rgba(255, 255, 255, 0.15)`.

## Shapes

The shape architecture relies on dynamic pill-shaped geometry (Level 3) to deliver a sleek consumer-grade touch aesthetic suited for fast mobile navigation:

- **Message Bubbles:** Agent cards feature fluid asymmetry (top-left, top-right, bottom-right at `1rem` or 16px; bottom-left at `0.25rem` or 4px). User outbound messages mirror this logic with an anchoring pinch at the bottom-right.
- **Interactive Action Elements:** Buttons, search bars, text entry capsules, and quick-reply recommendation chips are full pills (`rounded-full` / 9999px), creating instantly recognizable tap targets.
- **Structured Cards & Modals:** Large cards utilize `rounded-2xl` (`1rem` / 16px) to `rounded-3xl` (`2rem` / 32px) for bottom sheets, preserving an organic physical contour against mobile display borders.

## Components

### Message Bubbles
- **User Messages:** Background set to a linear gradient from `#6366F1` to `#4F46E5` angled at 135 degrees. Text is pure `#FFFFFF` with high-contrast font weight `400`. Margin pinned to the right edge with a maximum content width of 82%.
- **Agent & Bot Bubbles:** Surface background `#1E293B` surrounded by a `1px` continuous border of `rgba(255, 255, 255, 0.07)`. Text is `#F8FAFC`.
- **Proactive Bot Response Cards:** Embedded interactive options inside an agent bubble container, featuring an explicit header bar with agent avatar, timestamp, and verification badge.

### Action Chips & Quick Replies
- Horizontally scrollable row positioned directly above the input console.
- Pill geometry with `0.5rem` vertical and `1rem` horizontal padding.
- Default state: `rgba(99, 102, 241, 0.08)` fill, `1px` border of `rgba(99, 102, 241, 0.3)`, text `#F8FAFC`.
- Pressed/Active state: Background `#6366F1`, text `#FFFFFF`, accompanied by a subtle tactile shrink transform (`scale(0.97)`).

### Input Console
- Encapsulated in a floating dock floating `0.75rem` above the screen bottom safe area.
- Multi-line auto-expanding field surrounded by a soft pill shell.
- Attachment and emoji triggers rest inside the input field perimeter, rendered in `#94A3B8` switching to `#6366F1` upon hover or focus.
- Send trigger transforms from a disabled neutral glyph into a luminescent violet pill as soon as an input character threshold is reached.

### Agent Status Indicator
- Compact pill composed of a subtle green background tint (`rgba(16, 185, 129, 0.12)`), an outer hairline border (`rgba(16, 185, 129, 0.25)`), and an interior 6px emerald dot (`#10B981`) sporting an infinite soft ping animation.
- Accompanied by micro typography: "Typically replies in 2m".

### Rich Attachments & Media Cards
- File uploads display as self-contained cards with a deep charcoal background, featuring an explicit icon badge corresponding to file type (PDF, image, code).
- Image uploads feature continuous inner border clipping with `1rem` radius and a semi-translucent dark gradient scrim along the bottom to maintain timestamp readability.