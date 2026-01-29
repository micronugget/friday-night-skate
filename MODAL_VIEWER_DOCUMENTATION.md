# Modal Viewer with Swiper.js Navigation

## Overview

A Bootstrap 5 modal viewer with Swiper.js integration for viewing archive media (images and videos) with touch-friendly navigation. Supports keyboard controls, touch gestures, and VideoJS video playback.

## Features

### Core Functionality
- **Bootstrap 5 Modal**: Fullscreen on mobile, centered on desktop
- **Swiper.js Navigation**: Touch swipes, keyboard arrows, mouse buttons
- **Media Support**: Images (full resolution) and videos (VideoJS)
- **Metadata Panel**: Collapsible panel showing GPS, date, location, uploader
- **Loop Navigation**: Seamlessly wraps from last to first item
- **Keyboard Controls**: Arrow keys for navigation, ESC to close

### Accessibility
- **ARIA Labels**: All interactive elements properly labeled
- **Focus Trap**: Focus contained within modal when open
- **Keyboard Navigation**: Full keyboard support
- **Screen Reader Support**: Semantic HTML and ARIA attributes
- **Focus Restoration**: Returns focus to trigger element on close

### Mobile Optimization
- **Touch Gestures**: Smooth swipe left/right navigation
- **Touch Targets**: Minimum 44×44px for all interactive elements
- **Responsive Design**: Adapts to all screen sizes (xs to xxl)
- **Video Controls**: Optimized for mobile playback

### Performance
- **Lazy Loading**: Images loaded on demand
- **VideoJS Disposal**: Proper cleanup prevents memory leaks
- **Optimized Transitions**: 400ms smooth animations
- **Reduced Motion**: Respects user preferences

## Installation

### 1. Dependencies

Already installed via npm:
```json
{
  "swiper": "^12.1.0"
}
```

### 2. Files Structure

```
web/themes/custom/fridaynightskate/
├── src/
│   ├── js/
│   │   └── modal-viewer.js          # Main JavaScript
│   └── scss/
│       └── components/
│           └── _modal-viewer.scss   # Styles
├── build/
│   ├── js/
│   │   └── modal-viewer.js          # Compiled JS
│   └── css/
│       └── main.style.css           # Includes modal styles
├── includes/
│   └── view.theme                   # Preprocess hook
└── templates/
    └── views/
        └── views-view--archive-by-date.html.twig
```

### 3. Library Definition

In `fridaynightskate.libraries.yml`:
```yaml
modal-viewer:
  css:
    theme:
      build/css/main.style.css: {}
  js:
    build/js/modal-viewer.js: {}
  dependencies:
    - core/drupal
    - core/once
    - core/drupalSettings
```

## Usage

### Attaching to Views

In your view template (e.g., `views-view--archive-by-date.html.twig`):

```twig
{{ attach_library('fridaynightskate/modal-viewer') }}
```

### Data Attributes Required

The modal viewer expects masonry items to have these data attributes:

```html
<div class="masonry-item" 
     data-media-type="image|video"
     data-fullsize="/path/to/fullsize/image.jpg"
     data-date="January 15, 2026"
     data-location="Downtown"
     data-gps="45.5231,-122.6765"
     data-uploader="John Doe"
     data-video-url="/path/to/video.mp4"  <!-- if video -->
     data-video-id="video-123">           <!-- if video -->
  <img src="/thumbnail.jpg" alt="Media title">
</div>
```

### Preprocess Hook

The theme automatically adds these attributes via `fridaynightskate_preprocess_views_view_unformatted()` in `includes/view.theme`.

## User Interaction

### Opening the Modal
1. **Click** any masonry grid item
2. **Keyboard**: Focus item and press Enter or Space

### Navigation
- **Touch**: Swipe left/right
- **Keyboard**: Arrow keys (left/right)
- **Mouse**: Click arrow buttons
- **Loop**: Automatically wraps from last to first

### Closing the Modal
- **Button**: Click X button (top-right)
- **Keyboard**: Press ESC
- **Backdrop**: Click outside modal

### Metadata Panel
1. Click the info button (bottom-right)
2. Panel slides up with metadata
3. Click again to hide

## Technical Details

### Swiper Configuration

```javascript
new Swiper('.modal-swiper', {
  modules: [Navigation, Keyboard, A11y],
  navigation: true,
  keyboard: { enabled: true },
  a11y: { enabled: true },
  loop: true,
  speed: 400,
  preloadImages: false,
  lazy: true
});
```

### VideoJS Integration

Videos are initialized when their slide becomes active:

```javascript
swiper.on('slideChange', function () {
  // Dispose previous player
  if (currentVideoPlayer) {
    currentVideoPlayer.dispose();
  }
  
  // Initialize new player
  const video = activeSlide.querySelector('video');
  currentVideoPlayer = videojs(video.id);
});
```

