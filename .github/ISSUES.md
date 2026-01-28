# Friday Night Skate Archive - GitHub Issues

**Instructions:** Copy these issues to your GitHub repository. Create the Epic Issue first, then create each Sub-Issue and link them to the Epic.

---

## Epic Issue: Friday Night Skate Archive Feature

**Title:** Epic: Friday Night Skate Archive Feature  
**Labels:** `epic`, `enhancement`, `archive`  
**Priority:** Critical  
**Status:** Open

### Overview
Build a comprehensive archive system for the Friday Night Skate community where users can upload images and videos from skate sessions, organized by taxonomy-based skate dates, with full metadata preservation and public viewing capabilities.

### Project Context
- **Platform:** Drupal CMS 2 (Drupal 11)
- **Theme:** Radix 6 Bootstrap 5 subtheme with "Starry Night" inspiration
- **Development:** DDEV on Ubuntu 24.04
- **Production:** OpenLiteSpeed + MySQL 8.0 on Ubuntu 24.04
- **Repository:** GitHub (primary) + GitLab (mirror)

### Key Features
1. User-generated content upload (images and YouTube-hosted videos)
2. GPS and timestamp metadata extraction and preservation
3. Taxonomy-based organization by skate session dates
4. Content moderation workflow
5. Public masonry grid views with modal viewer
6. Mobile-optimized navigation with swipe gestures
7. Responsive image optimization with WebP format
8. Privacy controls for GPS data

### Technical Architecture
- Custom modules: `videojs_media`, `skating_video_uploader`
- Bulk upload: `media_library_bulk_upload`
- Video playback: VideoJS Media (replacing VideoJS MediaBlock)
- Frontend: Masonry.js + Swiper.js + Bootstrap 5
- Metadata: ffprobe for video GPS extraction

### Success Criteria
- All uploads tagged with valid skate date taxonomy terms
- Metadata preserved before YouTube upload
- Content moderation workflow functional
- Public archive views with modal + swipe navigation
- Performance: Lighthouse score > 90
- Security: Full audit passed
- Testing: All tests passing (PHPUnit, Drush, Nightwatch)

### Sub-Issues
This epic tracks the following sub-issues:
- #1: Taxonomy & Content Architecture
- #2: Media & Metadata Extraction Service
- #3: Moderation & Content Workflow
- #4: Bulk Upload & User Interface
- #5: VideoJS Media Module Testing & Migration
- #6: Archive Views with Masonry Grid
- #7: Modal Viewer with Swiper Navigation
- #8: Responsive Image Styles & Performance
- #9: Starry Night Radix 6 Subtheme
- #10: GPS Privacy & User Consent
- #11: User Registration & Roles
- #12: Performance Optimization
- #13: Security Audit & Validation
- #14: Documentation & User Guides
- #15: OpenLiteSpeed Deployment Configuration

---

## Sub-Issue #1: Taxonomy & Content Architecture

**Title:** Archive Taxonomy Structure & Content Type Setup  
**Labels:** `backend`, `architecture`, `content-type`  
**Priority:** Critical  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Create the foundational content architecture for the Friday Night Skate Archive including taxonomy vocabulary for skate dates and content types for managing uploaded media.

### Requirements
1. **Taxonomy Vocabulary: "Skate Dates"**
   - Machine name: `skate_dates`
   - Format: "YYYY-MM-DD - Location/Description"
   - Required field on all archive media
   - Hierarchical: No
   - Multiple values: No

2. **Content Type: "Archive Media"**
   - Machine name: `archive_media`
   - Fields:
     - Title (auto-generated from upload)
     - Media field (Image or Video reference)
     - Skate Date (Taxonomy reference, required)
     - GPS Coordinates (Geofield)
     - Timestamp (Date/Time)
     - Uploader (User reference, auto-populate)
     - Metadata (JSON field for exif/ffprobe data)
   - View modes: Full, Teaser, Thumbnail, Modal
   - URL pattern: `/archive/{skate_date}/{node_id}`

3. **Media Types**
   - Image: JPEG, PNG, WebP
   - Video: YouTube URL (via VideoJS Media module)

### Technical Tasks
- [ ] Create `skate_dates` vocabulary via config or Drush
- [ ] Create `archive_media` content type
- [ ] Configure all required fields with proper validation
- [ ] Set up view modes
- [ ] Configure URL aliases (Pathauto)
- [ ] Export configuration: `ddev drush cex`
- [ ] Write update hook for existing sites

### Validation
- [ ] Run `ddev drush cst` to verify config
- [ ] Run `ddev phpunit` for content type tests
- [ ] Create test content via UI to verify fields
- [ ] Verify taxonomy term assignment works

### Dependencies
None (foundational issue)

### Handoff
→ Backend Developer → Media Developer

