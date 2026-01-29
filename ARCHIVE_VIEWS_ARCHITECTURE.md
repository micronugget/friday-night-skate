# Archive Views with Masonry Grid - System Architecture

**Project**: Friday Night Skate Archive  
**Version**: 1.0  
**Date**: 2025-01-29  
**Architect**: System Design Agent  

---

## 1. Executive Summary

This document outlines the architecture for implementing Archive Views with Masonry.js grid layout for the Friday Night Skate project. The system displays Archive Media content (images and videos) in a responsive, masonry-style grid, filtered by Skate Date taxonomy terms.

### Key Technologies
- **Drupal 11**: Content management and Views
- **Radix 6 Theme**: Bootstrap 5-based theming
- **Masonry.js v4.2.2**: Dynamic grid layout
- **ImagesLoaded**: Lazy loading coordination
- **Bootstrap 5**: Responsive breakpoints

---

## 2. Architecture Diagram (Text-Based)

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER REQUEST                             │
│                    /archive/{skate_date_term}                    │
└─────────────────────────────┬───────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DRUPAL LAYER                                │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Views Module: archive_by_date                             │ │
│  │  - Contextual filter: Skate Date taxonomy term             │ │
│  │  - Display: Archive Media nodes                            │ │
│  │  - Format: Unformatted list                                │ │
│  │  - Row style: Fields or Content (thumbnail view mode)      │ │
│  └────────────────────────────┬───────────────────────────────┘ │
└────────────────────────────────┼───────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                      TEMPLATE LAYER                              │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  views-view--archive-by-date.html.twig                     │ │
│  │  - Container: .masonry-grid                                │ │
│  │  - Attach library: fridaynightskate/masonry-archive        │ │
│  │  - Data attributes for configuration                       │ │
│  └────────────────────────────┬───────────────────────────────┘ │
│                                │                                 │
│  ┌────────────────────────────▼───────────────────────────────┐ │
│  │  views-view-fields--archive-by-date.html.twig              │ │
│  │  (or node--archive-media--thumbnail.html.twig)             │ │
│  │  - Grid item: .masonry-item                                │ │
│  │  - Responsive image with lazy loading                      │ │
│  │  - Metadata overlay icon                                   │ │
│  │  - Hover effect classes                                    │ │
│  └────────────────────────────┬───────────────────────────────┘ │
└────────────────────────────────┼───────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                    ASSET PIPELINE (Laravel Mix)                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Input: src/js/archive-masonry.js                          │ │
│  │  Output: build/js/archive-masonry.js                       │ │
│  │  - Bundle Masonry.js + ImagesLoaded                        │ │
│  │  - Drupal.behaviors wrapper                                │ │
│  │  - Responsive breakpoint configuration                     │ │
│  └────────────────────────────┬───────────────────────────────┘ │
│                                │                                 │
│  ┌────────────────────────────▼───────────────────────────────┐ │
│  │  Input: src/scss/components/_archive-masonry.scss          │ │
│  │  Output: build/css/main.style.css                          │ │
│  │  - Grid container styles                                   │ │
│  │  - Item styles with hover effects                          │ │
│  │  - Metadata overlay styles                                 │ │
│  │  - Bootstrap 5 breakpoint integration                      │ │
│  └────────────────────────────┬───────────────────────────────┘ │
└────────────────────────────────┼───────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                      FRONTEND LAYER                              │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Masonry.js Initialization                                 │ │
│  │  - Wait for ImagesLoaded                                   │ │
│  │  - Initialize grid with responsive columns                 │ │
│  │  - xs:1, sm:2, md:3, lg:4, xl:5 columns                   │ │
│  │  - Handle window resize (debounced)                        │ │
│  │  - Re-layout on Drupal AJAX events                         │ │
│  └────────────────────────────┬───────────────────────────────┘ │
│                                │                                 │
│  ┌────────────────────────────▼───────────────────────────────┐ │
│  │  User Interactions                                         │ │
│  │  - Hover: Scale + shadow effects (CSS)                     │ │
│  │  - Click: Open modal (future Swiper.js integration)        │ │
│  │  - Lazy load: IntersectionObserver for images             │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. File Structure