### Focus Trap

Focus is trapped within the modal for accessibility:

```javascript
modal.addEventListener('keydown', (e) => {
  if (e.key === 'Tab') {
    // Cycle through focusable elements
  }
});
```

## Styling

### CSS Variables

Key colors used (from design specs):
- Electric Teal: `#00ff9f` (accents, hover states)
- Hot Pink: `#ff006e` (active metadata toggle)
- Near Black: `rgba(10, 10, 15, 0.98)` (modal background)

### Touch Targets

All interactive elements meet WCAG minimum sizes:
- Close button: 48×48px
- Metadata toggle: 48×48px
- Navigation arrows: 56×56px (desktop only)

### Responsive Breakpoints

```scss
// Mobile first
@media (max-width: 575.98px) {
  // XS: Full screen, no arrows
}

@media (max-width: 767.98px) {
  // SM: Hide nav arrows, touch only
}

// Desktop (768px+)
// MD, LG, XL: Show nav arrows, centered modal
```

## Browser Support

- **Modern Browsers**: Chrome, Firefox, Safari, Edge (latest 2 versions)
- **Mobile**: iOS Safari 12+, Chrome Android 90+
- **Swiper**: Uses modern ES6+ features, requires transpilation for IE11

## Testing

### Manual Testing Checklist

**Desktop:**
- [ ] Modal opens on click
- [ ] Arrow keys navigate
- [ ] ESC closes modal
- [ ] Mouse navigation arrows work
- [ ] Metadata panel toggles
- [ ] Focus trap works
- [ ] Videos play in modal

**Mobile:**
- [ ] Modal fullscreen on small screens
- [ ] Swipe gestures work smoothly
- [ ] Touch targets easy to tap
- [ ] Metadata panel accessible
- [ ] Videos play correctly
- [ ] No horizontal scrolling

**Accessibility:**
- [ ] Screen reader announces slides
- [ ] Keyboard-only navigation works
- [ ] Focus restoration after close
- [ ] ARIA labels present
- [ ] High contrast mode supported

### Automated Testing

Add Nightwatch tests:

```javascript
module.exports = {
  'Modal viewer opens on click': function (browser) {
    browser
      .url('http://localhost/archive')
      .waitForElementVisible('.masonry-item', 5000)
      .click('.masonry-item:first-child')
      .waitForElementVisible('#mediaModal', 2000)
      .assert.visible('.modal-swiper')
      .end();
  }
};
```

## Troubleshooting

### Modal doesn't open
- Check that `modal-viewer` library is attached
- Verify masonry items have class `masonry-item`
- Check browser console for JavaScript errors

### Swiper not working
- Ensure Swiper CSS is loaded
- Check that Bootstrap JavaScript is loaded first
- Verify data attributes on masonry items

### Videos not playing
- Confirm VideoJS is loaded
- Check video URLs are accessible
- Verify video format is supported (MP4 recommended)

### Memory leaks
- Ensure VideoJS `.dispose()` is called on slide change
- Check that Swiper is destroyed on modal close
- Monitor DevTools memory profiler

## Performance Optimization

### Image Lazy Loading
Images are loaded with `loading="lazy"` attribute.

### Video Preloading
Videos use `preload="none"` to avoid unnecessary downloads.

### Swiper Lazy Loading
Swiper preloads next/previous slides only.

### CSS Optimization
Modal styles are compiled into main.style.css (~237KB minified).

### JavaScript Bundle Size
- modal-viewer.js: 112KB (includes Swiper)
- Minified in production build
- Swiper modules: Only Navigation, Keyboard, A11y included

## Maintenance

### Updating Swiper
```bash
cd web/themes/custom/fridaynightskate
npm update swiper
npm run production
```

### Adding New Features
1. Modify `src/js/modal-viewer.js`
2. Update `src/scss/components/_modal-viewer.scss`
3. Run `npm run production`
4. Test changes
5. Update this documentation

### Debugging
Enable Swiper debug mode:
```javascript
new Swiper('.modal-swiper', {
  // ... config
  debugger: true
});
```

## Future Enhancements

Potential improvements:
- [ ] Thumbnail navigation bar
- [ ] Zoom functionality for images
- [ ] Social sharing buttons
- [ ] Download button for media
- [ ] Fullscreen API support
- [ ] Slideshow/autoplay mode
- [ ] Comments/reactions in metadata panel

## Credits

- **Swiper**: [swiperjs.com](https://swiperjs.com/)
- **Bootstrap 5**: Modal component
- **VideoJS**: Video playback
- **Radix 6**: Base theme

## License

Part of Friday Night Skate project - same license as parent project.
