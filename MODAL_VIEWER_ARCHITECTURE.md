# Modal Viewer Architecture

## Component Flow Diagram

```
┌──────────────────────────────────────────────────────────────┐
│                     Archive View Page                         │
│  ┌────────────────────────────────────────────────────────┐  │
│  │           Masonry Grid (views-view--archive)           │  │
│  │                                                         │  │
│  │  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐  │  │
│  │  │ Item 1  │  │ Item 2  │  │ Item 3  │  │ Item 4  │  │  │
│  │  │ [Image] │  │ [Video] │  │ [Image] │  │ [Image] │  │  │
│  │  │ data-*  │  │ data-*  │  │ data-*  │  │ data-*  │  │  │
│  │  └────┬────┘  └────┬────┘  └────┬────┘  └────┬────┘  │  │
│  │       │            │            │            │        │  │
│  └───────┼────────────┼────────────┼────────────┼────────┘  │
│          │            │            │            │           │
│          └────────────┴────────────┴────────────┘           │
│                       │ Click/Enter                          │
│                       ▼                                      │
│  ┌────────────────────────────────────────────────────────┐ │
│  │        Modal Viewer (Bootstrap 5 + Swiper.js)          │ │
│  │                                                         │ │
│  │  ┌─────────────────────────────────────────────────┐  │ │
│  │  │ Close Button (X) - ESC to close    [Focus Trap] │  │ │
│  │  └─────────────────────────────────────────────────┘  │ │
│  │                                                         │ │
│  │  ◄─────┐   ┌──────────────────────────┐   ┌─────►    │ │
│  │  Prev  │   │     Swiper Container     │   │ Next     │ │
│  │  Arrow │   │                          │   │ Arrow    │ │
│  │        │   │  ┌────────────────────┐  │   │          │ │
│  │        │   │  │   Active Slide     │  │   │          │ │
│  │        │   │  │                    │  │   │          │ │
│  │        │   │  │  [Image/VideoJS]   │  │   │          │ │
│  │        │   │  │                    │  │   │          │ │
│  │        │   │  └────────────────────┘  │   │          │ │
│  │        │   │                          │   │          │ │
│  │        │   │  ● ○ ○ ○ (Pagination)   │   │          │ │
│  │        │   └──────────────────────────┘   │          │ │
│  │  ◄─────┘        ▲          ▲              └─────►    │ │
│  │                 │          │                         │ │
│  │            Swipe L/R   Arrow Keys                    │ │
│  │                                                       │ │
│  │  ┌──────────────────────────────┐   ┌────────────┐  │ │
│  │  │     Metadata Panel           │◄──┤ Info Btn   │  │ │
│  │  │  • Date: Jan 15, 2026        │   │ (Toggle)   │  │ │
│  │  │  • Location: Downtown        │   └────────────┘  │ │
│  │  │  • GPS: 45.52,-122.67        │                   │ │
│  │  │  • Uploader: John Doe        │                   │ │
│  │  └──────────────────────────────┘                   │ │
│  └─────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────┘
```

## Data Flow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. PAGE LOAD                                                 │
│    • Drupal renders archive view                             │
│    • view.theme preprocess adds data attributes             │
│    • modal-viewer.js attaches to masonry items              │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. USER CLICKS ITEM                                          │
│    • modalViewer behavior captures click                    │
│    • Extract item index from click target                   │
│    • Call openModal(index)                                  │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. MODAL CREATION (First Time Only)                         │
│    • Create modal HTML structure                            │
│    • Inject into document.body                              │
│    • Setup event listeners                                  │
│    • Setup Bootstrap modal instance                         │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. BUILD SLIDES                                              │
│    • Query all .masonry-item elements                       │
│    • For each item:                                         │
│      - Read data attributes                                 │
│      - Create slide HTML                                    │
│      - For images: use data-fullsize URL                    │
│      - For videos: create VideoJS element                   │
│      - Store metadata in slide dataset                      │
│    • Append slides to swiper-wrapper                        │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. INITIALIZE SWIPER                                         │
│    • Create Swiper instance                                 │
│    • Enable modules: Navigation, Keyboard, A11y             │
│    • Configure loop, speed, lazy loading                    │
│    • Setup event handlers                                   │
│    • Navigate to clicked item index                         │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. SHOW MODAL                                                │
│    • Bootstrap modal.show()                                 │
│    • Trap focus in modal                                    │
│    • Update metadata for current slide                      │
│    • Initialize VideoJS if current slide is video           │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. USER NAVIGATION                                           │
│    • Swipe/Arrow/Click triggers slideChange                 │
│    • Swiper handler:                                        │
│      - Dispose current VideoJS player (if exists)           │
│      - Update metadata panel                                │
│      - Initialize new VideoJS player (if video)             │
│    • Lazy load next/prev images                             │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. MODAL CLOSE                                               │
│    • User clicks X, ESC, or backdrop                        │
│    • Bootstrap modal.hide()                                 │
│    • Cleanup:                                               │
│      - Dispose VideoJS player                               │
│      - Destroy Swiper instance                              │
│      - Restore focus to original trigger                    │
└─────────────────────────────────────────────────────────────┘
```

## File Dependency Tree

```
fridaynightskate theme
│
├── package.json
│   └── swiper: ^12.1.0
│
├── webpack.mix.js
│   └── Builds: src/js/modal-viewer.js → build/js/modal-viewer.js
│
├── fridaynightskate.libraries.yml
│   └── modal-viewer:
│       ├── build/js/modal-viewer.js
│       ├── build/css/main.style.css
│       └── dependencies: [drupal, once, drupalSettings]
│
├── templates/views/
│   └── views-view--archive-by-date.html.twig
│       └── {{ attach_library('fridaynightskate/modal-viewer') }}
│
├── includes/
│   └── view.theme
│       └── fridaynightskate_preprocess_views_view_unformatted()
│           └── Adds data-* attributes to masonry items
│
├── src/js/
│   └── modal-viewer.js
│       ├── import Swiper
│       ├── Drupal.behaviors.modalViewer
│       ├── openModal()
│       ├── createModal()
│       ├── buildSlides()
│       ├── initializeSwiper()
│       ├── updateMetadata()
│       ├── cleanupVideoPlayer()
│       └── trapFocus()
│
└── src/scss/components/
    └── _modal-viewer.scss
        ├── .modal-viewer
        ├── #mediaModal
        ├── .modal-swiper
        ├── .swiper-button-*
        ├── .metadata-toggle
        └── .metadata-panel