---

## Sub-Issue #2: Media & Metadata Extraction Service

**Title:** GPS & Metadata Extraction Before YouTube Upload  
**Labels:** `backend`, `media`, `critical`, `metadata`  
**Priority:** Critical  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Implement metadata extraction service using `ffprobe` for videos and `exif_read_data()` for images. Extract GPS coordinates, timestamps, and all available metadata BEFORE videos are uploaded to YouTube (which scrubs metadata).

### Requirements
1. **Image Metadata Extraction**
   - Use PHP `exif_read_data()` for images
   - Extract: GPS coordinates, timestamp, camera model, ISO, aperture
   - Store in JSON field on Archive Media node

2. **Video Metadata Extraction**
   - Use `ffprobe` (via `ddev exec`) on private:// files
   - Extract: GPS coordinates from MOV/MP4 metadata
   - Extract: timestamp, duration, resolution, codec
   - Store before YouTube upload

3. **MetadataExtractor Service**
   - Service: `skating_video_uploader.metadata_extractor`
   - Methods:
     - `extractImageMetadata($file)`: returns array
     - `extractVideoMetadata($file)`: returns array via ffprobe
     - `storeMetadata($node, $metadata)`: save to JSON field
   - Integration with upload workflow

4. **ffprobe Commands**
   ```bash
   ddev exec ffprobe -v quiet -print_format json -show_format -show_streams file.mp4
   ```

### Technical Tasks
- [ ] Update `MetadataExtractor` service in `skating_video_uploader`
- [ ] Implement `extractImageMetadata()` with exif_read_data()
- [ ] Implement `extractVideoMetadata()` using ffprobe subprocess
- [ ] Add JSON metadata storage field to Archive Media
- [ ] Hook into upload workflow (hook_media_presave)
- [ ] Handle errors gracefully (missing exif, ffprobe not found)
- [ ] Write PHPUnit tests for metadata extraction
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev phpunit --group metadata` tests
- [ ] Test with real images containing GPS data
- [ ] Test with video files from smartphones (MOV/MP4)
- [ ] Verify metadata stored before YouTube upload
- [ ] Run `ddev drush cr` after changes

### Dependencies
- Sub-Issue #1: Taxonomy & Content Architecture

### Handoff
→ Backend Developer → Security Specialist

---

## Sub-Issue #3: Moderation & Content Workflow

**Title:** Content Moderation Workflow for Archive Submissions  
**Labels:** `backend`, `workflow`, `moderation`, `security`  
**Priority:** High  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Implement Drupal Content Moderation workflow for user-submitted archive media. All uploads must be reviewed before publication.

### Requirements
1. **Moderation States**
   - Draft: Initial upload state
   - Review: Submitted for moderation
   - Published: Approved and visible to public
   - Archived: Hidden but preserved

2. **Moderation Workflow**
   - Name: "Archive Review"
   - Applied to: Archive Media content type
   - Transitions:
     - Draft → Review (uploader)
     - Review → Published (moderator)
     - Review → Draft (moderator, with reason)
     - Published → Archived (moderator)

3. **Roles & Permissions**
   - Skater: Upload, edit own, transition to Review
   - Moderator: Review all, publish, archive, edit any
   - Anonymous: View Published only

4. **Email Notifications**
   - On submission: Notify moderators
   - On approval: Notify uploader
   - On rejection: Notify uploader with reason

### Technical Tasks
- [ ] Enable Content Moderation module
- [ ] Create "Archive Review" workflow
- [ ] Configure moderation states and transitions
- [ ] Apply workflow to Archive Media content type
- [ ] Create "Moderator" role with permissions
- [ ] Configure email notifications (Rules or custom)
- [ ] Create moderation dashboard view
- [ ] Export configuration: `ddev drush cex`
- [ ] Write tests for workflow transitions

### Validation
- [ ] Run `ddev phpunit --group moderation` tests
- [ ] Test full workflow: Upload → Review → Publish
- [ ] Verify permissions for each role
- [ ] Test email notifications
- [ ] Run `ddev drush cr` after changes

### Dependencies
- Sub-Issue #1: Taxonomy & Content Architecture

### Handoff
→ Drupal Developer → Technical Writer

---

## Sub-Issue #4: Bulk Upload & User Interface

**Title:** User Media Upload Form with Bulk Support  
**Labels:** `backend`, `frontend`, `ux`, `forms`  
**Priority:** High  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Create user-friendly upload interface with bulk image upload support, YouTube URL input, and mobile-responsive design. Integrate `media_library_bulk_upload` module.

### Requirements
1. **Bulk Image Upload**
   - Use `drupal/media_library_bulk_upload` module
   - Drag-and-drop interface
   - Multiple file selection
   - Progress indicators
   - Client-side validation (file type, size)

2. **YouTube Video Upload**
   - URL input field with validation
   - Thumbnail preview
   - Metadata guidance tooltip
   - Instructions: "Upload to YouTube as Unlisted, then paste URL here"

3. **Required Fields**
   - Skate Date (autocomplete taxonomy select)
   - Bulk apply skate date to all uploads
   - Attribution (default to uploader, editable)

4. **Upload Workflow**
   - Stage 1: Select files + YouTube URLs
   - Stage 2: Extract metadata (show progress)
   - Stage 3: Assign skate date
   - Stage 4: Review & submit for moderation

5. **Mobile Optimization**
   - Touch-friendly file picker
   - Camera integration (capture directly)
   - Responsive Bootstrap 5 layout

### Technical Tasks
- [ ] Install `media_library_bulk_upload`: `ddev composer require drupal/media_library_bulk_upload`
- [ ] Create custom upload form in `skating_video_uploader`
- [ ] Integrate bulk upload widget
- [ ] Add YouTube URL field with validation
- [ ] Create multi-step form with progress indicator
- [ ] Implement metadata extraction integration
- [ ] Add skate date autocomplete
- [ ] Style with Bootstrap 5 / Radix 6
- [ ] Add client-side validation (JavaScript)
- [ ] Write form tests
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev phpunit --group upload_form` tests
- [ ] Run `ddev yarn test:nightwatch` for UI tests
- [ ] Test bulk upload with 10+ images
- [ ] Test YouTube URL validation
- [ ] Test on mobile devices (responsive)
- [ ] Verify Bootstrap 5 compliance

