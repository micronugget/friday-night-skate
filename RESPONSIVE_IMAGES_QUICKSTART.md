# Responsive Images - Quick Start Guide

## 🚀 Installation (5 minutes)

### 1. Enable the Module
```bash
# If starting fresh or after module update
ddev drush pm:uninstall fns_archive -y
ddev drush en fns_archive -y
ddev drush cr

# Or via config import
ddev drush config-import -y
ddev drush cr
```

### 2. Verify Installation
```bash
# Check image styles exist
ddev drush config:get image.style.archive_thumbnail

# Check responsive image style
ddev drush config:get responsive_image.styles.archive_responsive
```

**Expected:** Both commands should display YAML configuration.

### 3. Quick UI Check
Navigate to: `/admin/config/media/image-styles`

**Expected to see:**
- ✅ Archive Thumbnail (400×400)
- ✅ Archive Medium (800×600)
- ✅ Archive Large (1200×900)
- ✅ Archive Full (1920×1440)

## 📸 Testing (5 minutes)

### 1. Create Test Content
1. Navigate to: `/node/add/archive_media`
2. Upload a test image (at least 1920x1440 for best results)
3. Fill in required fields
4. Save

### 2. View the Content
1. View the created node
2. Right-click the image → "Inspect Element"
3. Look for:
   ```html
   <img src="...archive_thumbnail.jpg.webp" loading="lazy">
   ```

### 3. Test Responsive Behavior
1. Open browser DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Switch between:
   - Mobile (375px) → Should load smaller images
   - Tablet (768px) → Should load medium images
   - Desktop (1920px) → Should load larger images

## ✅ Success Checklist

Quick validation that everything works:

- [ ] Image styles appear in admin UI
- [ ] Images convert to WebP format (check URL ends with `.webp`)
- [ ] Images have `loading="lazy"` attribute
- [ ] Different image sizes load at different breakpoints
- [ ] Modal view uses responsive images
- [ ] Archive grid view uses thumbnails
- [ ] Existing content automatically benefits

## 🎯 Quick Performance Test

```bash
# Install lighthouse CLI
npm install -g lighthouse

# Run quick audit
lighthouse https://friday-night-skate.ddev.site/node/[ID] \
  --only-categories=performance \
  --view
```

**Target:** Performance score >90

## 📖 Full Documentation

- **Implementation Details:** `web/modules/custom/fns_archive/RESPONSIVE_IMAGES.md`
- **Complete Testing Guide:** `web/modules/custom/fns_archive/TESTING_RESPONSIVE_IMAGES.md`
- **Full Summary:** `IMPLEMENTATION_SUMMARY_RESPONSIVE_IMAGES.md`

## 🐛 Troubleshooting

### WebP Not Working
```bash
# Check WebP support
ddev php -r "var_dump(function_exists('imagewebp'));"
# Should output: bool(true)
```

### Images Not Loading
```bash
# Clear caches
ddev drush cr

# Flush image cache
ddev drush image-flush --all
```

### Configuration Issues
```bash
# Reinstall module
ddev drush pm:uninstall fns_archive -y
ddev drush en fns_archive -y
ddev drush cr
```

## 🎉 What You Get

- ✅ **4 Image Styles** optimized for different screen sizes
- ✅ **WebP Format** for 30-50% file size reduction
- ✅ **Lazy Loading** for faster initial page load
- ✅ **Responsive Delivery** via Bootstrap 5 breakpoints
- ✅ **Automatic Fallback** to JPEG for older browsers
- ✅ **Zero Code Changes** - pure configuration

## 🔄 Updates

When updating the module:
```bash
git pull
ddev drush config-import -y
ddev drush cr
ddev drush image-flush --all
```

## 💡 Next Steps

1. ✅ Complete this quick start
2. Run full testing from `TESTING_RESPONSIVE_IMAGES.md`
3. Run Lighthouse performance audit
4. Deploy to production
5. Monitor performance metrics

---

**Total Time:** ~10 minutes
**Difficulty:** Easy
**Requirements:** DDEV environment, fns_archive module
