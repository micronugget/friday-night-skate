#!/bin/bash

# Script to remove VideoJS Mediablock configuration entities
# This resolves the "already exist in active configuration" error when reinstalling the module

echo "Removing VideoJS Mediablock configuration entities..."

# Remove field configurations
echo "Removing field configurations..."
ddev drush config:delete field.field.media.videojs_audio.field_media_videojs_audio_file -y
ddev drush config:delete field.field.media.videojs_video.field_media_videojs_video_file -y

# Remove field storage configurations
echo "Removing field storage configurations..."
ddev drush config:delete field.storage.media.field_media_videojs_audio_file -y
ddev drush config:delete field.storage.media.field_media_videojs_video_file -y

# Remove media type configurations
echo "Removing media type configurations..."
ddev drush config:delete media.type.videojs_audio -y
ddev drush config:delete media.type.videojs_video -y

echo "Configuration cleanup complete!"
echo "You can now reinstall the VideoJS Mediablock module."
