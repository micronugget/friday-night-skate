# 📑 Design Specs Index

**Quick navigation for all design specification files**

---

## 🚀 Start Here

**New to this project?** Read in this order:

1. **`HANDOFF_SUMMARY.md`** ⭐ START HERE
   - Overview of what you're building
   - Quick start guide (3 steps)
   - Implementation checklist
   - **Time to read:** 5 minutes

2. **`README.md`**
   - Directory structure
   - File descriptions
   - Workflow overview
   - **Time to read:** 5 minutes

3. **`THEMER_QUICK_REFERENCE.md`**
   - Implementation checklist (8 phases)
   - Design tokens (copy-paste)
   - Common issues & solutions
   - **Time to read:** 3 minutes
   - **Keep open during implementation**

---

## 📐 Design Documentation

### Complete Specification
**`modal-viewer-swiper-ux-ui.md`** (41KB)
- Complete design specification (~40,000 words)
- All component details
- Code examples (SCSS, JavaScript, Twig)
- Accessibility guidelines
- **Use as:** Reference during coding

### Visual Reference
**`WIREFRAMES.md`** (18KB)
- ASCII wireframes for all breakpoints
- Component detail views
- State diagrams
- Animation timelines
- **Use as:** Visual layout reference

### Design Tokens
**`design-tokens.scss`** (13KB)
- All SCSS variables
- Copy-paste ready
- CSS custom properties
- Usage examples
- **Use as:** Copy into `_variables.scss`

---

## 📂 File Structure

```
.github/design-specs/
├── INDEX.md                          ← You are here
├── HANDOFF_SUMMARY.md                ← Start here (overview)
├── README.md                         ← Directory guide
├── THEMER_QUICK_REFERENCE.md         ← Daily implementation guide
├── WIREFRAMES.md                     ← Visual layouts
├── design-tokens.scss                ← SCSS variables
└── modal-viewer-swiper-ux-ui.md      ← Complete specs
```

---

## 🎯 Quick Lookup Table

| I Need To... | Go To |
|--------------|-------|
| Understand what I'm building | `HANDOFF_SUMMARY.md` |
| Get started quickly | `THEMER_QUICK_REFERENCE.md` |
| See a visual layout | `WIREFRAMES.md` |
| Copy SCSS variables | `design-tokens.scss` |
| Find code examples | `modal-viewer-swiper-ux-ui.md` |
| Check color values | `design-tokens.scss` or `THEMER_QUICK_REFERENCE.md` |
| Find animation timings | `THEMER_QUICK_REFERENCE.md` → Animation Timings |
| Understand responsive breakpoints | `WIREFRAMES.md` → Responsive Changes |
| Check accessibility requirements | `modal-viewer-swiper-ux-ui.md` → Accessibility |
| Troubleshoot an issue | `HANDOFF_SUMMARY.md` → Common Issues |
| See component states | `WIREFRAMES.md` → State Diagrams |
| Find JavaScript examples | `modal-viewer-swiper-ux-ui.md` → Step 3 |
| Find Twig template | `modal-viewer-swiper-ux-ui.md` → Step 2 |

---

## 🎨 Design Tokens Quick Reference

```scss
// Colors
--modal-accent-primary: #00ff9f;      // Electric teal
--modal-accent-secondary: #ff006e;    // Hot pink
--modal-accent-tertiary: #ffd60a;     // Street yellow
--modal-content-bg: #0a0a0f;          // Near-black

// Typography
--modal-font-display: 'Staatliches', Impact, sans-serif;
--modal-font-body: 'Archivo', system-ui, sans-serif;

// Sizing
Close button: 48px × 48px
Nav arrows: 56px × 56px (desktop only)
Touch targets: ≥ 44px minimum

// Animation
Modal open: 250ms ease-out
Swipe: 400ms smooth
Metadata panel: 400ms cubic-bezier
```

**Full tokens:** `design-tokens.scss`

---

## 📋 Implementation Phases