### Dependencies
- Sub-Issue #1: Taxonomy & Content Architecture
- Sub-Issue #2: Media & Metadata Extraction
- Sub-Issue #3: Moderation & Content Workflow

### Handoff
→ UX Designer → Frontend Developer → Tester

---

## Sub-Issue #5: VideoJS Media Module Testing & Migration

**Title:** Test videojs_media Module & Migrate from videojs_mediablock  
**Labels:** `backend`, `media`, `testing`, `migration`  
**Priority:** High  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
The custom `web/modules/custom/videojs_media` module hasn't been thoroughly tested. Create comprehensive tests for its functionality and migrate from the old `videojs_mediablock` module.

### Requirements
1. **Module Testing**
   - Entity CRUD operations
   - Access control
   - YouTube URL integration
   - VideoJS player rendering
   - Lock mechanism (if applicable)

2. **Migration from videojs_mediablock**
   - Identify dependencies on old module
   - Update `skating_video_uploader` to use new module
   - Migrate any existing content
   - Remove old module references

3. **Documentation**
   - API documentation for VideoJsMedia entity
   - Usage examples
   - Integration guide

### Technical Tasks
- [ ] Review `web/modules/custom/videojs_media` code
- [ ] Write PHPUnit tests for VideoJsMedia entity
- [ ] Write PHPUnit tests for VideoJsMediaBlock plugin
- [ ] Write kernel tests for access control
- [ ] Write functional tests for player rendering
- [ ] Update `skating_video_uploader` dependencies
- [ ] Remove `videojs_mediablock` from composer.json
- [ ] Write migration path documentation
- [ ] Create README.md for module
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev phpunit web/modules/custom/videojs_media`
- [ ] Achieve 80%+ code coverage
- [ ] Test VideoJS player rendering in browser
- [ ] Test YouTube URL embedding
- [ ] Test access controls
- [ ] Run `ddev drush cr` after changes

### Dependencies
- Sub-Issue #1: Taxonomy & Content Architecture

### Handoff
→ Backend Developer → QA Tester

---

## Sub-Issue #6: Archive Views with Masonry Grid

**Title:** Masonry Grid View for Archive Display  
**Labels:** `frontend`, `views`, `theme`, `masonry`  
**Priority:** High  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Create taxonomy-based archive views using Masonry.js for responsive grid layout. Each skate date has its own archive page showing all approved media.

### Requirements
1. **Archive View**
   - Path: `/archive/{skate_date_term}`
   - Filter: Published Archive Media by skate date
   - Display: Masonry grid layout
   - Items: Image thumbnails + video poster images

2. **Masonry.js Integration**
   - Responsive column layout (Bootstrap 5 breakpoints)
   - ImagesLoaded integration (prevent layout shift)
   - Lazy loading for images
   - Infinite scroll or pagination

3. **Item Display**
   - Image: Responsive image style (see Sub-Issue #8)
   - Video: Poster image with play icon overlay
   - Metadata icon: Small icon in lower-right corner
   - Hover effect: Scale + shadow (Bootstrap 5)

4. **Bootstrap 5 Breakpoints**
   - xs: 1 column
   - sm: 2 columns
   - md: 3 columns
   - lg: 4 columns
   - xl: 5 columns

### Technical Tasks
- [ ] Create Drupal View: "Archive by Skate Date"
- [ ] Configure contextual filter for taxonomy term
- [ ] Create view mode: "Archive Thumbnail"
- [ ] Install Masonry.js: `npm install masonry-layout imagesloaded`
- [ ] Create JavaScript integration for Masonry
- [ ] Implement lazy loading
- [ ] Style grid items with Bootstrap 5
- [ ] Add metadata icon to thumbnails
- [ ] Create Radix 6 template overrides
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev yarn test:nightwatch` for UI tests
- [ ] Test responsive layout at all breakpoints
- [ ] Test lazy loading performance
- [ ] Verify no layout shift (CLS < 0.1)
- [ ] Test with 50+ images
- [ ] Verify Bootstrap 5 compliance

