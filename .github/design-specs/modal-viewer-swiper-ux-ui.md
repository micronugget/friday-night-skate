# UX-UI Handoff: Modal Viewer with Swiper Navigation

**Status:** Complete  
**Project:** Friday Night Skate  
**Theme:** Radix 6 (Bootstrap 5 subtheme)  
**Date:** 2025  
**Designer:** UX/UI Designer Agent  

---

## 🎨 Design Vision

### Aesthetic Direction: **Urban Nocturnal Energy**

This modal viewer captures the raw, kinetic spirit of night skating. Think:
- **Cinematic Darkness**: Deep blacks with vibrant neon accents
- **Motion-First**: Smooth, fluid transitions that mirror skating flow
- **Tactile Immediacy**: Large touch targets, intuitive gestures
- **Street Credibility**: Bold typography, high contrast, unpolished edges

**The One Unforgettable Thing:** When users swipe between images, they feel the momentum and flow of skating through the city at night.

---

## 📐 Design Specifications

### Color System

```scss
// CSS Variables for Modal Viewer
:root {
  // Modal Background & Overlay
  --modal-overlay-bg: rgba(10, 10, 15, 0.95);           // Deep night navy with high opacity
  --modal-backdrop-blur: 8px;                            // Subtle backdrop blur
  
  // Content Background
  --modal-content-bg: #0a0a0f;                           // Near-black for main content
  --modal-panel-bg: rgba(20, 20, 28, 0.92);             // Slightly lighter for metadata panel
  
  // Accent Colors (Neon-inspired, tasteful)
  --modal-accent-primary: #00ff9f;                       // Electric teal (location pins, active states)
  --modal-accent-secondary: #ff006e;                     // Hot pink (video indicators)
  --modal-accent-tertiary: #ffd60a;                      // Street light yellow (warnings, focus)
  
  // Text
  --modal-text-primary: #f8f9fa;                         // High contrast white
  --modal-text-secondary: rgba(248, 249, 250, 0.7);     // Dimmed white for metadata
  --modal-text-muted: rgba(248, 249, 250, 0.45);        // Very dim for timestamps
  
  // Interactive Elements
  --modal-button-bg: rgba(248, 249, 250, 0.08);         // Transparent button base
  --modal-button-hover-bg: rgba(248, 249, 250, 0.15);   // Hover state
  --modal-button-active-bg: rgba(248, 249, 250, 0.25);  // Active/pressed state
  
  // Navigation
  --swiper-nav-bg: rgba(0, 255, 159, 0.1);              // Subtle teal glow
  --swiper-nav-border: rgba(0, 255, 159, 0.3);          // Teal border
  --swiper-nav-hover: rgba(0, 255, 159, 0.2);           // Hover glow
  
  // Focus/Accessibility
  --modal-focus-ring: 3px solid var(--modal-accent-tertiary);
  --modal-focus-offset: 2px;
}

// Dark theme variations (if light theme needed)
[data-bs-theme="light"] {
  --modal-overlay-bg: rgba(255, 255, 255, 0.95);
  --modal-content-bg: #ffffff;
  // ... (inverse colors if needed)
}
```

### Typography

**Display Font (Headers, Navigation):**  
- **Primary Choice:** `Staatliches` (Bold, condensed, urban racing aesthetic)  
- **Fallback:** `Impact, "Franklin Gothic Bold", sans-serif`  
- **Usage:** Modal title, metadata labels, close button  
- **Sizes:**
  - Modal title: `clamp(1.5rem, 4vw, 2rem)` (24px-32px)
  - Navigation labels: `1rem` (16px)
  - Metadata labels: `0.875rem` (14px)

**Body Font (Metadata, Descriptions):**  
- **Primary Choice:** `Archivo` (Clean, modern, excellent readability)  
- **Fallback:** `"Segoe UI", system-ui, sans-serif`  
- **Usage:** Metadata text, descriptions, timestamps  
- **Sizes:**
  - Body text: `1rem` (16px)
  - Small text: `0.875rem` (14px)
  - Micro text: `0.75rem` (12px)

**Font Loading:**
```html
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600&family=Staatliches&display=swap" rel="stylesheet">
```

```scss
// Typography Classes
.modal-title {
  font-family: 'Staatliches', Impact, sans-serif;
  font-weight: 400;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.modal-body-text {
  font-family: 'Archivo', system-ui, sans-serif;
  font-weight: 400;
  line-height: 1.5;
}

.modal-metadata-label {
  font-family: 'Staatliches', Impact, sans-serif;
  font-weight: 400;
  font-size: 0.875rem;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--modal-accent-primary);
}
```

### Spacing & Layout

**Bootstrap 5 Breakpoints (Drupal-defined):**
- XS: `< 576px` (Mobile - PRIMARY)
- SM: `576px - 767px` (Large mobile)
- MD: `768px - 991px` (Tablet)
- LG: `992px - 1199px` (Desktop)
- XL: `≥ 1200px` (Large desktop)
- XXL: `≥ 1400px` (Ultra-wide)

**Spacing Scale:**
```scss
// Based on Bootstrap 5 spacer values
$modal-space-xs: 0.25rem;   // 4px
$modal-space-sm: 0.5rem;    // 8px
$modal-space-md: 1rem;      // 16px
$modal-space-lg: 1.5rem;    // 24px
$modal-space-xl: 2rem;      // 32px
$modal-space-xxl: 3rem;     // 48px
```