### 3.1 Drupal Module (`web/modules/custom/fns_archive`)

```
fns_archive/
├── config/install/
│   ├── views.view.archive_by_date.yml          ← NEW: Archive view with contextual filter
│   └── (existing configs)
├── fns_archive.module                          ← MODIFY: Add preprocess hooks if needed
└── README.md                                   ← UPDATE: Document new view
```

### 3.2 Theme (`web/themes/custom/fridaynightskate`)

```
fridaynightskate/
├── src/
│   ├── js/
│   │   ├── archive-masonry.js                  ← NEW: Masonry initialization
│   │   └── main.script.js                      ← VERIFY: Import if needed
│   └── scss/
│       └── components/
│           └── _archive-masonry.scss           ← NEW: Grid styles
├── templates/
│   └── views/
│       ├── views-view--archive-by-date.html.twig              ← NEW: Grid container
│       ├── views-view-unformatted--archive-by-date.html.twig  ← NEW: Row wrapper
│       └── views-view-fields--archive-by-date.html.twig       ← NEW: Grid item
│       OR (alternative approach):
│       └── node/
│           └── node--archive-media--thumbnail.html.twig       ← MODIFY: Add masonry classes
├── fridaynightskate.libraries.yml              ← MODIFY: Add masonry-archive library
├── fridaynightskate.theme                      ← MODIFY: Add preprocess functions
└── package.json                                ← MODIFY: Add Masonry + ImagesLoaded deps
```

---

## 4. Component Specifications

### 4.1 Views Configuration

**View Name**: `archive_by_date`  
**Path**: `/archive/%taxonomy_term`  
**Display**: Page

#### Settings:
- **Format**: Unformatted list
- **Show**: Fields (or Content with thumbnail view mode)
- **Items per page**: 50 (with pager)
- **Contextual Filter**: 
  - `field_skate_date` (Taxonomy term ID)
  - When filter value is NOT available: Display empty text
  - When filter value IS available: Override title with taxonomy term name
- **Sort**: `field_timestamp` DESC (newest first)
- **Filters**:
  - Content type = Archive Media
  - Published status = Published
  - Moderation state = Published
- **Fields** (if using Fields format):
  - `field_archive_media` (Media field)
    - Formatter: Rendered entity (thumbnail view mode for images, poster for videos)
    - Link to content: Yes
  - `field_metadata` (Hidden, but used for overlay icon indicator)
  - Node ID (excluded from display, used for data attributes)

#### View Mode Strategy:
**Recommended**: Use existing `thumbnail` view mode for Archive Media nodes
- Already configured in `core.entity_view_display.node.archive_media.thumbnail.yml`
- Shows `field_archive_media` with entity_reference_label formatter
- Can be enhanced via template override

---

### 4.2 JavaScript Module

**File**: `src/js/archive-masonry.js`

```javascript
/**
 * @file
 * Masonry grid layout for Archive views.
 */

import Masonry from 'masonry-layout';
import imagesLoaded from 'imagesloaded';

(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.archiveMasonry = {
    attach: function (context, settings) {
      const grids = once('masonry-init', '.masonry-grid', context);
      
      grids.forEach(function (grid) {
        // Get responsive column config from data attributes or defaults
        const breakpoints = {
          xs: parseInt(grid.dataset.columnsXs) || 1,
          sm: parseInt(grid.dataset.columnsSm) || 2,
          md: parseInt(grid.dataset.columnsMd) || 3,
          lg: parseInt(grid.dataset.columnsLg) || 4,
          xl: parseInt(grid.dataset.columnsXl) || 5
        };
        
        // Calculate current columns based on viewport
        const getColumnCount = () => {
          const width = window.innerWidth;
          if (width >= 1200) return breakpoints.xl;
          if (width >= 992) return breakpoints.lg;
          if (width >= 768) return breakpoints.md;
          if (width >= 576) return breakpoints.sm;
          return breakpoints.xs;
        };
        
        // Initialize ImagesLoaded first
        imagesLoaded(grid, function () {
          // Initialize Masonry
          const masonry = new Masonry(grid, {
            itemSelector: '.masonry-item',
            columnWidth: '.masonry-sizer',
            percentPosition: true,
            gutter: 16, // Bootstrap 5 gutter equivalent
            transitionDuration: '0.3s',
            initLayout: true
          });
          
          // Store instance for later access
          grid.masonryInstance = masonry;
          
          // Re-layout on image load (lazy loading)
          const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
              if (entry.isIntersecting) {
                const img = entry.target;
                img.addEventListener('load', () => {
                  masonry.layout();
                });
              }
            });
          });
          
          grid.querySelectorAll('img[loading="lazy"]').forEach(img => {
            observer.observe(img);
          });
          
          // Handle window resize (debounced)
          let resizeTimer;
          window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
              masonry.layout();
            }, 250);
          });
          
          // Re-layout after Drupal AJAX events
          document.addEventListener('drupalAjaxComplete', () => {
            masonry.layout();
          });
        });
      });
    }
  };
})(Drupal, once);
```