| Phase | Time | File Reference |
|-------|------|----------------|
| 1. Setup | 15 min | `HANDOFF_SUMMARY.md` → Step 2 |
| 2. HTML | 30 min | `modal-viewer-swiper-ux-ui.md` → Step 2 |
| 3. Styling | 3-4 hrs | `modal-viewer-swiper-ux-ui.md` → Components |
| 4. JavaScript | 2-3 hrs | `modal-viewer-swiper-ux-ui.md` → Step 3 |
| 5. Library | 15 min | `modal-viewer-swiper-ux-ui.md` → Step 4 |
| 6. Integration | 1 hr | `modal-viewer-swiper-ux-ui.md` → Step 5 |
| 7. Testing | 2 hrs | `HANDOFF_SUMMARY.md` → Success Criteria |
| 8. Polish | 1 hr | `WIREFRAMES.md` → Animation Timelines |

**Total:** 8-12 hours

**Track progress:** `THEMER_QUICK_REFERENCE.md` → Checklist

---

## 🧩 Component Lookup

| Component | Find Details In | Visual In |
|-----------|----------------|-----------|
| Modal Container | `modal-viewer-swiper-ux-ui.md` → Section 1 | `WIREFRAMES.md` → Modal Layout |
| Close Button | `modal-viewer-swiper-ux-ui.md` → Section 2 | `WIREFRAMES.md` → Component Details |
| Swiper Container | `modal-viewer-swiper-ux-ui.md` → Section 3 | `WIREFRAMES.md` → Layout |
| Navigation Arrows | `modal-viewer-swiper-ux-ui.md` → Section 4 | `WIREFRAMES.md` → Desktop Layout |
| Pagination Dots | `modal-viewer-swiper-ux-ui.md` → Section 5 | `WIREFRAMES.md` → Component Details |
| Image Display | `modal-viewer-swiper-ux-ui.md` → Section 6a | `WIREFRAMES.md` → Modal Open |
| Video Display | `modal-viewer-swiper-ux-ui.md` → Section 6b | `WIREFRAMES.md` → Video View |
| Metadata Panel | `modal-viewer-swiper-ux-ui.md` → Section 7 | `WIREFRAMES.md` → Metadata Expanded |

---

## 🔧 Troubleshooting Guide

| Problem | Solution Location |
|---------|------------------|
| Swiper not initializing | `HANDOFF_SUMMARY.md` → Common Issues |
| Images not loading | `HANDOFF_SUMMARY.md` → Common Issues |
| Focus trap not working | `modal-viewer-swiper-ux-ui.md` → Focus Management |
| Metadata not updating | `HANDOFF_SUMMARY.md` → Common Issues |
| Styles not applying | `HANDOFF_SUMMARY.md` → Common Issues |
| Animation feels wrong | `WIREFRAMES.md` → Animation Timeline |
| Colors not matching | `design-tokens.scss` → Color Tokens |
| Touch targets too small | `THEMER_QUICK_REFERENCE.md` → Key Measurements |

---

## 📱 Responsive Breakpoints

| Breakpoint | Width | File With Details |
|------------|-------|-------------------|
| XS (Mobile) | < 576px | `WIREFRAMES.md` → Mobile Layout |
| SM (Large Mobile) | 576-767px | `WIREFRAMES.md` → Mobile Layout |
| MD (Tablet) | 768-991px | `WIREFRAMES.md` → Tablet Layout |
| LG (Desktop) | 992-1199px | `WIREFRAMES.md` → Desktop Layout |
| XL (Large Desktop) | ≥ 1200px | `WIREFRAMES.md` → Desktop Layout |

**Key Changes:**
- XS/SM: Fullscreen, no arrows, swipe only
- MD: Centered 90%, no arrows, swipe + keyboard
- LG+: Centered 80%, arrows visible, all navigation

**Full responsive specs:** `modal-viewer-swiper-ux-ui.md` → Specifications Table

---

## ✅ Success Criteria Checklist

Quick reference for testing:

### Functionality
- [ ] Modal opens fullscreen on mobile
- [ ] Swipe gestures work smoothly
- [ ] Navigation arrows work on desktop
- [ ] Keyboard navigation (←, →, ESC)
- [ ] Metadata panel toggles
- [ ] VideoJS plays videos

### Accessibility
- [ ] Focus trap works
- [ ] Screen reader announces changes
- [ ] Touch targets ≥ 44px
- [ ] Color contrast ≥ 7:1
- [ ] Keyboard navigation complete

### Cross-Browser
- [ ] iOS Safari
- [ ] Chrome
- [ ] Firefox
- [ ] Edge (optional)