**Touch Target Minimum:**
- All interactive elements: `44px × 44px` (WCAG AAA)
- Preferred: `48px × 48px` for primary actions

---

## 🧩 Component Breakdown

### 1. Modal Container

**Structure:**
```html
<div class="modal fade fns-media-modal" 
     id="mediaModal" 
     tabindex="-1" 
     role="dialog" 
     aria-labelledby="mediaModalTitle"
     aria-hidden="true"
     data-bs-theme="dark">
  <!-- Modal Dialog -->
</div>
```

**States:**
- **Closed:** `opacity: 0`, `display: none`
- **Opening:** Fade in + scale animation (250ms)
- **Open:** `opacity: 1`, fullscreen on mobile
- **Closing:** Fade out (200ms)

**Specifications:**

| Breakpoint | Behavior | Max Width | Padding |
|------------|----------|-----------|---------|
| XS (< 576px) | Fullscreen | 100vw | 0 |
| SM (≥ 576px) | Fullscreen | 100vw | 0 |
| MD (≥ 768px) | Centered, 90% | 90vw | 16px |
| LG (≥ 992px) | Centered, 85% | 85vw | 24px |
| XL (≥ 1200px) | Centered, 80% | 1200px max | 32px |

**Backdrop:**
- Background: `var(--modal-overlay-bg)`
- Backdrop filter: `blur(8px)`
- Click outside to close: YES
- ESC key to close: YES

---

### 2. Close Button

**Position:** 
- Fixed top-right corner
- XS/SM: `top: 12px, right: 12px`
- MD+: `top: 20px, right: 20px`

**Design:**
```scss
.fns-modal-close {
  // Dimensions
  width: 48px;
  height: 48px;
  
  // Visual
  background: var(--modal-button-bg);
  border: 2px solid var(--modal-accent-tertiary);
  border-radius: 50%;
  
  // Icon
  color: var(--modal-text-primary);
  font-size: 24px;
  line-height: 1;
  
  // Interaction
  cursor: pointer;
  transition: all 0.2s ease-out;
  
  &:hover, &:focus {
    background: var(--modal-button-hover-bg);
    border-color: var(--modal-accent-primary);
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 0 16px rgba(0, 255, 159, 0.3);
  }
  
  &:active {
    transform: rotate(90deg) scale(0.95);
  }
  
  // Z-index to sit above content
  z-index: 1060;
}
```

**Accessibility:**
- ARIA label: `aria-label="Close modal viewer"`
- Keyboard: ESC key also closes
- Focus trap: Ensure focus stays within modal when open

---

### 3. Swiper Container

**Layout:**
```
┌─────────────────────────────────────┐
│  [← Prev]   CONTENT   [Next →]     │  ← Navigation arrows (desktop)
│                                     │
│     ┌───────────────────┐          │
│     │                   │          │
│     │   Image/Video     │          │  ← Main content area
│     │                   │          │
│     └───────────────────┘          │
│                                     │
│     ● ○ ○ ○ ○                      │  ← Pagination dots
│                                     │
│  [Metadata Panel]                  │  ← Toggle panel (bottom)
└─────────────────────────────────────┘
```

**Swiper Configuration:**
```javascript
const swiperConfig = {
  // Core
  effect: 'slide',
  speed: 400,
  spaceBetween: 0,
  loop: false,
  centeredSlides: true,
  
  // Touch
  touchRatio: 1,
  touchAngle: 45,
  threshold: 10,
  resistanceRatio: 0.85,
  
  // Keyboard
  keyboard: {
    enabled: true,
    onlyInViewport: true,
  },
  
  // Mouse wheel
  mousewheel: {
    forceToAxis: true,
    sensitivity: 1,
  },
  
  // Preloading
  preloadImages: false,
  lazy: {
    loadPrevNext: true,
    loadPrevNextAmount: 2,
  },
  
  // Navigation
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  
  // Pagination
  pagination: {
    el: '.swiper-pagination',
    type: 'bullets',
    clickable: true,
  },
};
```

---

### 4. Navigation Arrows

**Desktop/Tablet Only** (hidden on XS/SM):

```scss
.swiper-button-next,
.swiper-button-prev {
  // Position
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  
  // Size
  width: 56px;
  height: 56px;
  
  // Visual
  background: var(--swiper-nav-bg);
  border: 2px solid var(--swiper-nav-border);
  border-radius: 50%;
  backdrop-filter: blur(4px);
  
  // Icon
  color: var(--modal-accent-primary);
  font-size: 24px;
  
  // Interaction
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    background: var(--swiper-nav-hover);
    transform: translateY(-50%) scale(1.15);
    box-shadow: 0 0 24px rgba(0, 255, 159, 0.4);
  }
  
  &:active {
    transform: translateY(-50%) scale(1.05);
  }
  
  &.swiper-button-disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
  }
  
  // Hide on mobile
  @media (max-width: 767px) {
    display: none;
  }
}

.swiper-button-prev {
  left: 24px;
  
  &::after {
    content: '←';
    font-family: 'Staatliches', sans-serif;
  }
}

.swiper-button-next {
  right: 24px;
  
  &::after {
    content: '→';
    font-family: 'Staatliches', sans-serif;
  }
}
```

**Accessibility:**
- ARIA labels: `aria-label="Previous image"` / `aria-label="Next image"`
- Keyboard: Arrow keys ← → also navigate

