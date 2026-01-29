# Modal Viewer Visual Wireframes

**Design Spec Reference:** `modal-viewer-swiper-ux-ui.md`

---

## Mobile Layout (XS/SM: < 768px)

### Modal Closed
```
┌─────────────────────────────────┐
│                                 │
│  [Masonry Grid with Images]     │
│                                 │
│  ┌───┐  ┌───┐                   │
│  │IMG│  │IMG│                   │
│  └───┘  └───┘                   │
│                                 │
│  ┌───┐  ┌───┐                   │
│  │IMG│  │IMG│  ← Click to open  │
│  └───┘  └───┘     modal         │
│                                 │
└─────────────────────────────────┘
```

### Modal Open - Image View
```
┌─────────────────────────────────┐ ← Fullscreen
│ [×]                             │ ← Close button (top-right)
│                                 │
│                                 │
│        ┌─────────────┐          │
│        │             │          │
│        │   IMAGE     │          │ ← Centered image
│        │             │          │
│        └─────────────┘          │
│                                 │
│                                 │
│        ● ○ ○ ○ ○                │ ← Pagination dots
│                                 │
│  [📍] TOGGLE METADATA           │ ← Metadata toggle
└─────────────────────────────────┘

Touch Gestures:
← Swipe left:  Next image
→ Swipe right: Previous image
↓ Swipe down:  Close modal
```

### Modal Open - Metadata Expanded
```
┌─────────────────────────────────┐
│ [×]                             │
│                                 │
│        ┌─────────────┐          │
│        │   IMAGE     │          │ ← Image smaller
│        └─────────────┘          │
│                                 │
│        ● ○ ○ ○ ○                │
│                                 │
│  [×] TOGGLE METADATA            │ ← Close metadata
├─────────────────────────────────┤
│ 📍 LOCATION                     │
│ Pike Place Market, Seattle      │
│                                 │
│ 📅 DATE                         │
│ Friday, January 24, 2025        │
│                                 │
│ 👤 UPLOADER                     │
│ @skater_username                │
│                                 │ ← Scrollable if needed
│ 🗺️ GPS COORDINATES              │
│ 47.6097° N, 122.3421° W         │
└─────────────────────────────────┘
```

### Modal Open - Video View
```
┌─────────────────────────────────┐
│ [×]                             │
│                                 │
│ ┌─────────────────────────────┐ │
│ │                             │ │
│ │        ┌───────┐            │ │
│ │        │   ▶   │            │ │ ← Hot pink play button
│ │        └───────┘            │ │
│ │                             │ │
│ │ ─────────────────────       │ │ ← VideoJS controls
│ └─────────────────────────────┘ │
│                                 │
│        ● ○ ○ ○ ○                │
│                                 │
│  [📍] TOGGLE METADATA           │
└─────────────────────────────────┘
```

---

## Tablet Layout (MD: 768-991px)

### Modal Open - Image View
```
┌─────────────────────────────────────────┐
│                                [×]      │ ← Close (top-right)
│                                         │
│                                         │
│       ┌───────────────────────┐         │
│       │                       │         │
│       │                       │         │
│       │       IMAGE           │         │ ← Centered
│       │                       │         │
│       │                       │         │
│       └───────────────────────┘         │
│                                         │
│               ● ○ ○ ○ ○                 │
│                                         │
│          [📍] TOGGLE METADATA           │
└─────────────────────────────────────────┘

Touch Gestures: Same as mobile
NO arrow buttons yet (only desktop)
```

---

## Desktop Layout (LG+: ≥ 992px)

### Modal Open - Image View
```
┌───────────────────────────────────────────────────────┐
│                                              [×]      │
│                                                       │
│   ┌──┐                                         ┌──┐  │
│   │←│                                          │→│  │ ← Nav arrows
│   └──┘                                         └──┘  │
│                                                       │
│              ┌────────────────────┐                   │
│              │                    │                   │
│              │                    │                   │
│              │      IMAGE         │                   │
│              │                    │                   │
│              │                    │                   │
│              └────────────────────┘                   │
│                                                       │
│                    ● ○ ○ ○ ○                          │
│                                                       │
│              [📍] TOGGLE METADATA                     │
└───────────────────────────────────────────────────────┘

Interactions:
- Click arrows: Navigate slides
- Keyboard ←/→: Navigate slides
- Keyboard ESC: Close modal
- Click outside: Close modal
- Hover arrows: Glow effect
```