---

### 4.3 SCSS Styling

**File**: `src/scss/components/_archive-masonry.scss`

```scss
/**
 * Archive Masonry Grid Layout
 * Responsive grid with Bootstrap 5 breakpoints
 */

.masonry-grid {
  margin: 0 -8px; // Negative margin for gutter compensation
  
  // Clearfix
  &::after {
    content: '';
    display: block;
    clear: both;
  }
}

// Sizing element for Masonry columnWidth
.masonry-sizer {
  width: 100%; // Base width, overridden by breakpoints
}

// Individual grid items
.masonry-item {
  float: left;
  padding: 8px; // Half of gutter (16px total between items)
  box-sizing: border-box;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  
  // Responsive column widths (Bootstrap 5 breakpoints)
  width: 100%; // xs: 1 column
  
  @include media-breakpoint-up(sm) {
    width: 50%; // sm: 2 columns
  }
  
  @include media-breakpoint-up(md) {
    width: 33.333%; // md: 3 columns
  }
  
  @include media-breakpoint-up(lg) {
    width: 25%; // lg: 4 columns
  }
  
  @include media-breakpoint-up(xl) {
    width: 20%; // xl: 5 columns
  }
  
  // Inner wrapper for consistent styling
  .masonry-item__inner {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    background: $gray-200;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    
    // Aspect ratio container (optional, for consistent sizing)
    &::before {
      content: '';
      display: block;
      padding-top: 100%; // 1:1 aspect ratio, adjust as needed
    }
  }
  
  // Media element
  img,
  video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  
  // Metadata overlay icon
  .metadata-indicator {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 24px;
    height: 24px;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    z-index: 10;
    
    &::before {
      content: '\1F4CD'; // 📍 Pin emoji, or use icon font
    }
  }
  
  // Hover effects
  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 10;
    
    img,
    video {
      transform: scale(1.05);
    }
  }
  
  // Focus state for accessibility
  &:focus-within {
    outline: 2px solid $primary;
    outline-offset: 2px;
  }
}

// Loading state
.masonry-grid.is-loading {
  .masonry-item {
    opacity: 0.5;
    animation: pulse 1.5s infinite;
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 0.5;
  }
  50% {
    opacity: 0.8;
  }
}
```

---

### 4.4 Template Overrides

#### **File**: `templates/views/views-view--archive-by-date.html.twig`

```twig
{#
/**
 * @file
 * Theme override for the Archive by Date view.
 *
 * Available variables:
 * - rows: The view results.
 * - view: The view object.
 * - title: The view title.
 */
#}
{% set grid_classes = [
  'masonry-grid',
  'archive-grid',
] %}

{% set grid_attributes = create_attribute() %}
{% set grid_attributes = grid_attributes
  .setAttribute('data-columns-xs', '1')
  .setAttribute('data-columns-sm', '2')
  .setAttribute('data-columns-md', '3')
  .setAttribute('data-columns-lg', '4')
  .setAttribute('data-columns-xl', '5')
%}

<div{{ grid_attributes.addClass(grid_classes) }}>
  {# Sizing element for Masonry columnWidth calculation #}
  <div class="masonry-sizer"></div>
  
  {# Grid items #}
  {{ rows }}
</div>

{{ attach_library('fridaynightskate/masonry-archive') }}
```