### Dependencies
- Sub-Issue #1: Taxonomy & Content Architecture
- Sub-Issue #5: VideoJS Media Module Testing

### Handoff
→ Frontend Developer → Performance Engineer

---

## Sub-Issue #7: Modal Viewer with Swiper Navigation

**Title:** Modal Image/Video Viewer with Swiper.js Navigation  
**Labels:** `frontend`, `theme`, `mobile`, `modal`  
**Priority:** High  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Implement Bootstrap 5 modal viewer with Swiper.js for touch-friendly navigation. Users can swipe left/right on mobile or use arrow keys on desktop to navigate between media items.

### Requirements
1. **Modal Viewer**
   - Bootstrap 5 modal component
   - Fullscreen on mobile
   - Overlay with transparent background
   - Close button (X) and ESC key support

2. **Swiper.js Navigation**
   - Touch gestures: Swipe left/right
   - Keyboard: Arrow keys
   - Mouse: Arrow buttons
   - Wrap around: Last → First
   - Preload: Next/previous items

3. **Content Display**
   - Images: Full resolution, responsive
   - Videos: VideoJS player with YouTube embed
   - Metadata panel: GPS, timestamp, uploader
   - Metadata icon: Click to show/hide panel

4. **Mobile Optimization**
   - Touch-friendly buttons (min 44x44px)
   - Swipe gestures smooth and responsive
   - Video controls optimized for mobile

5. **Accessibility**
   - ARIA labels
   - Keyboard navigation
   - Screen reader support
   - Focus management

### Technical Tasks
- [ ] Install Swiper.js: `npm install swiper`
- [ ] Create modal template (Twig)
- [ ] Initialize Swiper in JavaScript
- [ ] Implement modal trigger from grid items
- [ ] Integrate VideoJS for video playback
- [ ] Create metadata panel component
- [ ] Add keyboard navigation handlers
- [ ] Style with Bootstrap 5 / Radix 6
- [ ] Add accessibility attributes
- [ ] Write Nightwatch tests
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev yarn test:nightwatch` for UI tests
- [ ] Test swipe gestures on mobile devices
- [ ] Test keyboard navigation
- [ ] Test VideoJS player in modal
- [ ] Test metadata panel toggle
- [ ] Verify accessibility (ARIA, keyboard)
- [ ] Verify Bootstrap 5 compliance

### Dependencies
- Sub-Issue #6: Archive Views with Masonry Grid
- Sub-Issue #5: VideoJS Media Module Testing

### Handoff
→ Frontend Developer → UX Designer → Tester

---

## Sub-Issue #8: Responsive Image Styles & Performance

**Title:** Responsive Image Styles with WebP Format  
**Labels:** `frontend`, `performance`, `images`  
**Priority:** Medium  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Create responsive image styles optimized for Bootstrap 5 breakpoints with WebP format as default. Implement lazy loading and efficient caching.

### Requirements
1. **Image Styles**
   - `archive_thumbnail`: 400x400 crop, WebP
   - `archive_medium`: 800x600 scale, WebP
   - `archive_large`: 1200x900 scale, WebP
   - `archive_full`: 1920x1440 max, WebP
   - Fallback: JPEG for older browsers

2. **Responsive Images**
   - Use Drupal responsive image module
   - srcset with multiple sizes
   - sizes attribute for Bootstrap 5 breakpoints
   - Lazy loading: loading="lazy"

3. **View Modes Integration**
   - Archive Thumbnail: `archive_thumbnail`
   - Modal view: `archive_large` or `archive_full`
   - Teaser: `archive_medium`

4. **Performance Targets**
   - Lighthouse Performance > 90
   - LCP < 2.5s
   - CLS < 0.1
   - Image optimization: WebP < 100KB

### Technical Tasks
- [ ] Enable Responsive Image module
- [ ] Create image styles (Admin UI or config)
- [ ] Configure WebP format for styles
- [ ] Create responsive image style mapping
- [ ] Configure breakpoints.yml for Bootstrap 5
- [ ] Update view modes to use responsive images
- [ ] Implement lazy loading attribute
- [ ] Configure image cache headers
- [ ] Export configuration: `ddev drush cex`
- [ ] Run Lighthouse audit

### Validation
- [ ] Run Lighthouse performance test
- [ ] Verify WebP format used (check Network tab)
- [ ] Test responsive images at all breakpoints
- [ ] Measure LCP and CLS
- [ ] Test lazy loading behavior
- [ ] Verify cache headers

### Dependencies
- Sub-Issue #6: Archive Views with Masonry Grid

### Handoff
→ Performance Engineer → Frontend Developer

---

## Sub-Issue #9: Starry Night Radix 6 Subtheme

**Title:** Create Starry Night-Inspired Radix 6 Bootstrap 5 Subtheme  
**Labels:** `frontend`, `theme`, `design`, `radix`  
**Priority:** Medium  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Create a distinctive Radix 6 Bootstrap 5 subtheme inspired by Van Gogh's "Starry Night" painting. Follow the frontend design principles from `.github/agents/skills/frontend-design/SKILL.md`.

### Requirements
1. **Theme Foundation**
   - Base: Radix 6 (already in composer.json)
   - Framework: Bootstrap 5
   - Machine name: `starry_night`
   - Region configuration for Drupal layouts

2. **Design Inspiration: Starry Night**
   - Color palette: Deep blues, golden yellows, swirling motion
   - Primary: `#172A3A` (night sky)
   - Secondary: `#004B87` (deep blue)
   - Accent: `#FFD700` (golden yellow)
   - Background: `#0F1A2E` (dark blue gradient)