### Modal Open - Metadata Expanded (Desktop)
```
┌───────────────────────────────────────────────────────┐
│                                              [×]      │
│                                                       │
│   ┌──┐          ┌────────────┐              ┌──┐    │
│   │←│           │   IMAGE    │              │→│    │
│   └──┘          └────────────┘              └──┘    │
│                                                       │
│                    ● ○ ○ ○ ○                          │
│                                                       │
│              [×] TOGGLE METADATA                      │
├───────────────────────────────────────────────────────┤
│ ┌───────────────────────────────────────────────────┐ │
│ │ 📍 LOCATION                                       │ │
│ │ Pike Place Market, Seattle, WA                    │ │
│ │                                                   │ │
│ │ 📅 DATE                                           │ │
│ │ Friday, January 24, 2025 | 8:47 PM               │ │
│ │                                                   │ │
│ │ 👤 UPLOADER                                       │ │
│ │ @skater_username | Jane Doe                      │ │
│ │                                                   │ │
│ │ 🗺️ GPS COORDINATES                                │ │
│ │ 47.6097° N, 122.3421° W                          │ │
│ │ [View on Map]  [Get Directions]                  │ │
│ └───────────────────────────────────────────────────┘ │
└───────────────────────────────────────────────────────┘
```

---

## Component Details

### Close Button (All Breakpoints)
```
     ┌────────┐
     │   ×    │  48px × 48px
     │        │  Circle
     └────────┘  Yellow border (tertiary accent)
                 Rotates 90° on hover
```

### Navigation Arrows (Desktop Only)
```
     ┌────────┐       ┌────────┐
     │   ←    │       │   →    │  56px × 56px
     │        │       │        │  Circle
     └────────┘       └────────┘  Teal border/glow
                                  Scale 1.15 on hover
```

### Pagination Dots
```
Inactive:  ○ ○ ○ ○ ○    10px circle, 50% opacity
Active:    ● ─── ○ ○ ○   28px pill, 100% opacity
                         Teal color, glowing
```

### Metadata Toggle Button
```
┌──────────────────────┐
│  [📍] TOGGLE METADATA │  200px × 44px
└──────────────────────┘  Rounded top, teal border
                          Lifts up on hover
```

### Metadata Panel Item
```
📍 LOCATION                        ← Label (Staatliches, uppercase, teal)
Pike Place Market, Seattle, WA     ← Value (Archivo, white)

24px margin between items
```

---

## Color Coding Legend

```
Background Colors:
███ Near-Black (#0a0a0f)        - Modal background
███ Dark Navy (rgba 10,10,15)   - Overlay backdrop
███ Dark Gray (rgba 20,20,28)   - Metadata panel

Accent Colors:
███ Electric Teal (#00ff9f)     - Primary actions, borders
███ Hot Pink (#ff006e)          - Video indicators, progress
███ Street Yellow (#ffd60a)     - Focus rings, warnings

Text Colors:
███ White (#f8f9fa)             - Primary text
███ Dimmed White (70% opacity)  - Secondary text
███ Very Dim (45% opacity)      - Muted text
```

---

## State Diagrams

### Modal States
```
[Grid View]
    │
    │ Click image/video
    ↓
[Modal Opening]  ← 250ms fade + scale
    │
    ↓
[Modal Open - Viewing]
    │
    ├─→ [Swipe/Click] → [Next/Prev Slide]
    ├─→ [Click Toggle] → [Metadata Expanded]
    ├─→ [Press ESC] → [Modal Closing]
    ├─→ [Click X] → [Modal Closing]
    └─→ [Click Outside] → [Modal Closing]
              │
              ↓
        [Modal Closing]  ← 200ms fade
              │
              ↓
         [Grid View]
```

### Metadata Panel States
```
[Panel Hidden]
    │
    │ Click toggle
    ↓
[Panel Expanding]  ← 400ms slide up
    │
    ↓
[Panel Visible]
    │
    │ Click toggle
    ↓
[Panel Collapsing]  ← 300ms slide down
    │
    ↓
[Panel Hidden]
```

---

## Z-Index Stacking