#### **File**: `templates/views/views-view-unformatted--archive-by-date.html.twig`

```twig
{#
/**
 * @file
 * Theme override for unformatted view rows.
 *
 * Available variables:
 * - rows: The view results.
 */
#}
{% for row in rows %}
  {% set row_classes = [
    'masonry-item',
    'archive-item',
  ] %}
  
  <div{{ row.attributes.addClass(row_classes) }}>
    <div class="masonry-item__inner">
      {{ row.content }}
    </div>
  </div>
{% endfor %}
```

#### **File**: `templates/views/views-view-fields--archive-by-date.html.twig`

```twig
{#
/**
 * @file
 * Theme override for view fields.
 *
 * Available variables:
 * - fields: An array of field output.
 * - row: The raw result object.
 */
#}

{# Media field with responsive image #}
{% if fields.field_archive_media %}
  <div class="archive-item__media">
    {{ fields.field_archive_media.content }}
  </div>
{% endif %}

{# Metadata indicator if metadata exists #}
{% if row._entity.field_metadata.value %}
  <div class="metadata-indicator" title="{{ 'Has GPS/EXIF metadata'|t }}">
    <span class="visually-hidden">{{ 'Has metadata'|t }}</span>
  </div>
{% endif %}
```

**Alternative**: If using Content row style, override:  
`templates/node/node--archive-media--thumbnail.html.twig`

---

### 4.5 Library Definition

**File**: `fridaynightskate.libraries.yml` (APPEND)

```yaml
masonry-archive:
  version: 1.0
  css:
    theme:
      build/css/main.style.css: {} # Contains _archive-masonry.scss
  js:
    build/js/archive-masonry.js: {}
  dependencies:
    - core/drupal
    - core/once
    - core/drupalSettings
```

---

### 4.6 Package Dependencies

**File**: `package.json` (UPDATE devDependencies)

```json
{
  "devDependencies": {
    "masonry-layout": "^4.2.2",
    "imagesloaded": "^5.0.0"
  }
}
```

---

### 4.7 Theme Preprocess (Optional)

**File**: `fridaynightskate.theme` (ADD if needed)

```php
<?php

/**
 * Implements hook_preprocess_views_view().
 */
function fridaynightskate_preprocess_views_view(&$variables) {
  $view = $variables['view'];
  
  if ($view->id() === 'archive_by_date') {
    // Add custom classes or variables
    $variables['attributes']['class'][] = 'archive-view-wrapper';
    
    // Pass taxonomy term info to JavaScript if needed
    if (!empty($view->args[0])) {
      $variables['#attached']['drupalSettings']['archiveMasonry'] = [
        'termId' => $view->args[0],
      ];
    }
  }
}

/**
 * Implements hook_preprocess_node().
 */
function fridaynightskate_preprocess_node(&$variables) {
  $node = $variables['node'];
  
  if ($node->bundle() === 'archive_media' && $variables['view_mode'] === 'thumbnail') {
    // Add data attributes for metadata indicator
    if (!$node->get('field_metadata')->isEmpty()) {
      $variables['attributes']['data-has-metadata'] = 'true';
    }
    
    // Add lazy loading to media images
    $variables['content']['field_archive_media']['#attributes']['loading'] = 'lazy';
  }
}
```

---

## 5. Integration Points

### 5.1 Drupal ↔ Masonry.js

**Data Flow**:
1. **Drupal Views** generates HTML with `.masonry-grid` container
2. **Template** adds data attributes for configuration
3. **Drupal.behaviors** attaches Masonry on page load and AJAX events
4. **ImagesLoaded** ensures layout after image loading
5. **IntersectionObserver** triggers re-layout on lazy load

**Key Integration**:
- Use `Drupal.behaviors` pattern for proper AJAX compatibility
- Use `once()` utility to prevent double-initialization
- Listen for `drupalAjaxComplete` event for dynamic content
- Store Masonry instance on DOM element for external access

### 5.2 Bootstrap 5 ↔ Masonry.js

**Breakpoint Alignment**:
- Use Bootstrap's breakpoint variables in SCSS
- Calculate columns in JS based on viewport width
- Match gutter spacing to Bootstrap grid (16px)