3. **Visual Elements**
   - Swirling gradient backgrounds
   - Animated subtle motion effects
   - Starry particle effects (CSS or lightweight JS)
   - Curved/organic shapes vs rigid boxes

4. **Typography**
   - Display font: Distinctive serif or artistic sans-serif (NOT Inter/Roboto)
   - Body font: Readable sans-serif
   - Scale: Fluid typography (clamp())

5. **Components**
   - Archive grid with atmospheric effects
   - Modal with cinematic feel
   - Navigation with subtle animations

### Technical Tasks
- [ ] Create Radix 6 subtheme: `ddev drush generate radix-subtheme`
- [ ] Configure theme.info.yml
- [ ] Create custom SCSS structure
- [ ] Define color variables (Starry Night palette)
- [ ] Choose and load custom fonts
- [ ] Create gradient/texture backgrounds
- [ ] Implement motion effects (CSS animations)
- [ ] Style archive components
- [ ] Style modal viewer
- [ ] Create template overrides for layouts
- [ ] Build theme assets: `npm run build`
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev yarn test:nightwatch` for UI tests
- [ ] Verify Bootstrap 5 compliance
- [ ] Test responsive design at all breakpoints
- [ ] Check color contrast (WCAG AA)
- [ ] Test animations performance (no jank)
- [ ] Verify theme follows `.github/agents/skills/frontend-design/SKILL.md`

### Dependencies
- Sub-Issue #6: Archive Views with Masonry Grid
- Sub-Issue #7: Modal Viewer with Swiper Navigation

### Design Reference
See: `.github/agents/skills/frontend-design/SKILL.md` for design principles and Van Gogh's "Starry Night" for color/motion inspiration.

### Handoff
→ Frontend Designer → Themer → UX Designer

---

## Sub-Issue #10: GPS Privacy & User Consent

**Title:** GPS Privacy Controls & User Consent Management  
**Labels:** `backend`, `security`, `privacy`, `legal`, `gdpr`  
**Priority:** High  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Implement privacy controls for GPS metadata with user consent and option to strip location data from uploads.

### Requirements
1. **Upload Privacy Options**
   - Checkbox: "Include GPS location data"
   - Default: Checked (opt-out model)
   - Tooltip: Explain GPS usage and visibility
   - Strip GPS if unchecked

2. **Privacy Consent**
   - First upload: Show privacy policy modal
   - Checkbox: "I understand GPS data will be visible to public"
   - Store consent timestamp
   - Link to privacy policy

3. **GPS Display Controls**
   - Public view: Show GPS as approximate location (city-level)
   - Detailed GPS: Only visible to uploader and moderators
   - Option: "Hide my location on this upload"

4. **Privacy Policy Updates**
   - Document GPS data collection
   - Explain metadata extraction
   - Retention and deletion policies
   - User rights (GDPR compliance)

5. **Admin Controls**
   - Bulk strip GPS from content
   - Audit trail for privacy actions

### Technical Tasks
- [ ] Add "include_gps" field to upload form
- [ ] Implement GPS stripping function
- [ ] Create privacy consent entity/field
- [ ] Add first-upload modal with consent
- [ ] Implement approximate GPS display
- [ ] Create privacy policy page
- [ ] Add admin GPS management tools
- [ ] Write tests for privacy functions
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev phpunit --group privacy` tests
- [ ] Test GPS stripping on upload
- [ ] Test consent modal on first upload
- [ ] Verify approximate GPS display
- [ ] Test admin GPS management
- [ ] Review GDPR compliance