```
Z-Index Layer Chart:

1060  ┌─────────────────┐  Close button (always on top)
      └─────────────────┘

1055  ┌─────────────────┐  Modal backdrop (Bootstrap default)
      └─────────────────┘

1050  ┌─────────────────┐  Modal content container
      └─────────────────┘

15    ┌─────────────────┐  Metadata toggle button
      └─────────────────┘

14    ┌─────────────────┐  Metadata panel
      └─────────────────┘

10    ┌─────────────────┐  Swiper navigation (arrows, pagination)
      └─────────────────┘

1     ┌─────────────────┐  Swiper slides (images/videos)
      └─────────────────┘
```

---

## Touch Zones (Mobile)

```
┌─────────────────────────────────┐
│ [×] ← 48×48 touch               │
│                                 │
│ ←──────── SWIPE ZONE ──────→    │ ← Full width/height
│ ←──────── SWIPE ZONE ──────→    │   for horizontal swipe
│ ←──────── SWIPE ZONE ──────→    │
│                                 │
│        ● ○ ○ ○ ○ ← 44×44 each   │
│                                 │
│  [📍] TOGGLE ← 44px height      │
└─────────────────────────────────┘
```

---

## Responsive Breakpoint Changes

| Feature | XS/SM | MD | LG+ |
|---------|-------|-----|-----|
| Modal size | Fullscreen | 90vw | 80vw (max 1200px) |
| Nav arrows | Hidden | Hidden | Visible |
| Padding | 0 | 16px | 24-32px |
| Close position | 12,12 | 16,16 | 20,20 |
| Dots size | 12px | 10px | 10px |
| Metadata height | 280px | 300px | 320px |

---

## Animation Timeline

### Modal Open (Total: 250ms)
```
0ms    ─────────────────→
       Backdrop fades in
       Modal scales from 0.95 to 1.0
       Modal opacity 0 → 1

250ms  Complete
       Focus moves to close button
```

### Slide Transition (Total: 400ms)
```
0ms    ─────────────────→
       Current slide moves out
       Next slide moves in
       Pagination dot animates

400ms  Complete
       Metadata updates
       Screen reader announces
```

### Metadata Panel Open (Total: 400ms)
```
0ms    ─────────────────→
       Max-height 0 → 320px
       Opacity 0 → 1
       translateY(20px) → 0

400ms  Complete
       Content scrollable if needed
```

---

## Accessibility Annotations

### Focus Order
```
1. [×] Close button
2. [←] Previous arrow (desktop only)
3. [→] Next arrow (desktop only)
4. [●][○][○][○][○] Pagination bullets (5 buttons)
5. [📍] Metadata toggle
6. [Panel Content] Metadata panel items
   ↺ Back to Close button
```

### ARIA Live Region
```
┌─────────────────────────────────┐
│ (Hidden visually)               │
│                                 │
│ Screen Reader Announcement:     │
│ "Viewing image 3 of 12"         │
│                                 │
│ Updates on each slide change    │
└─────────────────────────────────┘
```

---

## Browser/Device Testing Matrix

| Device | Viewport | Priority | Test Focus |
|--------|----------|----------|------------|
| iPhone SE | 375×667 | HIGH | Touch gestures, fullscreen |
| iPhone 12 Pro | 390×844 | HIGH | Swipe feel, touch targets |
| iPad Mini | 768×1024 | MEDIUM | Tablet layout transition |
| iPad Pro | 1024×1366 | MEDIUM | Large tablet experience |
| Desktop 1080p | 1920×1080 | HIGH | Nav arrows, keyboard |
| Desktop 4K | 3840×2160 | LOW | Max width limit (1200px) |

---

## Design Asset Checklist

- [ ] Fonts loaded: Staatliches, Archivo
- [ ] Emojis rendering correctly: 📍📅👤🗺️▶
- [ ] Arrow symbols: ← → × (Unicode)
- [ ] Colors defined in CSS variables
- [ ] Backdrop blur supported (fallback solid color)
- [ ] VideoJS theme customized
- [ ] Swiper library loaded from CDN
- [ ] Bootstrap 5 modal included

---

**Visual Reference Complete!**

For full technical specifications, see:
- `modal-viewer-swiper-ux-ui.md` (Complete design specs)
- `THEMER_QUICK_REFERENCE.md` (Implementation checklist)
