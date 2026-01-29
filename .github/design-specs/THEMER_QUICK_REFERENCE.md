# Themer Quick Reference: Modal Viewer Implementation

**Full Design Specs:** `modal-viewer-swiper-ux-ui.md`

---

## 🎯 Quick Implementation Checklist

### Phase 1: Setup (15 min)
- [ ] Create 4 new SCSS files in `src/scss/components/`:
  - `_modal-viewer.scss`
  - `_swiper-custom.scss`
  - `_metadata-panel.scss`
  - `_videojs-theme.scss`
- [ ] Add imports to `main.style.scss`
- [ ] Add variables to `base/_variables.scss`

### Phase 2: HTML Structure (30 min)
- [ ] Create Twig template: `templates/modal/fns-media-modal.html.twig`
- [ ] Add modal container with Bootstrap 5 classes
- [ ] Add Swiper wrapper and slides
- [ ] Add navigation arrows
- [ ] Add pagination dots
- [ ] Add metadata panel

### Phase 3: Styling (3-4 hours)
- [ ] Style modal container (fullscreen on mobile)
- [ ] Style close button (48px circle, rotation on hover)
- [ ] Style Swiper navigation arrows (56px circles, teal glow)
- [ ] Style pagination dots (active dot elongates)
- [ ] Style metadata panel (slide up from bottom)
- [ ] Style VideoJS player (hot pink play button)

### Phase 4: JavaScript (2-3 hours)
- [ ] Create `src/js/modal-viewer.js`
- [ ] Initialize Swiper on modal open
- [ ] Destroy Swiper on modal close
- [ ] Update metadata on slide change
- [ ] Add screen reader announcements
- [ ] Add focus trap

### Phase 5: Library Definition (15 min)
- [ ] Add `modal-viewer` library to `fridaynightskate.libraries.yml`
- [ ] Add `swiper` library (CDN)
- [ ] Add dependencies

### Phase 6: Integration (1 hour)
- [ ] Attach library in preprocess function
- [ ] Test modal trigger from Masonry grid
- [ ] Verify data attributes for metadata

### Phase 7: Testing (2 hours)
- [ ] Test on mobile (iPhone SE, 375px)
- [ ] Test on tablet (iPad, 768px)
- [ ] Test on desktop (1920x1080)
- [ ] Run accessibility audit (axe DevTools)
- [ ] Test keyboard navigation
- [ ] Test screen reader (NVDA/JAWS)

### Phase 8: Polish (1 hour)
- [ ] Refine animation timings
- [ ] Test swipe feel (momentum, resistance)
- [ ] Verify touch targets ≥ 44px
- [ ] Check color contrast
- [ ] Test VideoJS integration

---

## 🎨 Design Tokens (Copy-Paste Ready)

```scss
// Colors
--modal-overlay-bg: rgba(10, 10, 15, 0.95);
--modal-content-bg: #0a0a0f;
--modal-accent-primary: #00ff9f;        // Electric teal
--modal-accent-secondary: #ff006e;      // Hot pink
--modal-accent-tertiary: #ffd60a;       // Street light yellow
--modal-text-primary: #f8f9fa;

// Typography
--modal-font-display: 'Staatliches', Impact, sans-serif;
--modal-font-body: 'Archivo', system-ui, sans-serif;

// Spacing
--modal-space-md: 1rem;
--modal-space-lg: 1.5rem;

// Animation
--modal-transition-normal: 0.3s ease;
--modal-transition-slow: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
```

---

## 📏 Key Measurements

| Element | Mobile | Desktop |
|---------|--------|---------|
| Close button | 48px × 48px | 48px × 48px |
| Nav arrows | Hidden | 56px × 56px |
| Touch targets | 44px min | 44px min |
| Modal padding | 0 | 24-32px |
| Metadata panel max-height | 280px | 320px |

---

## 🎬 Animation Timings

| Action | Duration | Easing |
|--------|----------|--------|
| Modal open | 250ms | ease-out |
| Modal close | 200ms | ease-in |
| Swipe transition | 400ms | cubic-bezier(0.25, 0.1, 0.25, 1) |
| Metadata panel | 400ms | cubic-bezier(0.4, 0, 0.2, 1) |
| Close button rotation | 300ms | cubic-bezier(0.68, -0.55, 0.265, 1.55) |

---

## 🔑 Critical Implementation Notes

### 1. Mobile-First
- Fullscreen on XS/SM (< 768px)
- No navigation arrows on mobile (swipe only)
- Larger touch targets (48px vs 44px)

### 2. Swiper Configuration
```javascript
{
  speed: 400,
  threshold: 10,              // 10px before swipe triggers
  resistanceRatio: 0.85,      // Elastic resistance at edges
  touchAngle: 45,             // 45° tolerance for horizontal swipe
}
```

### 3. Accessibility Must-Haves
- Focus trap (modal only)
- ESC closes modal
- Arrow keys navigate slides
- ARIA live region for slide announcements
- Focus on close button when modal opens

### 4. VideoJS Integration
- Hot pink circular play button (80px × 80px)
- Custom control bar (dark with teal accents)
- Auto-pause when switching slides

---

## 📞 Coordination Required

| Task | Coordinate With | Details |
|------|----------------|---------|
| VideoJS player customization | @media-dev | Hot pink theme, control bar styling |
| Modal trigger from grid | @drupal-developer | Click handler on Masonry items |
| Metadata data attributes | @drupal-developer | GPS, date, uploader data on slides |
| Performance optimization | @performance-engineer | Image lazy loading, preload strategy |

---

## 🐛 Common Issues & Solutions

### Issue: Swiper not initializing
**Solution:** Ensure modal is fully rendered before initializing Swiper. Use `shown.bs.modal` event.

### Issue: Images not loading
**Solution:** Check lazy loading configuration. Ensure `preloadImages: false` and `lazy.loadPrevNext: true`.

### Issue: Focus trap not working
**Solution:** Verify all focusable elements are captured. Check for hidden elements with `tabindex="-1"`.

### Issue: Metadata not updating
**Solution:** Ensure data attributes are on `.swiper-slide` elements. Check `updateMetadata()` function.

### Issue: Backdrop click not closing modal
**Solution:** Verify Bootstrap 5 modal configuration. Check `data-bs-dismiss="modal"` on backdrop.

---

## 🎯 Success Criteria

- [ ] Modal opens fullscreen on mobile
- [ ] Swipe feels smooth and natural
- [ ] Navigation arrows work on desktop
- [ ] Keyboard navigation works (←, →, ESC)
- [ ] Metadata panel toggles smoothly
- [ ] VideoJS player has hot pink theme
- [ ] Focus stays within modal
- [ ] Screen reader announces slide changes
- [ ] Touch targets are ≥ 44px
- [ ] Color contrast passes WCAG AAA (7:1)

---

## 📚 Reference Files

1. **Full Design Specs:** `.github/design-specs/modal-viewer-swiper-ux-ui.md`
2. **Existing Masonry Grid:** `src/scss/components/_archive-masonry.scss`
3. **Bootstrap 5 Modal Docs:** https://getbootstrap.com/docs/5.3/components/modal/
4. **Swiper API Docs:** https://swiperjs.com/swiper-api

---

**Estimated Total Time:** 8-12 hours  
**Priority:** High  
**Complexity:** Medium-High  

**Questions?** Review the full design spec document or coordinate with:
- @media-dev (VideoJS)
- @drupal-developer (Drupal integration)
- @ux-ui-designer (design clarifications)