### Dependencies
- Sub-Issue #2: Media & Metadata Extraction
- Sub-Issue #4: Bulk Upload & User Interface

### Handoff
→ Security Specialist → Legal Review → Drupal Developer

---

## Sub-Issue #11: User Registration & Roles

**Title:** User Registration, Authentication & Role Management  
**Labels:** `backend`, `security`, `users`, `authentication`  
**Priority:** Medium  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Configure user registration, email verification, and role-based permissions for the archive system.

### Requirements
1. **User Roles**
   - Anonymous: View published content only
   - Authenticated: Basic account
   - Skater: Can upload and manage own content
   - Moderator: Review and publish content
   - Administrator: Full access

2. **Registration Process**
   - Email verification required
   - Account approval: Automatic for email verification
   - Welcome email with upload instructions
   - Profile completion optional

3. **Skater Profile**
   - Display name
   - Bio (optional)
   - Profile picture (optional)
   - Upload statistics
   - Link to user's uploaded content

4. **Permissions Matrix**
   ```
   Anonymous: View published archives
   Authenticated: View published, access own profile
   Skater: Upload, edit own, transition to review
   Moderator: Review all, publish, edit any
   Admin: Full access, manage users
   ```

5. **Email Notifications**
   - Welcome email
   - Upload confirmation
   - Moderation status updates
   - Weekly digest (optional)