**Responsive Image Integration**:
- Use Drupal's responsive_image module (already in use)
- Configure image styles for archive_media thumbnail view mode
- Breakpoints defined in `fridaynightskate.breakpoints.yml`

### 5.3 Laravel Mix Build Pipeline

**Build Process**:
1. **Input**: `src/js/archive-masonry.js` (ES6 with imports)
2. **Webpack**: Bundle Masonry + ImagesLoaded
3. **Output**: `build/js/archive-masonry.js` (browser-compatible)
4. **SCSS**: Compile `_archive-masonry.scss` into `main.style.css`

**Commands**:
```bash
ddev yarn install          # Install Masonry + ImagesLoaded
ddev yarn dev              # Development build
ddev yarn watch            # Watch for changes
ddev yarn production       # Production build (minified)
```

---

## 6. Implementation Plan & Task Breakdown

### 6.1 Phase 1: Foundation (Drupal Developer)

**Agent**: `developer_drupal.md`

**Tasks**:
1. **Create Views configuration**:
   - File: `web/modules/custom/fns_archive/config/install/views.view.archive_by_date.yml`
   - Settings: As specified in Section 4.1
   - Test: Access `/archive/1` (assuming term ID 1 exists)

2. **Update fns_archive README**:
   - Document new view in `web/modules/custom/fns_archive/README.md`
   - Add usage instructions

3. **Export configuration**:
   - Run: `ddev drush cex`
   - Commit: `config/sync/views.view.archive_by_date.yml`

**Success Criteria**:
- View accessible at `/archive/{term_id}`
- Displays Archive Media nodes filtered by skate date
- Shows empty text if no content
- Title overridden with taxonomy term name

**Estimated Time**: 2-3 hours

---

### 6.2 Phase 2: Theme Integration (Themer)

**Agent**: `themer.agent.md`

**Tasks**:

#### 2A: Package Setup
1. Update `package.json` with Masonry + ImagesLoaded
2. Run `ddev yarn install`
3. Verify dependencies in `node_modules/`

#### 2B: JavaScript Development
1. Create `src/js/archive-masonry.js` (as per Section 4.2)
2. Import in `src/js/main.script.js` if needed (or standalone)
3. Test ESLint/Biome compliance: `ddev yarn biome:check src/js/archive-masonry.js`

#### 2C: SCSS Development
1. Create `src/scss/components/_archive-masonry.scss` (as per Section 4.3)
2. Import in main SCSS file: `@import 'components/archive-masonry';`
3. Test Stylelint: `ddev yarn stylint`

#### 2D: Build Assets
1. Run: `ddev yarn dev`
2. Verify output: `build/js/archive-masonry.js` and CSS in `build/css/main.style.css`

#### 2E: Library Definition
1. Add `masonry-archive` library to `fridaynightskate.libraries.yml` (Section 4.5)
2. Clear cache: `ddev drush cr`

#### 2F: Template Overrides
1. Create:
   - `templates/views/views-view--archive-by-date.html.twig`
   - `templates/views/views-view-unformatted--archive-by-date.html.twig`
   - `templates/views/views-view-fields--archive-by-date.html.twig`
2. OR modify existing: `templates/node/node--archive-media--thumbnail.html.twig`
3. Clear cache: `ddev drush cr`

#### 2G: Lazy Loading Implementation
1. Add `loading="lazy"` to images in templates
2. Implement IntersectionObserver in JS (already in Section 4.2)

#### 2H: Metadata Indicator
1. Add conditional rendering in template (Section 4.4)
2. Style `.metadata-indicator` in SCSS

#### 2I: Hover Effects
1. CSS transitions in SCSS (already in Section 4.3)
2. Test across browsers

**Success Criteria**:
- Masonry grid displays with correct column counts
- Responsive: 1 → 2 → 3 → 4 → 5 columns as viewport increases
- Images lazy load properly
- Hover effects smooth and performant
- Metadata icon shows for nodes with GPS/EXIF data
- No console errors
- Passes Stylelint + Biome checks

**Estimated Time**: 6-8 hours

---

### 6.3 Phase 3: Testing & Validation (Tester)