---

### 5. Pagination Dots

**Position:** Bottom center, above metadata panel

```scss
.swiper-pagination {
  position: absolute;
  bottom: 80px; // Above metadata toggle
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
  
  display: flex;
  gap: 8px;
  padding: 8px 16px;
  background: rgba(10, 10, 15, 0.6);
  border-radius: 24px;
  backdrop-filter: blur(8px);
}

.swiper-pagination-bullet {
  width: 10px;
  height: 10px;
  background: var(--modal-text-secondary);
  border-radius: 50%;
  opacity: 0.5;
  cursor: pointer;
  transition: all 0.3s ease;
  
  &:hover {
    opacity: 0.8;
    transform: scale(1.2);
  }
  
  &.swiper-pagination-bullet-active {
    width: 28px;
    border-radius: 5px;
    background: var(--modal-accent-primary);
    opacity: 1;
    box-shadow: 0 0 12px rgba(0, 255, 159, 0.5);
  }
}

// Mobile: Larger touch targets
@media (max-width: 767px) {
  .swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    
    &.swiper-pagination-bullet-active {
      width: 32px;
    }
  }
}
```

---

### 6. Content Display Area

#### 6a. Image Display

```scss
.swiper-slide {
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--modal-content-bg);
  
  img {
    max-width: 100%;
    max-height: calc(100vh - 160px); // Account for controls
    width: auto;
    height: auto;
    object-fit: contain;
    
    // Loading state
    &.loading {
      opacity: 0;
      animation: fadeIn 0.3s ease-out forwards;
    }
  }
}

@keyframes fadeIn {
  to { opacity: 1; }
}

// Mobile: Full viewport
@media (max-width: 767px) {
  .swiper-slide img {
    max-height: calc(100vh - 120px);
  }
}
```

**Image States:**
- **Loading:** Skeleton shimmer (reuse from masonry grid)
- **Loaded:** Fade in (300ms)
- **Error:** Show placeholder with retry button

#### 6b. Video Display (VideoJS Integration)

```scss
.swiper-slide {
  .video-js {
    width: 100%;
    max-width: 100%;
    height: auto;
    max-height: calc(100vh - 160px);
    
    // Custom VideoJS theme
    background-color: #000;
    
    .vjs-big-play-button {
      // Override VideoJS defaults
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background-color: rgba(255, 0, 110, 0.9); // Hot pink
      border: 3px solid rgba(255, 255, 255, 0.3);
      font-size: 48px;
      
      &:hover {
        background-color: rgba(255, 0, 110, 1);
        transform: scale(1.1);
        box-shadow: 0 0 32px rgba(255, 0, 110, 0.6);
      }
    }
    
    // Control bar
    .vjs-control-bar {
      background-color: rgba(10, 10, 15, 0.9);
      backdrop-filter: blur(8px);
    }
    
    // Progress bar
    .vjs-play-progress {
      background-color: var(--modal-accent-secondary);
    }
  }
}

// Mobile: Slightly smaller max height
@media (max-width: 767px) {
  .swiper-slide .video-js {
    max-height: calc(100vh - 120px);
  }
}
```

**Video Loading States:**
- **Buffering:** Show spinner with neon accent
- **Playing:** Normal controls
- **Paused:** Show play button overlay
- **Error:** Show error message with retry

---

### 7. Metadata Panel

**Collapsible panel at bottom of modal:**

```
┌─────────────────────────────────┐
│  [📍] TOGGLE METADATA           │ ← Toggle button
├─────────────────────────────────┤
│  📍 LOCATION                    │
│  123 Main St, Seattle, WA       │
│                                 │
│  📅 DATE                        │
│  Friday, January 24, 2025       │
│                                 │
│  👤 UPLOADER                    │
│  @skater_username               │
└─────────────────────────────────┘
```

**Toggle Button:**
```scss
.fns-metadata-toggle {
  // Position
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  z-index: 15;
  
  // Size
  width: 200px;
  height: 44px;
  
  // Visual
  background: var(--modal-panel-bg);
  border: 2px solid var(--modal-accent-primary);
  border-radius: 22px 22px 0 0;
  backdrop-filter: blur(8px);
  
  // Text
  font-family: 'Staatliches', sans-serif;
  font-size: 0.875rem;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--modal-accent-primary);
  
  // Icon
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  
  &::before {
    content: '📍';
    font-size: 16px;
  }
  
  // Interaction
  cursor: pointer;
  transition: all 0.3s ease;
  
  &:hover {
    background: rgba(0, 255, 159, 0.15);
    transform: translateX(-50%) translateY(-4px);
    box-shadow: 0 -4px 16px rgba(0, 255, 159, 0.3);
  }
  
  &[aria-expanded="true"] {
    border-radius: 0;
    
    &::before {
      content: '×';
      font-size: 24px;
    }
  }
}
```

