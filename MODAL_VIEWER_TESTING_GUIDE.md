# Modal Viewer - Quick Testing Guide

## Prerequisites

- DDEV installed and running
- Drupal site with archive view configured
- Archive view using Masonry grid layout

## Setup Steps

### 1. Start DDEV (if not running)

```bash
cd /home/runner/work/friday-night-skate/friday-night-skate
ddev start
```

### 2. Clear Drupal Cache

```bash
ddev drush cr
```

### 3. Verify the Library is Attached

Check `/web/themes/custom/fridaynightskate/templates/views/views-view--archive-by-date.html.twig`:

```twig
{{ attach_library('fridaynightskate/modal-viewer') }}
```

Should be on line 19.

### 4. Check Built Assets

Verify these files exist:
- `web/themes/custom/fridaynightskate/build/js/modal-viewer.js` ✓
- `web/themes/custom/fridaynightskate/build/css/main.style.css` ✓

### 5. Access the Archive View

```bash
# Get the site URL
ddev describe

# Navigate to:
https://friday-night-skate.ddev.site/archive
```

## Testing Checklist

### Basic Functionality

1. **Modal Opens**
   - Click any image/video in the archive grid
   - Modal should appear with the clicked item

2. **Navigation**
   - **Desktop**: Click left/right arrows
   - **Keyboard**: Press arrow keys
   - **Mobile**: Swipe left/right
   - Should navigate between items

3. **Close Modal**
   - Click X button (top-right)
   - Press ESC key
   - Click outside modal
   - Should close and return focus

4. **Metadata Panel**
   - Click info button (bottom-right)
   - Panel should slide up with metadata
   - Click again to hide

### Video Testing

If archive has videos:
1. Navigate to a video slide
2. Video should load with VideoJS player
3. Click play
4. Navigate to next slide
5. Previous video should stop/dispose

### Accessibility Testing

1. **Keyboard Navigation**
   - Tab through modal elements
   - Should trap focus inside modal
   - ESC should close

2. **Screen Reader**
   - Use NVDA/JAWS/VoiceOver
   - Should announce slide positions
   - Should read metadata

### Mobile Testing

Test on real devices or browser DevTools:
- iPhone/iPad (iOS Safari)
- Android (Chrome)

Check:
- Touch targets easy to tap (≥44px)
- Swipe gestures smooth
- No horizontal scroll
- Modal fullscreen on mobile

## Expected Behavior

### Desktop (≥768px)
- Modal centered, not fullscreen
- Navigation arrows visible
- Arrow keys work
- Metadata panel bottom-right

### Mobile (<768px)
- Modal fullscreen
- No navigation arrows (swipe only)
- Touch targets larger
- Metadata panel bottom-right (smaller)

## Troubleshooting

### Modal doesn't open

**Check browser console:**
```javascript
// Expected: No errors
// If errors, check:
```

1. Library attached? View page source, search for "modal-viewer.js"
2. JavaScript errors? Check console
3. Bootstrap loaded? Check for bootstrap.Modal

**Fix:**
```bash
ddev drush cr
# Force rebuild browser cache (Ctrl+Shift+R)
```

### Swiper not working

**Symptoms:**
- Can't navigate between slides
- Arrows don't respond
- Swipe doesn't work

**Fix:**
1. Check Swiper CSS loaded
2. Verify data attributes on items
3. Check console for Swiper errors

**Debug:**
```javascript
// In browser console:
console.log(Drupal.behaviors.modalViewer);
// Should show object with attach function
```

### Videos not playing

**Check:**
1. VideoJS loaded? Look for `video-js` class
2. Video URLs accessible?
3. Video format supported? (MP4 best)

**Test video URL:**
```bash
# In browser console:
const video = document.querySelector('video');
console.log(video.src);
// Try opening URL directly
```

### Data attributes missing

**Check preprocess hook:**
```bash
# View the compiled template
ddev drush twig:debug

# Check view.theme is loaded
ddev drush php-eval "include_once('web/themes/custom/fridaynightskate/includes/view.theme');"
```

**Verify output HTML:**
```html
<!-- Right-click masonry item → Inspect -->
<!-- Should see: -->
<div class="masonry-item" 
     data-media-type="image"
     data-fullsize="..."
     data-date="..."
     ...>
```

## Performance Checks

### Page Load
- Modal assets: ~350KB (JS + CSS)
- Should load in <2s on 3G

### Memory Usage
1. Open DevTools → Performance
2. Open modal
3. Navigate 10 slides
4. Close modal
5. Check memory profile - should release memory

### Swiper Performance
- Touch response: <100ms
- Animation: 60fps
- No jank or stutter

## Browser Compatibility

Test in:
- ✓ Chrome 90+ (Desktop & Android)
- ✓ Firefox 88+
- ✓ Safari 14+ (Desktop & iOS)
- ✓ Edge 90+

## Reporting Issues

If you find bugs, note:
1. Browser & version
2. Device (if mobile)
3. Steps to reproduce
4. Console errors
5. Expected vs actual behavior

## Success Criteria

✅ Modal opens on click
✅ Navigation works (arrows/keyboard/swipe)
✅ Modal closes properly
✅ Metadata panel toggles
✅ Videos play and dispose correctly
✅ Focus trap works
✅ Touch targets easy to use on mobile
✅ No console errors
✅ No memory leaks after multiple opens
✅ Accessible with keyboard and screen reader

## Next Steps

After testing:
1. Document any issues found
2. Fix issues as needed
3. Add Nightwatch tests
4. Export configuration: `ddev drush cex`
5. Deploy to staging

---

**Need Help?**

Check:
- `MODAL_VIEWER_DOCUMENTATION.md` - Full technical documentation
- Browser DevTools console
- Swiper docs: https://swiperjs.com/
- Bootstrap modal docs: https://getbootstrap.com/docs/5.3/components/modal/
