# Design Specifications Directory

**Project:** Friday Night Skate  
**Feature:** Bootstrap 5 Modal Viewer with Swiper.js Navigation  
**Designer:** UX/UI Designer Agent  
**Date:** January 2025  

---

## 📁 Files in This Directory

### 1. `modal-viewer-swiper-ux-ui.md` (MAIN SPEC)
**🎨 Complete Design Specification - 40,000+ words**

The comprehensive design document containing:
- Design vision and aesthetic direction
- Complete color system (CSS variables)
- Typography specifications (Staatliches + Archivo)
- Spacing and layout for all breakpoints
- Detailed component breakdown (7 major components)
- Animation and motion specifications
- Full accessibility guidelines (WCAG AAA)
- Bootstrap 5 component usage
- Custom CSS requirements
- Implementation guidance
- Code examples (SCSS, JavaScript, Twig)

**Use this for:** Complete reference, technical implementation details, code examples.

---

### 2. `THEMER_QUICK_REFERENCE.md`
**⚡ Implementation Checklist - Quick Start**

A condensed guide containing:
- 8-phase implementation checklist
- Copy-paste ready design tokens
- Key measurements table
- Animation timing reference
- Critical implementation notes
- Coordination requirements
- Common issues and solutions
- Success criteria

**Use this for:** Day-to-day implementation, quick lookups, progress tracking.

---

### 3. `WIREFRAMES.md`
**📐 Visual Reference - Layouts & States**

ASCII wireframes and diagrams showing:
- Mobile, tablet, and desktop layouts
- Component detail views
- Color coding legend
- State diagrams
- Z-index stacking
- Touch zones
- Animation timelines
- Focus order
- Testing matrix

**Use this for:** Visual understanding, layout planning, stakeholder communication.

---

## 🎯 Quick Start for Themer

### First Time Setup (15 minutes)
1. Read `THEMER_QUICK_REFERENCE.md` (5 min)
2. Skim `WIREFRAMES.md` to visualize layout (5 min)
3. Bookmark `modal-viewer-swiper-ux-ui.md` for reference (5 min)

### During Implementation
1. Follow checklist in `THEMER_QUICK_REFERENCE.md`
2. Reference `WIREFRAMES.md` for layout questions
3. Copy code from `modal-viewer-swiper-ux-ui.md`

### When Stuck
1. Check "Common Issues" in `THEMER_QUICK_REFERENCE.md`
2. Review specific component section in main spec
3. Check wireframe for visual clarification

---

## 🎨 Design System Summary

### Aesthetic: Urban Nocturnal Energy
- **Primary Accent:** Electric Teal (`#00ff9f`)
- **Secondary Accent:** Hot Pink (`#ff006e`)
- **Tertiary Accent:** Street Light Yellow (`#ffd60a`)
- **Background:** Near-Black (`#0a0a0f`)

### Typography
- **Display:** Staatliches (Bold, condensed, urban racing)
- **Body:** Archivo (Clean, modern, readable)

### Key Principle
**Mobile-first, night-time skating vibe with smooth, momentum-based interactions.**

---

## 📊 Feature Breakdown

| Component | Mobile | Desktop | Complexity |
|-----------|--------|---------|------------|
| Modal Container | Fullscreen | Centered | Low |
| Close Button | 48×48 circle | 48×48 circle | Low |
| Swiper Slides | Touch swipe | Keyboard + mouse | Medium |
| Navigation Arrows | Hidden | 56×56 circles | Low |
| Pagination Dots | 12px | 10px | Low |
| Metadata Panel | Slide up | Slide up | Medium |
| VideoJS Theme | Hot pink play | Hot pink play | Medium |

**Total Estimated Time:** 8-12 hours

---

## 🔄 Design Process Workflow

```
┌─────────────────────────────────────────────────┐
│ ARCHITECT                                       │
│ Strategic planning completed                    │
└─────────────┬───────────────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────────────┐
│ UX/UI DESIGNER (YOU ARE HERE)                   │
│ Design specifications created                   │
│ - modal-viewer-swiper-ux-ui.md                  │
│ - THEMER_QUICK_REFERENCE.md                     │
│ - WIREFRAMES.md                                 │
└─────────────┬───────────────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────────────┐
│ THEMER (NEXT STEP)                              │
│ Implementation:                                 │
│ 1. Create SCSS files                            │
│ 2. Create Twig template                         │
│ 3. Create JavaScript                            │
│ 4. Build and test                               │
└─────────────┬───────────────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────────────┐
│ MEDIA DEVELOPER                                 │
│ VideoJS integration and customization           │
└─────────────┬───────────────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────────────┐
│ DRUPAL DEVELOPER                                │
│ Backend integration and data handling           │
└─────────────┬───────────────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────────────┐
│ TESTER                                          │
│ Quality assurance and accessibility testing     │
└─────────────────────────────────────────────────┘
```

