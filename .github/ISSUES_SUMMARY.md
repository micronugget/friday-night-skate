# Friday Night Skate Archive - GitHub Issues Summary

## Document Overview
This repository now contains a comprehensive GitHub Issues plan for implementing the Friday Night Skate Archive feature.

**File Location:** `.github/ISSUES.md`  
**Total Issues:** 16 (1 Epic + 15 Sub-Issues)  
**Lines of Code:** 1,192 lines  
**Status:** Ready for implementation

---

## Epic Issue

### Epic: Friday Night Skate Archive Feature
The main Epic Issue that encompasses all archive functionality. This issue provides:
- Project context and technical architecture
- Key features and success criteria
- Links to all 15 sub-issues
- High-level overview for stakeholders

---

## Sub-Issues Breakdown

### 1️⃣ Foundation Issues (Start Here)
These issues establish the core architecture and can be developed in parallel:

- **Sub-Issue #1: Taxonomy & Content Architecture**
  - Creates the foundational content structure
  - Sets up skate date taxonomy and archive media content type
  - No dependencies - start immediately

- **Sub-Issue #5: VideoJS Media Module Testing & Migration**
  - Tests the custom videojs_media module
  - Migrates away from videojs_mediablock
  - No dependencies - start immediately

- **Sub-Issue #11: User Registration & Roles**
  - Sets up user accounts and permissions
  - No dependencies - start immediately

---

### 2️⃣ Core Backend Issues (Sequential)
These issues build on the foundation:

- **Sub-Issue #2: Media & Metadata Extraction Service**
  - **Critical:** GPS extraction with ffprobe before YouTube upload
  - Implements metadata preservation
  - Depends on: #1

- **Sub-Issue #3: Moderation & Content Workflow**
  - Content moderation workflow (Draft → Review → Published)
  - Email notifications
  - Depends on: #1

- **Sub-Issue #10: GPS Privacy & User Consent**
  - Privacy controls for GPS data
  - User consent management
  - Depends on: #2

---

### 3️⃣ User Interface Issues
Build the upload experience:

- **Sub-Issue #4: Bulk Upload & User Interface**
  - Multi-file upload with media_library_bulk_upload
  - YouTube URL integration
  - Mobile-optimized forms
  - Depends on: #1, #2, #3

---

### 4️⃣ Display & Frontend Issues (Mostly Parallel)
Create the public-facing archive:

- **Sub-Issue #6: Archive Views with Masonry Grid**
  - Masonry.js grid layout
  - Taxonomy-based views
  - Depends on: #1, #5

- **Sub-Issue #7: Modal Viewer with Swiper Navigation**
  - Bootstrap 5 modal with Swiper.js
  - Touch/swipe navigation
  - Depends on: #6

- **Sub-Issue #8: Responsive Image Styles & Performance**
  - WebP format optimization
  - Bootstrap 5 breakpoints
  - Depends on: #6

- **Sub-Issue #9: Starry Night Radix 6 Subtheme**
  - Van Gogh-inspired theme design
  - Custom color palette and animations
  - Depends on: #6, #7
  - **Design Reference:** `.github/agents/skills/frontend-design/SKILL.md`

---

### 5️⃣ Optimization Issues
Enhance performance and security:

- **Sub-Issue #12: Performance Optimization**
  - Lighthouse > 90 target
  - Caching strategies
  - Depends on: #6, #7, #8

- **Sub-Issue #13: Security Audit & Validation**
  - Comprehensive security testing
  - XSS, CSRF, SQL injection prevention
  - Depends on: #2, #4, #11

---

### 6️⃣ Final Issues (After Features Complete)
Documentation and deployment:

- **Sub-Issue #14: Documentation & User Guides**
  - User, moderator, and developer guides
  - Depends on: All features

- **Sub-Issue #15: OpenLiteSpeed Deployment Configuration**
  - Production environment setup
  - Ansible deployment automation
  - Depends on: All features

---

## Technical Requirements Met ✅

### Backend Validation
All backend issues include:
- ✅ `ddev phpunit` tests (9 references)
- ✅ `ddev drush cst` configuration validation
- ✅ `ddev drush cex` configuration export
- ✅ `ddev drush cr` cache clearing

### Frontend Validation
All frontend issues include:
- ✅ `ddev yarn test:nightwatch` tests (5 references)
- ✅ Bootstrap 5 compliance verification
- ✅ Responsive design testing
- ✅ Accessibility testing

### Specific Requirements
- ✅ **Moderation & Workflow:** Dedicated Sub-Issue #3
- ✅ **Media & Metadata:** Sub-Issue #2 with ffprobe GPS extraction (13 references)
- ✅ **Visual Inspiration:** Sub-Issue #9 references "Starry Night" (10 references)

---

## Key Features of Each Issue