### Technical Tasks
- [ ] Configure user registration settings
- [ ] Enable email verification
- [ ] Create "Skater" role with permissions
- [ ] Create "Moderator" role with permissions
- [ ] Configure welcome email template
- [ ] Create user profile view
- [ ] Add "My Uploads" view for users
- [ ] Configure notification emails
- [ ] Write tests for user registration
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev phpunit --group user` tests
- [ ] Test registration process
- [ ] Test email verification
- [ ] Verify role permissions
- [ ] Test all email notifications
- [ ] Test profile page

### Dependencies
None (can be developed independently)

### Handoff
→ Backend Developer → Technical Writer

---

## Sub-Issue #12: Performance Optimization

**Title:** Archive Performance Optimization & Caching  
**Labels:** `performance`, `optimization`, `caching`  
**Priority:** Medium  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Optimize archive performance to meet target metrics: Lighthouse > 90, LCP < 2.5s, FID < 100ms, CLS < 0.1.

### Requirements
1. **Core Web Vitals Targets**
   - LCP (Largest Contentful Paint): < 2.5s
   - FID (First Input Delay): < 100ms
   - CLS (Cumulative Layout Shift): < 0.1
   - Overall Lighthouse Performance: > 90

2. **Caching Strategy**
   - Browser cache: 1 year for images/assets
   - CDN integration ready
   - Drupal Internal Page Cache
   - Drupal Dynamic Page Cache
   - BigPipe for progressive rendering

3. **Database Optimization**
   - Index taxonomy term queries
   - Index view queries
   - Query monitoring and optimization

4. **Frontend Optimization**
   - Minified CSS/JS
   - Critical CSS inline
   - Deferred JavaScript loading
   - Font loading optimization
   - Lazy loading images and videos

5. **OpenLiteSpeed-Specific**
   - LSCache configuration
   - ESI (Edge Side Includes) for dynamic content
   - QUIC protocol support

### Technical Tasks
- [ ] Enable Drupal caching modules
- [ ] Configure cache headers
- [ ] Implement Critical CSS
- [ ] Defer non-critical JavaScript
- [ ] Optimize font loading (preload)
- [ ] Add database indexes
- [ ] Configure BigPipe
- [ ] Minify CSS/JS assets
- [ ] Run Lighthouse audits
- [ ] Document OpenLiteSpeed cache config
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run Lighthouse performance test (all pages)
- [ ] Measure LCP, FID, CLS
- [ ] Test with 100+ images in view
- [ ] Test with slow 3G network throttling
- [ ] Verify cache headers
- [ ] Run `ddev drush cr` and test

### Dependencies
- Sub-Issue #6: Archive Views with Masonry Grid
- Sub-Issue #7: Modal Viewer with Swiper Navigation
- Sub-Issue #8: Responsive Image Styles

### Handoff
→ Performance Engineer → DevOps

---

## Sub-Issue #13: Security Audit & Validation

**Title:** Security Audit for Archive Upload & Display  
**Labels:** `security`, `audit`, `validation`  
**Priority:** High  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Comprehensive security audit of upload, moderation, and display features. Prevent XSS, CSRF, SQL injection, and unauthorized access.

### Requirements
1. **File Upload Security**
   - File type validation (whitelist)
   - File size limits
   - MIME type verification
   - Virus scanning integration point
   - Rate limiting on uploads

2. **Input Validation**
   - YouTube URL validation
   - Taxonomy term validation
   - User input sanitization
   - XSS prevention in all text fields

3. **Access Control**
   - Verify moderation permissions
   - Test unauthorized access attempts
   - CSRF token validation
   - API authentication (if applicable)

4. **Data Protection**
   - SQL injection prevention (parameterized queries)
   - Prepared statements for all database queries
   - Secure file storage (private://)
   - HTTPS enforcement

5. **Security Headers**
   - Content-Security-Policy
   - X-Frame-Options
   - X-Content-Type-Options
   - Strict-Transport-Security

### Technical Tasks
- [ ] Install `file_upload_secure_validator` module (already in composer.json)
- [ ] Configure file validation rules
- [ ] Implement rate limiting for uploads
- [ ] Audit all database queries
- [ ] Audit all user input handling
- [ ] Configure security headers
- [ ] Write security tests (PHPUnit)
- [ ] Perform penetration testing
- [ ] Run OWASP security scan
- [ ] Document security measures
- [ ] Export configuration: `ddev drush cex`

### Validation
- [ ] Run `ddev phpunit --group security` tests
- [ ] Test malicious file upload attempts
- [ ] Test XSS injection attempts
- [ ] Test CSRF attacks
- [ ] Test unauthorized access
- [ ] Run security scanner (OWASP ZAP)
- [ ] Review all validation with Security Specialist

### Dependencies
- Sub-Issue #2: Media & Metadata Extraction
- Sub-Issue #4: Bulk Upload & User Interface
- Sub-Issue #11: User Registration & Roles

### Handoff
→ Security Specialist → Architect

---

## Sub-Issue #14: Documentation & User Guides

**Title:** Archive Documentation & User Guides  
**Labels:** `documentation`, `training`, `guides`  
**Priority:** Medium  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Create comprehensive documentation for users, moderators, and developers.

### Requirements
1. **User Guide: "How to Upload"**
   - Account creation
   - Upload images step-by-step
   - YouTube video instructions
   - Metadata and privacy
   - Tagging with skate dates

2. **Moderator Guide**
   - Moderation workflow
   - Review criteria
   - Approval/rejection process
   - Privacy and GPS handling
   - Bulk actions

3. **Developer Documentation**
   - Architecture overview
   - Module APIs (videojs_media)
   - Metadata extraction service
   - Testing procedures
   - Deployment process

4. **System Requirements**
   - ffprobe installation
   - OpenLiteSpeed configuration
   - DDEV setup for development

5. **CHANGELOG**
   - Version history
   - Feature additions
   - Bug fixes
   - Breaking changes

### Technical Tasks
- [ ] Create `docs/` directory in repository
- [ ] Write USER_GUIDE.md
- [ ] Write MODERATOR_GUIDE.md
- [ ] Write DEVELOPER.md
- [ ] Write SYSTEM_REQUIREMENTS.md
- [ ] Write CHANGELOG.md
- [ ] Add inline code documentation
- [ ] Create video tutorials (optional)
- [ ] Add FAQ section
- [ ] Update main README.md

### Validation
- [ ] Review documentation completeness
- [ ] Test instructions with new user
- [ ] Verify all links work
- [ ] Check formatting and readability
- [ ] Peer review

### Dependencies
All features (document after implementation)

### Handoff
→ Technical Writer → Architect

---

## Sub-Issue #15: OpenLiteSpeed Deployment Configuration

**Title:** Production Deployment for OpenLiteSpeed + MySQL  
**Labels:** `devops`, `deployment`, `production`, `openlitespeed`  
**Priority:** Medium  
**Epic:** Epic: Friday Night Skate Archive Feature  
**Status:** Open

### Description
Configure production environment on Ubuntu 24.04 with OpenLiteSpeed and MySQL 8.0. Set up deployment process using Ansible from `micronugget/ansible` repository.

### Requirements
1. **OpenLiteSpeed Configuration**
   - Virtual host setup
   - PHP 8.2+ configuration
   - Rewrite rules (Drupal-specific)
   - LSCache configuration
   - HTTPS/SSL setup

2. **File System**
   - Private file directory (metadata files)
   - Public file directory (images)
   - ffprobe binary installed
   - Proper permissions

3. **Database**
   - MySQL 8.0 configuration
   - Database optimization
   - Backup schedule
   - Migration scripts

4. **Deployment Process**
   - Git-based deployment
   - Ansible playbooks (`micronugget/ansible`)
   - Zero-downtime deployment
   - Rollback procedure

5. **Environment Variables**
   - Database credentials
   - YouTube API keys
   - Private key storage
   - Mail configuration

6. **Monitoring**
   - Error logging
   - Performance monitoring
   - Uptime monitoring
   - Backup verification

### Technical Tasks
- [ ] Update Ansible playbook for OpenLiteSpeed
- [ ] Configure virtual host for archive
- [ ] Set up LSCache
- [ ] Install ffprobe on server
- [ ] Configure private file directory
- [ ] Set up SSL certificate (Let's Encrypt)
- [ ] Create deployment script
- [ ] Create rollback script
- [ ] Configure cron jobs
- [ ] Set up monitoring
- [ ] Document deployment process
- [ ] Test full deployment

### Validation
- [ ] Deploy to staging environment
- [ ] Test ffprobe on server
- [ ] Test file uploads
- [ ] Test video metadata extraction
- [ ] Verify cache functionality
- [ ] Test rollback procedure
- [ ] Verify SSL/HTTPS
- [ ] Load testing

### Dependencies
All features (deploy after implementation)

### Handoff
→ DevOps → Provisioner/Deployer → Environment Manager

---

## Dependency Graph

```
Foundation:
├─ #1 Taxonomy & Content Architecture
│  ├─ #2 Media & Metadata Extraction
│  │  ├─ #10 GPS Privacy & User Consent
│  │  └─ #13 Security Audit (partial)
│  ├─ #3 Moderation & Content Workflow
│  └─ #5 VideoJS Media Module Testing