---

## 🧩 Integration Points

### With Other Agents

**→ Themer** (IMMEDIATE NEXT)
- Receives: Complete design specs
- Implements: SCSS, Twig, JavaScript
- Coordinates: Media Dev, Drupal Dev

**→ Media Developer**
- Receives: VideoJS theme requirements
- Implements: VideoJS customization, hot pink play button
- Coordinates: Themer

**→ Drupal Developer**
- Receives: Data requirements (metadata, modal trigger)
- Implements: Backend integration, data attributes
- Coordinates: Themer

**→ Performance Engineer**
- Receives: Performance requirements
- Implements: Image lazy loading, preload optimization
- Coordinates: Themer, Media Dev

**→ Tester**
- Receives: Accessibility requirements, success criteria
- Implements: Comprehensive testing (PHPUnit, Nightwatch, axe)
- Coordinates: All agents

---

## 📋 Pre-Implementation Review

### Designer Self-Check
- [x] Aesthetic direction is bold and distinctive
- [x] Mobile-first approach throughout
- [x] All breakpoints specified
- [x] Touch targets meet WCAG AAA (≥ 44px)
- [x] Color contrast exceeds 7:1
- [x] Animation timings specified
- [x] Accessibility fully detailed
- [x] Code examples provided
- [x] Implementation guidance clear
- [x] Coordination points identified

### Themer Pre-Flight Check
Before starting implementation, verify:
- [ ] DDEV environment is running
- [ ] Node.js and npm installed
- [ ] Swiper.js available (CDN or npm)
- [ ] Bootstrap 5 theme active
- [ ] VideoJS library loaded
- [ ] Existing Masonry grid functional

---

## 🎓 Learning Resources

### Bootstrap 5 Modal
- **Docs:** https://getbootstrap.com/docs/5.3/components/modal/
- **Key Concepts:** Backdrop, focus trap, ESC key, responsive sizing

### Swiper.js
- **Docs:** https://swiperjs.com/swiper-api
- **Key Concepts:** Touch gestures, lazy loading, keyboard navigation, pagination

### VideoJS
- **Docs:** https://videojs.com/guides/
- **Key Concepts:** Theming, controls customization, events

### WCAG Accessibility
- **Docs:** https://www.w3.org/WAI/WCAG22/quickref/
- **Key Concepts:** Focus management, ARIA, keyboard navigation, color contrast

---

## 🚀 Success Metrics

### Design Quality
- **Distinctive:** Does it capture the night-skating vibe?
- **Functional:** Do all interactions work smoothly?
- **Accessible:** Does it meet WCAG AAA?
- **Responsive:** Does it adapt gracefully across devices?

### Implementation Quality
- **Performance:** Load time < 2 seconds
- **Smooth:** Swipe transitions feel natural (400ms)
- **Reliable:** Works on iOS Safari, Chrome, Firefox
- **Maintainable:** Code is clean, commented, modular

### User Experience
- **Intuitive:** First-time users understand without instructions
- **Delightful:** Micro-interactions create memorable moments
- **Efficient:** Users can navigate quickly (< 1 second per action)
- **Accessible:** Screen reader users can navigate independently

---

## 📞 Contact & Coordination

### Questions About Design?
- Review full spec: `modal-viewer-swiper-ux-ui.md`
- Check wireframes: `WIREFRAMES.md`
- Coordinate with: @ux-ui-designer

### Questions About Implementation?
- Check quick reference: `THEMER_QUICK_REFERENCE.md`
- Review common issues section
- Coordinate with: @themer

### Questions About Integration?
- VideoJS: @media-dev
- Backend/Data: @drupal-developer
- Performance: @performance-engineer
- Testing: @tester

---

## 🎉 Design Handoff Status

**Status:** ✅ Complete  
**Date:** January 2025  
**Designer:** UX/UI Designer Agent  
**Next Agent:** @themer  
**Priority:** High  
**Estimated Implementation:** 8-12 hours  

---

## 📝 Version History

### v1.0 (January 2025)
- Initial design specification
- Complete component breakdown
- Accessibility guidelines
- Implementation guidance
- Wireframes and visual reference
- Themer quick reference

---

**🎨 Design is complete. Ready for implementation! 🚀**