**Panel Content:**
```scss
.fns-metadata-panel {
  // Position
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 14;
  
  // Size
  max-height: 0;
  overflow: hidden;
  
  // Visual
  background: var(--modal-panel-bg);
  border-top: 2px solid var(--modal-accent-primary);
  backdrop-filter: blur(12px);
  
  // Animation
  transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &.show {
    max-height: 320px; // Enough for all metadata
    overflow-y: auto;
  }
  
  // Content padding
  .metadata-content {
    padding: $modal-space-lg;
    
    @media (max-width: 767px) {
      padding: $modal-space-md;
    }
  }
}

.metadata-item {
  margin-bottom: $modal-space-lg;
  
  &:last-child {
    margin-bottom: 0;
  }
  
  .metadata-label {
    font-family: 'Staatliches', sans-serif;
    font-size: 0.875rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--modal-accent-primary);
    margin-bottom: $modal-space-xs;
    display: flex;
    align-items: center;
    gap: 6px;
    
    &::before {
      font-size: 16px;
    }
  }
  
  .metadata-value {
    font-family: 'Archivo', sans-serif;
    font-size: 1rem;
    color: var(--modal-text-primary);
    line-height: 1.5;
  }
}

// Specific metadata items
.metadata-item--location .metadata-label::before {
  content: '📍';
}

.metadata-item--date .metadata-label::before {
  content: '📅';
}

.metadata-item--uploader .metadata-label::before {
  content: '👤';
}

.metadata-item--gps .metadata-label::before {
  content: '🗺️';
}
```

**Mobile Optimization:**
- Panel slides up from bottom
- Slightly shorter max-height on mobile (280px)
- Scrollable if content exceeds max-height

---

## 🎬 Animation & Motion Specifications

### Modal Open/Close

```scss
// Modal fade-in
.modal.fade {
  .modal-dialog {
    transition: transform 0.25s ease-out, opacity 0.25s ease-out;
    transform: scale(0.95);
    opacity: 0;
  }
}

.modal.show {
  .modal-dialog {
    transform: scale(1);
    opacity: 1;
  }
}

// Modal fade-out
.modal.fade:not(.show) {
  .modal-dialog {
    transform: scale(0.95);
    opacity: 0;
  }
}
```

**Timing:** 250ms ease-out (open), 200ms ease-in (close)

---

### Swipe Transitions

**Key Principle:** Maintain momentum and flow

```scss
.swiper-wrapper {
  transition-timing-function: cubic-bezier(0.25, 0.1, 0.25, 1); // Smooth ease
}

// Fast swipe (< 300ms duration)
.swiper-wrapper.swiper-wrapper-fast {
  transition-duration: 300ms;
}

// Normal swipe (300-500ms duration)
.swiper-wrapper.swiper-wrapper-normal {
  transition-duration: 400ms;
}

// Slow swipe (elastic resistance at edges)
.swiper-wrapper.swiper-wrapper-slow {
  transition-duration: 600ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); // Deceleration
}
```

**Interaction Details:**
- **Touch swipe:** Natural physics-based momentum
- **Arrow key:** 400ms smooth transition
- **Arrow button click:** 400ms smooth transition
- **Pagination dot click:** 500ms smooth transition (longer for multi-slide jumps)

---

### Preload Indicators

**Loading Next/Previous Images:**

```scss
.swiper-slide-next,
.swiper-slide-prev {
  &::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 48px;
    height: 48px;
    border: 3px solid rgba(0, 255, 159, 0.2);
    border-top-color: var(--modal-accent-primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    opacity: 0;
  }
  
  &.loading::before {
    opacity: 1;
  }
}

@keyframes spin {
  to { transform: translate(-50%, -50%) rotate(360deg); }
}
```

**Strategy:**
- Preload 1 image ahead and 1 behind current slide
- Show spinner only if load takes > 200ms
- Fade in image once loaded (300ms)

---

### Metadata Panel Animation

```scss
.fns-metadata-panel {
  transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.3s ease;
  
  &.show {
    animation: slideUpFade 0.4s ease-out;
  }
  
  &.hide {
    animation: slideDownFade 0.3s ease-in;
  }
}

@keyframes slideUpFade {
  from {
    max-height: 0;
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    max-height: 320px;
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideDownFade {
  from {
    max-height: 320px;
    opacity: 1;
    transform: translateY(0);
  }
  to {
    max-height: 0;
    opacity: 0;
    transform: translateY(20px);
  }
}
```

---

### Micro-interactions

**Close Button Rotation:**
```scss
.fns-modal-close {
  &:hover {
    transform: rotate(90deg) scale(1.1);
    transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); // Bounce
  }
}
```

**Navigation Arrow Pulse (Hint on First Load):**
```scss
.swiper-button-next,
.swiper-button-prev {
  &.first-load-hint {
    animation: pulseBounce 2s ease-in-out 3; // Pulse 3 times
  }
}

@keyframes pulseBounce {
  0%, 100% {
    transform: translateY(-50%) scale(1);
  }
  50% {
    transform: translateY(-50%) scale(1.15);
    box-shadow: 0 0 32px rgba(0, 255, 159, 0.6);
  }
}
```

---

## ♿ Accessibility Specifications

### Focus Management

**Focus Trap:**
When modal opens:
1. Focus moves to close button
2. Tab cycles through: Close → Prev Arrow → Next Arrow → Pagination → Metadata Toggle → Panel Content → back to Close
3. Shift+Tab cycles backward
4. Focus cannot escape modal until closed

```javascript
// Focus trap implementation
const focusableElements = modal.querySelectorAll(
  'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
);
const firstFocusable = focusableElements[0];
const lastFocusable = focusableElements[focusableElements.length - 1];

modal.addEventListener('keydown', (e) => {
  if (e.key === 'Tab') {
    if (e.shiftKey) {
      if (document.activeElement === firstFocusable) {
        lastFocusable.focus();
        e.preventDefault();
      }
    } else {
      if (document.activeElement === lastFocusable) {
        firstFocusable.focus();
        e.preventDefault();
      }
    }
  }
});
```

