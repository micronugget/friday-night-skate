# FNS Archive Module

## Overview
This module provides the foundational content architecture for the Friday Night Skate Archive, including taxonomy vocabulary for skate dates and a content type for managing uploaded media.

## Features

### Taxonomy Vocabulary: Skate Dates
- **Machine name:** `skate_dates`
- **Format:** "YYYY-MM-DD - Location/Description"
- **Purpose:** Organize archive media by skate session date and location
- **Hierarchical:** No
- **Multiple values per node:** No

### Content Type: Archive Media
- **Machine name:** `archive_media`
- **Purpose:** Store and organize images and videos from Friday Night Skate sessions

#### Fields:
1. **Title** - Auto-generated from upload filename
2. **Media** (`field_archive_media`) - Entity reference to media (image or video)
3. **Skate Date** (`field_skate_date`) - Taxonomy reference to skate_dates vocabulary (required)
4. **GPS Coordinates** (`field_gps_coordinates`) - Geofield for location data
5. **Timestamp** (`field_timestamp`) - Date/Time field for when media was captured
6. **Uploader** (`field_uploader`) - User reference, auto-populated with current user
7. **Metadata** (`field_metadata`) - Text field for JSON-formatted EXIF/ffprobe data

#### View Modes:
- **Full** - Display all fields
- **Teaser** - Display media and skate date
- **Thumbnail** - Display media only
- **Modal** - Display media with key metadata (date, GPS, timestamp, uploader)

#### URL Pattern:
`/archive/{skate_date}/{node_id}`

Example: `/archive/2024-01-15-downtown-loop/123`

## Installation

This module is installed automatically when enabled. On installation, it creates:
- The `skate_dates` taxonomy vocabulary
- The `archive_media` content type with all required fields
- View modes for different display contexts
- Pathauto pattern for clean URLs

## Usage

### Creating Archive Media
1. Navigate to Content → Add content → Archive Media
2. Enter a title or let it auto-generate
3. Select or upload media (image or video)
4. Choose the skate date from the dropdown
5. Optionally add GPS coordinates, timestamp, and metadata
6. The uploader field will auto-populate with your user account

### Managing Skate Dates
1. Navigate to Structure → Taxonomy → Skate Dates
2. Add terms in the format: "YYYY-MM-DD - Location/Description"
3. Example: "2024-01-15 - Downtown Loop"

## Technical Details

### Auto-population
The module implements `hook_node_presave()` to automatically populate:
- **Uploader field** with the current user when creating new archive_media nodes

### Update Hook
The module includes `fns_archive_update_10001()` to install all configurations on existing sites.

## Dependencies
- node
- field
- taxonomy
- datetime
- text
- user
- media
- geofield
- pathauto

## Maintainers
- Friday Night Skate development team
