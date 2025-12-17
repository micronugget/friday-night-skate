# Skating Video Uploader

## Overview

The Skating Video Uploader module extends the VideoJS Mediablock module to collect GPS and timecode metadata from videos and upload them to YouTube with user consent. This module is designed specifically for skating clubs to easily share videos while preserving important metadata.

## Features

- Collects and preserves GPS location data from videos
- Extracts and stores timecode metadata
- Provides a user consent mechanism for metadata collection and YouTube uploads
- Integrates with the VideoJS Mediablock module
- Uploads videos to YouTube using the YouTube Data API v3
- Preserves metadata before YouTube scrubs it away
- Implements OAuth 2.0 authentication for secure YouTube uploads

## Requirements

- Drupal 10.3 or higher
- VideoJS Mediablock module (2.3.x branch)
- PHP FFmpeg library
- Google API Client library
- YouTube Data API v3 enabled in Google Cloud Console

## Installation

1. Install the module using Composer:
   ```
   composer require drupal/skating_video_uploader
   ```

2. Enable the module:
   ```
   drush en skating_video_uploader
   ```

3. Configure the YouTube API credentials at `/admin/config/media/skating-video-uploader`.

## Configuration

### YouTube API Setup

1. Create a project in the [Google Cloud Console](https://console.cloud.google.com/)
2. Enable the YouTube Data API v3
3. Create OAuth 2.0 credentials (Client ID and Client Secret)
4. Configure the authorized redirect URI to point to your site's callback URL:
   ```
   https://your-site.com/admin/config/media/skating-video-uploader/youtube/oauth-callback
   ```
5. Enter these credentials in the module's settings form
6. Click "Authenticate with YouTube" to complete the OAuth flow

### Metadata Consent Text

You can customize the consent text that users will see when uploading videos. This text should clearly explain:

- What metadata is being collected
- How it will be used
- That the video will be uploaded to YouTube
- Any privacy implications

## Usage

1. Create a new VideoJS Mediablock at `/block/add/videojs_mediablock`
2. Upload a video file
3. Check the consent checkbox to allow metadata collection and YouTube upload
4. Save the block
5. The module will automatically extract metadata and upload the video to YouTube
6. The YouTube video ID will be stored in the database along with the extracted metadata

## Troubleshooting

- If videos fail to upload, check the YouTube API credentials and ensure the OAuth flow has been completed
- If metadata extraction fails, ensure the FFmpeg library is properly installed
- Check the Drupal logs for detailed error messages

## Credits

Developed for the Friday Night Skate Club by [Your Name/Organization].