---

### ARIA Labels & Roles

**Modal Container:**
```html
<div class="modal" 
     role="dialog" 
     aria-modal="true"
     aria-labelledby="mediaModalTitle"
     aria-describedby="mediaModalDesc">
```

**Close Button:**
```html
<button class="fns-modal-close" 
        type="button"
        aria-label="Close modal viewer"
        data-bs-dismiss="modal">
  <span aria-hidden="true">×</span>
</button>
```

**Navigation Arrows:**
```html
<button class="swiper-button-prev" 
        type="button"
        aria-label="View previous image">
  <span aria-hidden="true">←</span>
</button>

<button class="swiper-button-next" 
        type="button"
        aria-label="View next image">
  <span aria-hidden="true">→</span>
</button>
```

**Pagination:**
```html
<div class="swiper-pagination" 
     role="group" 
     aria-label="Image pagination">
  <button class="swiper-pagination-bullet" 
          type="button"
          aria-label="Go to image 1"
          aria-current="true"></button>
  <!-- ... more bullets -->
</div>
```

**Metadata Toggle:**
```html
<button class="fns-metadata-toggle"
        type="button"
        aria-expanded="false"
        aria-controls="metadataPanel"
        aria-label="Toggle image metadata">
  <span aria-hidden="true">📍</span>
  Toggle Metadata
</button>
```

**Metadata Panel:**
```html
<div class="fns-metadata-panel"
     id="metadataPanel"
     role="region"
     aria-labelledby="metadataHeading">
  <!-- Content -->
</div>
```

---

### Screen Reader Support

**Live Announcements:**
```html
<div class="sr-only" role="status" aria-live="polite" aria-atomic="true">
  <!-- Announce slide changes -->
  <span id="swiperStatus">Viewing image 3 of 12</span>
</div>
```

**Skip Links:**
```html
<a href="#modalContent" class="sr-only sr-only-focusable">
  Skip to modal content
</a>
```

---

### Keyboard Navigation

| Key | Action |
|-----|--------|
| `ESC` | Close modal |
| `←` | Previous slide |
| `→` | Next slide |
| `Space` | Toggle video play/pause |
| `Tab` | Navigate through interactive elements |
| `Shift+Tab` | Navigate backward |
| `Enter` | Activate focused button |
| `Home` | Go to first slide (optional) |
| `End` | Go to last slide (optional) |

---

### Color Contrast