**Agent**: `tester.md`

**Tasks**:

#### 3A: Unit Tests (PHPUnit)
1. Test Views configuration loads correctly
2. Test contextual filter functionality
3. Test node preprocess functions (if added)

#### 3B: Functional Tests (Nightwatch.js)
1. **Test: Grid Rendering**
   - Visit `/archive/{term_id}`
   - Assert `.masonry-grid` exists
   - Assert correct number of `.masonry-item` elements
   - Assert column count at different viewport sizes

2. **Test: Lazy Loading**
   - Scroll down page
   - Assert images load progressively
   - Check `loading="lazy"` attribute

3. **Test: Hover Effects**
   - Hover over grid item
   - Assert transform and box-shadow changes
   - Check image scale

4. **Test: Metadata Indicator**
   - Assert `.metadata-indicator` exists for nodes with metadata
   - Assert absent for nodes without metadata

5. **Test: Responsive Behavior**
   - Resize viewport (xs, sm, md, lg, xl)
   - Assert column count changes correctly
   - Assert no layout breaks

6. **Test: Accessibility**
   - Keyboard navigation
   - Screen reader compatibility
   - Focus states

#### 3C: Browser Testing
- Chrome, Firefox, Safari, Edge
- Mobile devices (iOS Safari, Chrome Mobile)

#### 3D: Performance Testing
- Lighthouse audit (target: 90+ performance score)
- Check for layout shifts (CLS < 0.1)
- Monitor JavaScript execution time

**Success Criteria**:
- All tests pass
- Cross-browser compatibility verified
- Performance metrics acceptable
- Accessibility WCAG 2.1 AA compliant

**Estimated Time**: 4-6 hours

---

### 6.4 Phase 4: Documentation & Handoff

**Agent**: `technical-writer.md`

**Tasks**:
1. Update theme README with Masonry usage
2. Document JavaScript API for extending grid
3. Add inline code comments
4. Create user guide for content editors
5. Update architecture documentation

**Deliverables**:
- `web/themes/custom/fridaynightskate/README.mdx` (updated)
- `ARCHIVE_MASONRY_USAGE.md` (new)
- Inline JSDoc comments

**Estimated Time**: 2-3 hours

---

## 7. Potential Issues & Considerations

### 7.1 Performance Concerns

**Issue**: Large number of images causing slow layout  
**Mitigation**:
- Implement lazy loading with IntersectionObserver
- Use Views paging (50 items per page)
- Optimize image styles (WebP format, responsive images)
- Debounce resize handler

**Issue**: Masonry re-layout thrashing  
**Mitigation**:
- Call `layout()` only when necessary (image load, resize)
- Use `transitionDuration: '0.3s'` for smooth animation
- Batch layout updates with `requestAnimationFrame`

### 7.2 Responsive Design

**Issue**: Column count not matching breakpoints  
**Mitigation**:
- Use Bootstrap's breakpoint variables in SCSS
- Calculate columns dynamically in JS based on `window.innerWidth`
- Test across all breakpoints

**Issue**: Gutter spacing inconsistent  
**Mitigation**:
- Use negative margins on container
- Match gutter to Bootstrap grid (16px)
- Use `box-sizing: border-box` on items

### 7.3 Browser Compatibility

**Issue**: Masonry not supported in older browsers  
**Mitigation**:
- Use Masonry v4 (widely compatible)
- Fallback to CSS Grid with `@supports` detection
- Polyfill for IntersectionObserver if needed

### 7.4 Drupal Integration

**Issue**: Masonry not initializing after AJAX  
**Mitigation**:
- Use `Drupal.behaviors` pattern
- Listen for `drupalAjaxComplete` event
- Re-initialize with `once()` utility

**Issue**: Template suggestions not working  
**Mitigation**:
- Clear Twig cache: `ddev drush cr`
- Check template naming convention
- Use `ddev drush debug:theme` to verify suggestions

### 7.5 Content Authoring

**Issue**: Videos not displaying correctly  
**Mitigation**:
- Use poster image for video thumbnails
- Ensure VideoJS integration for playback
- Test with both image and video media types

**Issue**: Metadata not showing  
**Mitigation**:
- Verify `field_metadata` is populated
- Check conditional logic in template
- Test with GPS-enabled media