### Build & Deploy
- [ ] `ddev npm run build` succeeds
- [ ] `ddev drush cr` clears cache
- [ ] No console errors

**Full criteria:** `HANDOFF_SUMMARY.md` → Success Criteria

---

## 🤝 Coordination Matrix

| Task | Coordinate With | File Reference |
|------|----------------|----------------|
| VideoJS customization | @media-dev | `modal-viewer-swiper-ux-ui.md` → Section 6b |
| Modal trigger | @drupal-developer | `modal-viewer-swiper-ux-ui.md` → Step 5 |
| Metadata attributes | @drupal-developer | `modal-viewer-swiper-ux-ui.md` → Section 7 |
| Image lazy loading | @performance-engineer | `modal-viewer-swiper-ux-ui.md` → Swiper Config |
| Accessibility testing | @tester | `modal-viewer-swiper-ux-ui.md` → Accessibility |

---

## 📊 File Statistics

| File | Size | Words | Purpose |
|------|------|-------|---------|
| `HANDOFF_SUMMARY.md` | 11KB | ~2,500 | Quick start & overview |
| `README.md` | 11KB | ~2,200 | Directory guide |
| `THEMER_QUICK_REFERENCE.md` | 6KB | ~1,300 | Daily implementation |
| `WIREFRAMES.md` | 18KB | ~3,000 | Visual reference |
| `design-tokens.scss` | 13KB | ~2,000 | SCSS variables |
| `modal-viewer-swiper-ux-ui.md` | 41KB | ~9,500 | Complete specification |
| **TOTAL** | **~100KB** | **~20,500 words** | Full design package |

---

## 🎓 Learning Resources

### External Documentation
- **Bootstrap 5 Modal:** https://getbootstrap.com/docs/5.3/components/modal/
- **Swiper.js:** https://swiperjs.com/swiper-api
- **VideoJS:** https://videojs.com/guides/
- **WCAG:** https://www.w3.org/WAI/WCAG22/quickref/

### Internal Documentation
- **Existing Masonry Grid:** `web/themes/custom/fridaynightskate/src/scss/components/_archive-masonry.scss`
- **Theme Variables:** `web/themes/custom/fridaynightskate/src/scss/base/_variables.scss`
- **Breakpoints:** `web/themes/custom/fridaynightskate/fridaynightskate.breakpoints.yml`

---

## 🎯 Daily Workflow

### Day 1 Morning (2 hours)
1. Read `HANDOFF_SUMMARY.md` (15 min)
2. Setup files (15 min)
3. Copy design tokens (30 min)
4. Create HTML structure (60 min)

### Day 1 Afternoon (2 hours)
5. Style modal container (60 min)
6. Style close button (30 min)
7. Test basic modal open/close (30 min)

### Day 2 Morning (3 hours)
8. Create JavaScript file (90 min)
9. Style navigation arrows (45 min)
10. Test navigation (45 min)

### Day 2 Afternoon (2 hours)
11. Style pagination dots (45 min)
12. Style metadata panel (75 min)

### Day 3 Morning (2 hours)
13. Integrate VideoJS theme (60 min)
14. Refine animations (60 min)

### Day 3 Afternoon (2 hours)
15. Test on mobile devices (60 min)
16. Run accessibility audit (30 min)
17. Fix issues (30 min)

---

## 📝 Version Control

### Git Workflow
```bash
# Create feature branch
git checkout -b feature/modal-viewer-swiper

# Stage your work
git add web/themes/custom/fridaynightskate/

# Commit incrementally
git commit -m "feat: Add modal viewer SCSS files"
git commit -m "feat: Add modal viewer JavaScript"
git commit -m "feat: Add modal viewer Twig template"
git commit -m "feat: Integrate VideoJS theme"
git commit -m "test: Add accessibility improvements"

# Push when complete
git push origin feature/modal-viewer-swiper
```

---

## 🎉 You're All Set!

**Everything you need is in this directory.**

**Start with:** `HANDOFF_SUMMARY.md`  
**Build with:** `THEMER_QUICK_REFERENCE.md`  
**Reference:** All other files as needed  

**Questions?** Review the docs or coordinate with the agent team.

**Let's build something unforgettable! 🎨🛹🌃**

---

**Last Updated:** January 2025  
**Designer:** UX/UI Designer Agent  
**Status:** Complete & Ready for Implementation