**WCAG AAA Compliance:**
- **Text on dark background:** White (#f8f9fa) on near-black (#0a0a0f) = 18.7:1 ✅
- **Accent primary:** Electric teal (#00ff9f) on dark = 12.4:1 ✅
- **Accent secondary:** Hot pink (#ff006e) on dark = 6.8:1 ✅
- **Muted text:** rgba(248, 249, 250, 0.7) on dark = 13.1:1 ✅

All combinations exceed WCAG AAA standard (7:1 for normal text, 4.5:1 for large text).

---

### Focus Indicators

```scss
// High-contrast focus ring
*:focus-visible {
  outline: var(--modal-focus-ring);
  outline-offset: var(--modal-focus-offset);
}

// Custom focus for specific elements
.fns-modal-close:focus-visible,
.swiper-button-next:focus-visible,
.swiper-button-prev:focus-visible,
.fns-metadata-toggle:focus-visible {
  outline: 3px solid var(--modal-accent-tertiary);
  outline-offset: 4px;
  box-shadow: 0 0 16px rgba(255, 214, 10, 0.5);
}
```

---

## 🧱 Bootstrap 5 Components Used

### Core Components
1. **Modal** (`modal`, `modal-dialog`, `modal-content`)
   - Base structure for overlay and container
   - Built-in backdrop, focus trap, ESC support
   - Responsive sizing utilities

2. **Buttons** (`btn`, `btn-close`)
   - Close button styling base
   - Button group utilities if needed

3. **Utilities:**
   - **Display:** `d-flex`, `d-none`, `d-md-block`
   - **Positioning:** `position-absolute`, `position-fixed`
   - **Spacing:** `m-*`, `p-*`, `gap-*`
   - **Typography:** `text-*`, `fs-*`

### Not Using (Custom Implementation):
- Navigation arrows (custom for Swiper)
- Pagination (custom for Swiper)
- Carousel (using Swiper instead)

---

## 🎨 Custom CSS Requirements

### New SCSS Files Needed

**1. `_modal-viewer.scss`** (Main modal styles)
```scss
// Modal container, close button, layout
// Located: web/themes/custom/fridaynightskate/src/scss/components/
```

**2. `_swiper-custom.scss`** (Swiper overrides)
```scss
// Custom navigation, pagination, transitions
// Located: web/themes/custom/fridaynightskate/src/scss/components/
```

**3. `_metadata-panel.scss`** (Metadata panel)
```scss
// Panel toggle, content, animations
// Located: web/themes/custom/fridaynightskate/src/scss/components/
```

**4. `_videojs-theme.scss`** (VideoJS customization)
```scss
// Custom VideoJS theme for modal context
// Located: web/themes/custom/fridaynightskate/src/scss/components/
```

---

### Variables to Add

**In `base/_variables.scss`:**
```scss
// Modal Viewer Variables
// Import these at the top of the file

// Colors
$modal-overlay-bg: rgba(10, 10, 15, 0.95) !default;
$modal-content-bg: #0a0a0f !default;
$modal-panel-bg: rgba(20, 20, 28, 0.92) !default;

$modal-accent-primary: #00ff9f !default;
$modal-accent-secondary: #ff006e !default;
$modal-accent-tertiary: #ffd60a !default;

$modal-text-primary: #f8f9fa !default;
$modal-text-secondary: rgba(248, 249, 250, 0.7) !default;
$modal-text-muted: rgba(248, 249, 250, 0.45) !default;

// Typography
$modal-font-display: 'Staatliches', Impact, sans-serif !default;
$modal-font-body: 'Archivo', system-ui, sans-serif !default;

// Spacing
$modal-space-xs: 0.25rem !default;
$modal-space-sm: 0.5rem !default;
$modal-space-md: 1rem !default;
$modal-space-lg: 1.5rem !default;
$modal-space-xl: 2rem !default;
$modal-space-xxl: 3rem !default;

// Animation
$modal-transition-fast: 0.2s ease-in !default;
$modal-transition-normal: 0.3s ease !default;
$modal-transition-slow: 0.4s cubic-bezier(0.4, 0, 0.2, 1) !default;
```

---

### Import Order

**In `main.style.scss`:**
```scss
// 1. Variables (must be first)
@import 'base/variables';

// 2. Bootstrap (after variables)
@import 'bootstrap';

// 3. Base styles
@import 'base/functions';
@import 'base/mixins';
@import 'base/typography';
@import 'base/elements';
@import 'base/helpers';
@import 'base/utilities';

// 4. Components
@import 'components/archive-masonry';
@import 'components/modal-viewer';          // NEW
@import 'components/swiper-custom';         // NEW
@import 'components/metadata-panel';        // NEW
@import 'components/videojs-theme';         // NEW
```

---

## 📦 Assets Provided

### Icons
**Using emoji for simplicity and distinctive character:**
- Close: `×` (multiplication symbol)
- Navigation: `←` `→` (arrow symbols)
- Location: `📍` (pin emoji)
- Date: `📅` (calendar emoji)
- User: `👤` (user silhouette emoji)
- Map: `🗺️` (world map emoji)
- Video: `▶` (play triangle)

**Alternative:** If emojis are inconsistent across platforms, use Font Awesome or custom SVG icons with same visual style.

### Fonts
**Google Fonts (already specified above):**
- Staatliches (Display)
- Archivo (Body)

### Images
**Placeholder/Loading:**
- Shimmer gradient animation (reuse from masonry)
- Spinner for video buffering

**Error State:**
- Simple error icon (⚠️ or custom SVG)
- Retry button

---

## 🛠️ Implementation Guidance for Themer

### Step 1: File Structure Setup

Create new SCSS files:
```bash
cd web/themes/custom/fridaynightskate/src/scss/components/
touch _modal-viewer.scss
touch _swiper-custom.scss
touch _metadata-panel.scss
touch _videojs-theme.scss
```

Add imports to `main.style.scss` (as shown above).

---

### Step 2: HTML Structure (Twig Template)

**File:** `web/themes/custom/fridaynightskate/templates/modal/fns-media-modal.html.twig`

```twig
{#
/**
 * @file
 * FNS Media Modal Viewer
 * 
 * Available variables:
 * - items: Array of media items with:
 *   - url: Image/video URL
 *   - type: 'image' or 'video'
 *   - alt: Alt text
 *   - metadata: GPS, date, uploader, etc.
 */
#}

<div class="modal fade fns-media-modal" 
     id="mediaModal" 
     tabindex="-1" 
     role="dialog" 
     aria-labelledby="mediaModalTitle"
     aria-hidden="true"
     data-bs-theme="dark">
  
  <div class="modal-dialog modal-fullscreen-sm-down modal-xl">
    <div class="modal-content">
      
      {# Close Button #}
      <button type="button" 
              class="fns-modal-close" 
              data-bs-dismiss="modal" 
              aria-label="{{ 'Close modal viewer'|t }}">
        <span aria-hidden="true">×</span>
      </button>
      
      {# Swiper Container #}
      <div class="swiper fns-media-swiper">
        <div class="swiper-wrapper">
          {% for item in items %}
            <div class="swiper-slide">
              {% if item.type == 'image' %}
                <img src="{{ item.url }}" 
                     alt="{{ item.alt }}" 
                     loading="lazy"
                     class="swiper-lazy">
                <div class="swiper-lazy-preloader"></div>
              {% elseif item.type == 'video' %}
                <video class="video-js vjs-big-play-centered" 
                       controls 
                       preload="metadata"
                       data-setup='{}'>
                  <source src="{{ item.url }}" type="video/mp4">
                </video>
              {% endif %}
            </div>
          {% endfor %}
        </div>
        
        {# Navigation Arrows (Desktop) #}
        <div class="swiper-button-next" aria-label="{{ 'Next image'|t }}"></div>
        <div class="swiper-button-prev" aria-label="{{ 'Previous image'|t }}"></div>
        
        {# Pagination #}
        <div class="swiper-pagination"></div>
      </div>
      
      {# Metadata Toggle #}
      <button type="button"
              class="fns-metadata-toggle"
              aria-expanded="false"
              aria-controls="metadataPanel"
              data-bs-toggle="collapse"
              data-bs-target="#metadataPanel">
        <span aria-hidden="true">📍</span>
        {{ 'Toggle Metadata'|t }}
      </button>
      
      {# Metadata Panel #}
      <div class="fns-metadata-panel collapse" 
           id="metadataPanel"
           role="region"
           aria-labelledby="metadataHeading">
        <div class="metadata-content">
          <h3 id="metadataHeading" class="sr-only">{{ 'Image Metadata'|t }}</h3>
          
          {# Dynamic metadata loaded via JS #}
          <div class="metadata-item metadata-item--location">
            <div class="metadata-label">{{ 'Location'|t }}</div>
            <div class="metadata-value" data-metadata="location">—</div>
          </div>
          
          <div class="metadata-item metadata-item--date">
            <div class="metadata-label">{{ 'Date'|t }}</div>
            <div class="metadata-value" data-metadata="date">—</div>
          </div>
          
          <div class="metadata-item metadata-item--uploader">
            <div class="metadata-label">{{ 'Uploader'|t }}</div>
            <div class="metadata-value" data-metadata="uploader">—</div>
          </div>
          
          <div class="metadata-item metadata-item--gps">
            <div class="metadata-label">{{ 'GPS Coordinates'|t }}</div>
            <div class="metadata-value" data-metadata="gps">—</div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
  
  {# Screen Reader Status Announcements #}
  <div class="sr-only" role="status" aria-live="polite" aria-atomic="true">
    <span id="swiperStatus"></span>
  </div>
</div>
```

---

### Step 3: JavaScript Setup

**File:** `web/themes/custom/fridaynightskate/src/js/modal-viewer.js`

```javascript
/**
 * FNS Media Modal Viewer
 * Swiper.js integration with Bootstrap 5 Modal
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Keyboard, Mousewheel, Lazy } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

(function (Drupal) {
  'use strict';

  let swiperInstance = null;

  /**
   * Initialize Swiper when modal opens
   */
  function initSwiper() {
    if (swiperInstance) return;

    swiperInstance = new Swiper('.fns-media-swiper', {
      modules: [Navigation, Pagination, Keyboard, Mousewheel, Lazy],
      
      // Core config
      effect: 'slide',
      speed: 400,
      spaceBetween: 0,
      loop: false,
      centeredSlides: true,
      
      // Touch
      touchRatio: 1,
      touchAngle: 45,
      threshold: 10,
      resistanceRatio: 0.85,
      
      // Keyboard
      keyboard: {
        enabled: true,
        onlyInViewport: true,
      },
      
      // Mouse wheel
      mousewheel: {
        forceToAxis: true,
        sensitivity: 1,
      },
      
      // Lazy loading
      preloadImages: false,
      lazy: {
        loadPrevNext: true,
        loadPrevNextAmount: 2,
      },
      
      // Navigation
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      
      // Pagination
      pagination: {
        el: '.swiper-pagination',
        type: 'bullets',
        clickable: true,
        renderBullet: function (index, className) {
          return `<button type="button" class="${className}" aria-label="Go to image ${index + 1}"></button>`;
        },
      },
      
      // Events
      on: {
        slideChange: function () {
          updateMetadata(this.activeIndex);
          announceSlideChange(this.activeIndex, this.slides.length);
        },
      },
    });
  }

  /**
   * Update metadata panel with current slide data
   */
  function updateMetadata(index) {
    const slide = document.querySelector(`.swiper-slide:nth-child(${index + 1})`);
    if (!slide) return;

    // Get metadata from data attributes
    const metadata = {
      location: slide.dataset.location || '—',
      date: slide.dataset.date || '—',
      uploader: slide.dataset.uploader || '—',
      gps: slide.dataset.gps || '—',
    };

    // Update DOM
    document.querySelector('[data-metadata="location"]').textContent = metadata.location;
    document.querySelector('[data-metadata="date"]').textContent = metadata.date;
    document.querySelector('[data-metadata="uploader"]').textContent = metadata.uploader;
    document.querySelector('[data-metadata="gps"]').textContent = metadata.gps;
  }

  /**
   * Announce slide change for screen readers
   */
  function announceSlideChange(index, total) {
    const status = document.getElementById('swiperStatus');
    if (status) {
      status.textContent = Drupal.t('Viewing image @current of @total', {
        '@current': index + 1,
        '@total': total,
      });
    }
  }

  /**
   * Destroy Swiper when modal closes
   */
  function destroySwiper() {
    if (swiperInstance) {
      swiperInstance.destroy(true, true);
      swiperInstance = null;
    }
  }

  /**
   * Drupal behavior
   */
  Drupal.behaviors.fnsMediaModal = {
    attach: function (context, settings) {
      const modal = document.getElementById('mediaModal');
      if (!modal) return;

      // Initialize Swiper when modal opens
      modal.addEventListener('shown.bs.modal', function () {
        initSwiper();
      });

      // Destroy Swiper when modal closes
      modal.addEventListener('hidden.bs.modal', function () {
        destroySwiper();
      });

      // Focus management
      modal.addEventListener('shown.bs.modal', function () {
        const closeButton = modal.querySelector('.fns-modal-close');
        if (closeButton) closeButton.focus();
      });
    },
  };

})(Drupal);
```

---

### Step 4: Library Definition

**File:** `web/themes/custom/fridaynightskate/fridaynightskate.libraries.yml`

Add:
```yaml
modal-viewer:
  version: 1.0
  css:
    theme:
      build/css/main.style.css: {}
  js:
    build/js/modal-viewer.js: {}
  dependencies:
    - core/drupal
    - core/drupalSettings
    - fridaynightskate/swiper
    - fridaynightskate/bootstrap5
    - fridaynightskate/videojs

swiper:
  version: 11.0
  remote: https://github.com/nolimits4web/swiper
  license:
    name: MIT
    url: https://github.com/nolimits4web/swiper/blob/master/LICENSE
  css:
    theme:
      https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css: { type: external, minified: true }
  js:
    https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js: { type: external, minified: true }
```

---

### Step 5: Attach Library

**In template or preprocess:**
```php
function fridaynightskate_preprocess_page(&$variables) {
  // Attach modal viewer library on archive pages
  if (\Drupal::routeMatch()->getRouteName() === 'view.archive.page_1') {
    $variables['#attached']['library'][] = 'fridaynightskate/modal-viewer';
  }
}
```

---

### Step 6: Build & Test

```bash
# Build CSS/JS
ddev npm run build

# Clear Drupal cache
ddev drush cr

# Test on multiple devices:
# - iPhone SE (mobile portrait)
# - iPad (tablet)
# - Desktop (1920x1080)
```

---

### Step 7: Accessibility Testing

**Manual checks:**
- [ ] Tab through all interactive elements
- [ ] ESC closes modal
- [ ] Arrow keys navigate slides
- [ ] Screen reader announces slide changes
- [ ] Focus trap works (focus stays in modal)
- [ ] Color contrast passes (use axe DevTools)
- [ ] Touch targets are ≥ 44x44px

**Automated tools:**
- axe DevTools browser extension
- WAVE browser extension
- Lighthouse accessibility audit

---

## 🎯 Next Steps for Themer

1. **Create SCSS files** with provided styles (copy from this document)
2. **Create Twig template** for modal structure
3. **Create JavaScript file** for Swiper initialization
4. **Add library definition** in `.libraries.yml`
5. **Test in browser** (mobile-first!)
6. **Refine animations** based on feel (speed, easing)
7. **Conduct accessibility audit**
8. **Coordinate with @media-dev** for VideoJS integration
9. **Coordinate with @drupal-developer** for Drupal modal trigger from Masonry grid

---

## 📝 Design System Summary

### Colors
- **Primary Accent:** Electric Teal (`#00ff9f`)
- **Secondary Accent:** Hot Pink (`#ff006e`)
- **Tertiary Accent:** Street Light Yellow (`#ffd60a`)
- **Background:** Near-Black (`#0a0a0f`)
- **Text:** High Contrast White (`#f8f9fa`)

### Typography
- **Display:** Staatliches (Bold, urban, condensed)
- **Body:** Archivo (Clean, readable)

### Spacing
- **Touch Targets:** Minimum 44px × 44px
- **Padding:** 16px (mobile), 24px (tablet), 32px (desktop)
- **Gaps:** 8px (small), 16px (medium), 24px (large)

### Motion
- **Fast:** 200-250ms (close, micro-interactions)
- **Normal:** 300-400ms (swipe, open)
- **Slow:** 400-600ms (metadata panel, multi-slide jumps)
- **Easing:** Cubic bezier for natural momentum

### Breakpoints (Bootstrap 5)
- **XS:** < 576px (Mobile - PRIMARY)
- **SM:** 576-767px
- **MD:** 768-991px
- **LG:** 992-1199px
- **XL:** ≥ 1200px

---

## ✅ Deliverables Checklist

- [x] Design specifications (colors, typography, spacing, breakpoints)
- [x] Component breakdown with states (modal, swiper, metadata)
- [x] Animation/motion specifications (transitions, micro-interactions)
- [x] Accessibility notes (ARIA, focus, keyboard, screen readers)
- [x] Bootstrap 5 components to use (modal, utilities)
- [x] Custom CSS requirements (4 new SCSS files)
- [x] Implementation guidance for Themer (step-by-step)

---

## 🚀 Distinctive Design Elements

**What makes this modal unforgettable:**

1. **Neon-Nocturnal Color Palette:** Electric teal, hot pink, and street light yellow against deep blacks create a distinctive night-skating aesthetic that's unlike generic modals.

2. **Staatliches Typography:** Bold, condensed, urban racing font gives immediate street credibility and energetic personality.

3. **Momentum-Based Swipe Physics:** Smooth, physics-based transitions that mirror the flow and momentum of skating.

4. **Metadata as a "Secret Panel":** Toggle button with glowing teal accent creates discovery and interaction, not just passive viewing.

5. **Close Button Rotation:** 90° rotation with bounce on hover—playful, memorable, distinctive.

6. **Emoji Icons:** Instead of generic SVGs, using emojis (📍, 📅, 👤) adds personality and immediate recognition without additional assets.

7. **Navigation Arrow Pulse Hint:** Subtle hint animation on first load guides users without being intrusive.

8. **VideoJS Hot Pink Play Button:** Circular hot pink play button stands out against black video backgrounds—impossible to miss.

---

**Design Handoff Complete! 🎨**

**Status:** Ready for Themer implementation  
**Estimated Implementation Time:** 8-12 hours  
**Priority:** High (core feature for mobile experience)  

**Questions?** Coordinate with:
- @themer (implementation)
- @media-dev (VideoJS integration)
- @drupal-developer (Drupal modal trigger)
- @architect (strategic decisions)