### 7.6 Build Pipeline

**Issue**: Webpack errors with Masonry import  
**Mitigation**:
- Use ES6 import: `import Masonry from 'masonry-layout';`
- Check Laravel Mix configuration in `webpack.mix.js`
- Clear build cache: `rm -rf node_modules/.cache`

**Issue**: SCSS not compiling  
**Mitigation**:
- Check import syntax: `@import 'components/archive-masonry';`
- Verify file path in webpack.mix.js
- Run: `ddev yarn dev` with verbose flag

---

## 8. Testing Strategy

### 8.1 Manual Testing Checklist

- [ ] View accessible at `/archive/{term_id}`
- [ ] Grid displays with correct column count at each breakpoint
- [ ] Images lazy load as user scrolls
- [ ] Hover effects smooth (scale + shadow)
- [ ] Metadata indicator shows for nodes with GPS data
- [ ] Clicking item opens node page (or modal, future)
- [ ] No console errors
- [ ] No layout shifts (CLS)
- [ ] Keyboard navigation works
- [ ] Screen reader announces items correctly

### 8.2 Automated Testing

**PHPUnit**:
```bash
ddev phpunit --group fns_archive
```

**Nightwatch**:
```bash
ddev yarn test:nightwatch --test tests/archive-masonry.js
```

**PHPStan**:
```bash
ddev phpstan analyse web/modules/custom/fns_archive
```

**Stylelint**:
```bash
ddev yarn stylint
```

**Biome** (JS linting):
```bash
ddev yarn biome:check src/js/archive-masonry.js
```

### 8.3 Performance Benchmarks

**Target Metrics**:
- **Lighthouse Performance**: 90+
- **CLS (Cumulative Layout Shift)**: < 0.1
- **LCP (Largest Contentful Paint)**: < 2.5s
- **FID (First Input Delay)**: < 100ms
- **Masonry Layout Time**: < 500ms for 50 items

---

## 9. Deployment Checklist

### 9.1 Pre-Deployment

- [ ] All tests passing
- [ ] Configuration exported: `ddev drush cex`
- [ ] Assets built for production: `ddev yarn production`
- [ ] Code review completed
- [ ] Documentation updated

### 9.2 Deployment Steps

1. **Git Operations**:
   ```bash
   git checkout -b feature/archive-masonry-views
   git add .
   git commit -m "feat: Implement Archive Views with Masonry grid"
   git push origin feature/archive-masonry-views
   ```

2. **Production Server** (OpenLiteSpeed):
   ```bash
   # Pull code
   git pull origin main
   
   # Import configuration
   vendor/bin/drush cim -y
   
   # Clear cache
   vendor/bin/drush cr
   
   # Rebuild theme
   cd web/themes/custom/fridaynightskate
   npm ci --production
   npm run production
   ```

3. **Verification**:
   - Test view at `/archive/{term_id}`
   - Check console for errors
   - Verify responsive behavior
   - Run smoke tests

### 9.3 Rollback Plan

If issues arise:
```bash
# Revert to previous commit
git revert HEAD

# Re-export config
vendor/bin/drush cex -y

# Clear cache
vendor/bin/drush cr
```

---

## 10. Future Enhancements

### 10.1 Swiper.js Integration (Phase 2)

**Purpose**: Modal navigation between images  
**Implementation**:
- Click grid item → Open modal with Swiper carousel
- Navigate between all items in current skate date
- Show full metadata in modal
- Agent: `themer.agent.md`

### 10.2 Infinite Scroll

**Purpose**: Load more items without pagination  
**Implementation**:
- Replace pager with IntersectionObserver on last item
- AJAX load next page
- Append to Masonry grid
- Call `masonry.appended()` for new items

### 10.3 Filter/Search UI

**Purpose**: Client-side filtering by media type, date range  
**Implementation**:
- Add filter controls above grid
- Use Isotope.js (Masonry alternative with filtering)
- Hide/show items without page reload

### 10.4 Lightbox Integration

**Purpose**: Quick preview without leaving page  
**Implementation**:
- Use GLightbox or Photoswipe
- Full-screen image/video viewer
- Keyboard navigation