```

## Interaction Flow

```
Desktop User:
┌──────┐    ┌──────┐    ┌──────┐    ┌──────┐
│Click │───▶│Modal │───▶│Arrow │───▶│Press │
│Item  │    │Opens │    │Keys  │    │ ESC  │
└──────┘    └──────┘    └──────┘    └──────┘
                            │            │
                            │            ▼
                            │        ┌──────┐
                            │        │Modal │
                            │        │Close │
                            │        └──────┘
                            ▼
                        ┌──────┐
                        │View  │
                        │Media │
                        └──────┘

Mobile User:
┌──────┐    ┌──────┐    ┌──────┐    ┌──────┐
│Tap   │───▶│Modal │───▶│Swipe │───▶│Tap X │
│Item  │    │Opens │    │L/R   │    │Button│
└──────┘    └──────┘    └──────┘    └──────┘
                            │            │
                            │            ▼
                            │        ┌──────┐
                            │        │Modal │
                            │        │Close │
                            │        └──────┘
                            ▼
                        ┌──────┐    ┌──────┐
                        │View  │───▶│Tap   │
                        │Media │    │Info  │
                        └──────┘    └──────┘
                                        │
                                        ▼
                                    ┌──────┐
                                    │View  │
                                    │Meta  │
                                    └──────┘
```

## Responsive Breakpoints

```
┌────────────────────────────────────────────────────────────┐
│ XS (<576px) - Mobile Phone                                 │
│  • Modal: Fullscreen                                       │
│  • Navigation: Swipe only (no arrows)                      │
│  • Touch targets: 44×44px minimum                          │
│  • Metadata: Compact panel, bottom                         │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ SM (576-767px) - Large Phone / Small Tablet                │
│  • Modal: Fullscreen below 576px                           │
│  • Navigation: Swipe only (no arrows)                      │
│  • Touch targets: 44×44px                                  │
│  • Metadata: Standard panel                                │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ MD (768-991px) - Tablet                                    │
│  • Modal: Centered (not fullscreen)                        │
│  • Navigation: Arrows + swipe + keyboard                   │
│  • Controls: Standard size                                 │
│  • Metadata: Full panel                                    │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ LG+ (≥992px) - Desktop                                     │
│  • Modal: Centered, max-width 1200px                       │
│  • Navigation: All methods                                 │
│  • Controls: Full size (56px arrows)                       │
│  • Metadata: Full panel with all fields                    │
└────────────────────────────────────────────────────────────┘
```

## Event Lifecycle

```
Modal Open:
  ┌─────────────────┐
  │ User Action     │
  │ (click/enter)   │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Store Focus     │
  │ Reference       │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Create/Get      │
  │ Modal HTML      │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Build Slides    │
  │ from Grid Items │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Init Swiper     │
  │ Go to Index     │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Show Modal      │
  │ (Bootstrap)     │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Trap Focus      │
  │ Update Metadata │
  └─────────────────┘

Slide Change:
  ┌─────────────────┐
  │ Swiper Event:   │
  │ slideChange     │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Dispose Current │
  │ VideoJS Player  │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Update Metadata │
  │ Panel Content   │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Init VideoJS    │
  │ (if video slide)│
  └─────────────────┘

Modal Close:
  ┌─────────────────┐
  │ Bootstrap Event:│
  │ hidden.bs.modal │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Dispose VideoJS │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Destroy Swiper  │
  └────────┬────────┘
           │
           ▼
  ┌─────────────────┐
  │ Restore Focus   │
  │ to Trigger      │
  └─────────────────┘
```

## Browser Compatibility Matrix

```
┌─────────────┬──────────┬──────────┬──────────┬──────────┐
│   Feature   │  Chrome  │ Firefox  │  Safari  │   Edge   │
├─────────────┼──────────┼──────────┼──────────┼──────────┤
│ Swiper      │   90+    │   88+    │   14+    │   90+    │
│ Bootstrap 5 │   90+    │   88+    │   14+    │   90+    │
│ VideoJS     │   80+    │   80+    │   13+    │   80+    │
│ ES6 Modules │   61+    │   60+    │   11+    │   79+    │
│ Touch Events│   Yes    │   Yes    │   Yes    │   Yes    │
│ Keyboard Nav│   Yes    │   Yes    │   Yes    │   Yes    │
│ ARIA        │   Yes    │   Yes    │   Yes    │   Yes    │
└─────────────┴──────────┴──────────┴──────────┴──────────┘

✓ Full Support    ◐ Partial Support    ✗ No Support
```