### Comprehensive Detail Level
Each sub-issue includes:
1. **Description:** Clear explanation of the issue's purpose
2. **Requirements:** Detailed functional and technical requirements
3. **Technical Tasks:** Step-by-step implementation checklist
4. **Validation:** Testing and verification procedures
5. **Dependencies:** Links to prerequisite issues
6. **Handoff:** Which team role handles the issue

### Development Workflow
Issues are organized for efficient development:
- Clear dependency graph provided
- Parallel work opportunities identified
- Sequential requirements highlighted
- Estimated timeline: 8-12 weeks with 2-3 developers

---

## Dependency Graph

```
Foundation (Parallel):
├─ #1 Taxonomy & Content Architecture
├─ #5 VideoJS Media Module Testing
└─ #11 User Registration & Roles

Core Backend (Sequential):
├─ #2 Media & Metadata Extraction
│  └─ #10 GPS Privacy & User Consent
└─ #3 Moderation & Content Workflow

User Interface:
└─ #4 Bulk Upload & User Interface
   ├─ Depends on: #1, #2, #3
   └─ Feeds into: #13 Security Audit

Display & Frontend (Parallel):
├─ #6 Archive Views with Masonry Grid
│  ├─ #7 Modal Viewer with Swiper Navigation
│  ├─ #8 Responsive Image Styles
│  └─ #9 Starry Night Radix 6 Subtheme

Optimization:
├─ #12 Performance Optimization (after #6, #7, #8)
└─ #13 Security Audit (after #2, #4, #11)

Final (After All):
├─ #14 Documentation & User Guides
└─ #15 OpenLiteSpeed Deployment Configuration
```

---

## How to Use This Document

### For the CTO
1. Review the Epic Issue for high-level overview
2. Approve any new contrib modules listed in sub-issues
3. Review Sub-Issue #9 for theme design direction
4. Use dependency graph to plan resource allocation

### For Project Managers
1. Copy issues from `.github/ISSUES.md` into GitHub
2. Create Epic Issue first, then create sub-issues and link them
3. Use the dependency graph to plan sprints
4. Track progress using the provided checklists

### For Developers
1. Start with foundation issues (#1, #5, #11)
2. Follow dependency graph for implementation order
3. Use technical task checklists within each issue
4. Run validation tests as specified in each issue

### For DevOps
1. Review Sub-Issue #15 for deployment requirements
2. Prepare OpenLiteSpeed environment
3. Set up Ansible playbooks from micronugget/ansible repo
4. Ensure ffprobe is installed in development and production

---

## Technology Stack Summary

### Backend
- **Platform:** Drupal CMS 2 (Drupal 11)
- **Custom Modules:** videojs_media, skating_video_uploader
- **Contrib Modules:** media_library_bulk_upload, radix, videojs_mediablock (deprecated)
- **Metadata:** ffprobe for video GPS extraction, exif_read_data for images

### Frontend
- **Theme:** Radix 6 Bootstrap 5 subtheme ("Starry Night" design)
- **JavaScript Libraries:** Masonry.js, Swiper.js, VideoJS
- **Optimization:** WebP images, responsive image styles, lazy loading

### Development
- **Environment:** DDEV on Ubuntu 24.04
- **Testing:** PHPUnit, Drush, Nightwatch
- **IDE:** PHPStorm (primary), VSCode (for Copilot features)

### Production
- **Server:** OpenLiteSpeed + MySQL 8.0 on Ubuntu 24.04
- **Deployment:** Git-based with Ansible (micronugget/ansible repo)
- **Caching:** LSCache for OpenLiteSpeed

---

## Next Steps

1. **Immediate Actions:**
   - Review and approve the issues structure
   - Copy issues from `.github/ISSUES.md` to GitHub repository
   - Create Epic Issue first, then sub-issues
   - Assign initial team members to foundation issues

2. **Week 1-2:**
   - Begin development on #1, #5, #11 (parallel)
   - Set up development environments
   - Review and approve any new contrib modules

3. **Week 3-6:**
   - Implement core backend issues (#2, #3, #10)
   - Develop upload interface (#4)
   - Begin frontend issues (#6, #7, #8)

4. **Week 7-10:**
   - Complete theme design (#9)
   - Optimize performance (#12)
   - Security audit (#13)

5. **Week 11-12:**
   - Documentation (#14)
   - Production deployment (#15)
   - Final testing and launch

---

## Success Metrics

Upon completion, the Friday Night Skate Archive will:
- ✅ Allow community members to upload images and videos
- ✅ Preserve GPS and metadata before YouTube upload
- ✅ Provide organized archive views by skate date
- ✅ Offer intuitive modal viewer with swipe navigation
- ✅ Maintain high performance (Lighthouse > 90)
- ✅ Pass comprehensive security audit
- ✅ Include complete documentation
- ✅ Deploy successfully to OpenLiteSpeed production server

---

**Document Created:** 2026-01-28  
**Repository:** micronugget/friday-night-skate  
**Prepared By:** GitHub Copilot Enterprise  
**For Review By:** CTO, Micronugget
