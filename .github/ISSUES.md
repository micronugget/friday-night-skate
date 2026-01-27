# Friday Night Skate - Project Issues for GitHub
Copy these issues to your GitHub repository.
---
## Issue #1: Archive Content Type & Entity Structure
**Labels:** `epic`, `backend`, `architecture`  
**Priority:** High
Create "Skate Session" content type with date field, media reference, user reference, GPS fields, and moderation workflow.
**Handoff:** → Media Developer
---
## Issue #2: GPS Metadata Extraction Service
**Labels:** `backend`, `media`, `critical`  
**Priority:** Critical
Implement PHP service using `exif_read_data()` for images and `ffprobe` for videos. Store GPS before YouTube upload.
**Dependencies:** Issue #1  
**Handoff:** → Drupal Developer
---
## Issue #3: YouTube Video Integration
**Labels:** `backend`, `media`, `integration`  
**Priority:** High
YouTube URL validation, thumbnail retrieval, VideoJS media lock integration.
**Dependencies:** Issue #1, Issue #2  
**Handoff:** → Themer
---
## Issue #4: User Media Upload Form
**Labels:** `backend`, `frontend`, `ux`  
**Priority:** High
Multi-file upload, YouTube URL input, date picker, progress indicators, mobile-friendly.
**Dependencies:** Issue #1, #2, #3  
**Handoff:** → Themer, Tester
---
## Issue #5: Masonry Grid Archive View
**Labels:** `frontend`, `theme`, `views`  
**Priority:** High
Responsive Masonry.js with imagesLoaded, lazy loading, Bootstrap 5 breakpoints.
**Dependencies:** Issue #1, #3  
**Handoff:** → Performance Engineer
---
## Issue #6: Modal Image/Video Viewer with Swiper
**Labels:** `frontend`, `theme`, `mobile`  
**Priority:** High
Bootstrap 5 modal with Swiper.js for touch navigation, VideoJS integration, accessibility.
**Dependencies:** Issue #5  
**Handoff:** → Tester
---
## Issue #7: Responsive Image Styles
**Labels:** `frontend`, `performance`  
**Priority:** Medium
WebP format, Bootstrap 5 breakpoints, srcset/sizes, lazy loading, Lighthouse > 90.
**Dependencies:** Issue #5  
**Handoff:** → Tester
---
## Issue #8: Content Moderation Workflow
**Labels:** `backend`, `security`, `workflow`  
**Priority:** Medium
Draft → Review → Published states, email notifications, moderator role.
**Dependencies:** Issue #1  
**Handoff:** → Technical Writer
---
## Issue #9: User Registration & Authentication
**Labels:** `backend`, `security`  
**Priority:** Medium
Email verification, "Skater" role, profile page, GDPR compliance.
**Dependencies:** None  
**Handoff:** → Technical Writer
---
## Issue #10: GPS Privacy & User Consent
**Labels:** `security`, `privacy`, `legal`  
**Priority:** High
GPS disclosure, strip option, consent checkbox, privacy policy updates.
**Dependencies:** Issue #2  
**Handoff:** → Drupal Developer
---
## Issue #11: Performance Optimization
**Labels:** `performance`  
**Priority:** Medium
LCP < 2.5s, FID < 100ms, CLS < 0.1, caching configuration.
**Dependencies:** Issue #5, #6, #7  
**Handoff:** → Tester
---
## Issue #12: Security Audit
**Labels:** `security`, `audit`  
**Priority:** High
File upload validation, XSS/CSRF/SQLi prevention, rate limiting.
**Dependencies:** Issue #2, #4, #9  
**Handoff:** → Architect
---
## Issue #13: Documentation
**Labels:** `documentation`  
**Priority:** Medium
User guides, moderator guide, developer guide, CHANGELOG.
**Dependencies:** All features  
**Handoff:** → Architect
---
## Issue #14: Deployment Configuration
**Labels:** `devops`, `deployment`  
**Priority:** Medium
OpenLiteSpeed config, private files, ffprobe, SSL, rollback procedure.
**Dependencies:** All features  
**Handoff:** → Tester
---
## Dependency Graph
```
#1 Content Type
├── #2 GPS Extraction → #10 Privacy
├── #3 YouTube
├── #8 Moderation
└── #5 Masonry → #6 Modal, #7 Images, #11 Performance
#9 User Auth (independent)
#12 Security (after #2, #4, #9)
#13 Docs, #14 Deployment (after all)
```