---

## 11. Dependencies Summary

### 11.1 Drupal Modules (Required)
- `views` (core)
- `taxonomy` (core)
- `media` (core)
- `responsive_image` (core)
- `fns_archive` (custom, existing)

### 11.2 JavaScript Libraries
- `masonry-layout` (^4.2.2)
- `imagesloaded` (^5.0.0)

### 11.3 Build Tools (DevDependencies)
- `laravel-mix` (^6.0.18)
- `webpack` (via Laravel Mix)
- `autoprefixer` (^10.4.5)
- `sass` (^1.63.6)

### 11.4 Theme Dependencies
- `bootstrap` (^5.3.3, via Radix 6)
- `@popperjs/core` (^2.11.8)

---

## 12. Code Review Checklist

Before submitting PR:

- [ ] **Drupal Coding Standards**: PHPStan level 6 passes
- [ ] **JavaScript**: Biome check passes
- [ ] **CSS**: Stylelint passes
- [ ] **Tests**: All PHPUnit and Nightwatch tests pass
- [ ] **Configuration**: Exported and committed
- [ ] **Documentation**: README updated
- [ ] **Accessibility**: WCAG 2.1 AA compliant
- [ ] **Performance**: Lighthouse score 90+
- [ ] **Browser Testing**: Chrome, Firefox, Safari, Edge
- [ ] **Mobile Testing**: iOS Safari, Chrome Mobile
- [ ] **Git**: Conventional commit format
- [ ] **Security**: No XSS vulnerabilities (template escaping)

---

## 13. Contact & Support

**Specialized Agents**:
- **Drupal Development**: `developer_drupal.md`
- **Theming**: `themer.agent.md`
- **Testing**: `tester.md`
- **Documentation**: `technical-writer.md`

**Agent Directory**: `.github/AGENT_DIRECTORY.md`

---

## 14. Appendix

### A. Bootstrap 5 Breakpoints Reference

| Breakpoint | Min Width | Max Width | Columns | Grid Item Width |
|------------|-----------|-----------|---------|-----------------|
| XS         | 0         | 575px     | 1       | 100%            |
| SM         | 576px     | 767px     | 2       | 50%             |
| MD         | 768px     | 991px     | 3       | 33.333%         |
| LG         | 992px     | 1199px    | 4       | 25%             |
| XL         | 1200px    | 1399px    | 5       | 20%             |
| XXL        | 1400px    | ∞         | 5       | 20%             |

### B. Masonry.js Options Reference

```javascript
{
  itemSelector: '.masonry-item',      // Selector for grid items
  columnWidth: '.masonry-sizer',      // Sizing element for column width
  percentPosition: true,              // Use % instead of px for positioning
  gutter: 16,                         // Spacing between items (px)
  transitionDuration: '0.3s',        // Animation speed
  initLayout: true,                   // Layout on initialization
  resize: true,                       // Auto-relayout on window resize
  fitWidth: false,                    // Center grid if container wider than grid
  horizontalOrder: false,             // Layout items in horizontal order
}
```

### C. ImagesLoaded API

```javascript
// Wait for all images
imagesLoaded(container, callback);

// Wait for specific images
imagesLoaded('.masonry-item img', callback);

// Progress callback
imagesLoaded(container)
  .on('progress', (instance, image) => {
    // Individual image loaded
  })
  .on('always', (instance) => {
    // All images loaded (success or error)
  });
```

### D. Useful Commands

```bash
# Theme development
ddev yarn install           # Install dependencies
ddev yarn dev               # Build assets (development)
ddev yarn watch             # Watch for changes
ddev yarn production        # Build assets (production, minified)

# Drupal
ddev drush cr               # Clear cache
ddev drush cex              # Export configuration
ddev drush cim              # Import configuration

# Testing
ddev phpunit --group fns_archive
ddev yarn test:nightwatch
ddev phpstan analyse web/modules/custom/fns_archive

# Linting
ddev yarn biome:check src/js/
ddev yarn stylint
```

---

**End of Architecture Document**

**Version**: 1.0  
**Last Updated**: 2025-01-29  
**Status**: Ready for Implementation