Upload Flow:
├─ #4 Bulk Upload & User Interface
│  ├─ Depends on: #1, #2, #3
│  └─ #13 Security Audit (partial)

Display:
├─ #6 Archive Views with Masonry Grid
│  ├─ Depends on: #1, #5
│  ├─ #7 Modal Viewer with Swiper Navigation
│  ├─ #8 Responsive Image Styles
│  ├─ #9 Starry Night Radix 6 Subtheme
│  └─ #12 Performance Optimization

Users:
├─ #11 User Registration & Roles
│  └─ #13 Security Audit (partial)

Final:
├─ #13 Security Audit (after #2, #4, #11)
├─ #14 Documentation (after all features)
└─ #15 OpenLiteSpeed Deployment (after all features)
```

## Development Workflow

1. **Start with Foundation:** Issues #1, #5, #11 (parallel)
2. **Build Core:** Issues #2, #3 (sequential)
3. **Add Privacy:** Issue #10 (after #2)
4. **User Interface:** Issue #4 (after #1, #2, #3)
5. **Display Layer:** Issues #6, #7, #8, #9 (mostly parallel after #1, #5)
6. **Optimization:** Issue #12 (after #6, #7, #8)
7. **Security & Docs:** Issues #13, #14 (after major features)
8. **Production:** Issue #15 (final)

## Testing Requirements

### Backend Issues
Every backend issue must include:
- [ ] PHPUnit tests: `ddev phpunit`
- [ ] Drush validation: `ddev drush cst`
- [ ] Configuration export: `ddev drush cex`
- [ ] Cache clear: `ddev drush cr`

### Frontend Issues
Every frontend issue must include:
- [ ] Nightwatch tests: `ddev yarn test:nightwatch`
- [ ] Bootstrap 5 compliance verification
- [ ] Responsive design testing
- [ ] Accessibility testing

### Integration Testing
- [ ] Full workflow tests (upload → moderation → display)
- [ ] Cross-browser testing
- [ ] Mobile device testing
- [ ] Performance testing (Lighthouse)

---

## Notes for CTO

1. **Approval Required:** Each sub-issue involving new contrib modules includes explicit installation commands for your review.

2. **VideoJS Migration:** Issue #5 specifically addresses testing `videojs_media` and migrating from `videojs_mediablock`.

3. **Starry Night Theme:** Issue #9 references `.github/agents/skills/frontend-design/SKILL.md` for design inspiration.

4. **DDEV Compliance:** All commands use `ddev` prefix as required.

5. **OpenLiteSpeed:** Issue #15 covers production deployment specifics.

6. **Open Source Ready:** Issues include documentation for external contributors.

7. **GitHub + GitLab:** Deployment configuration supports both repositories.

---

**Total Issues:** 15 Sub-Issues + 1 Epic = 16 GitHub Issues
**Estimated Timeline:** 8-12 weeks with 2-3 developers
**Priority Order:** #1 → #2 → #3 → #4 → #5 → #6 → #7 → #8 → #9 → #10 → #11 → #12 → #13 → #14 → #15
