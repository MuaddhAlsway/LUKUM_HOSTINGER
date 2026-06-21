-- ============================================================================
-- COPY & PASTE THESE TWO LINES INTO PhpMyAdmin SQL TAB OR MySQL CLI
-- ============================================================================

ALTER TABLE exhibitions ADD COLUMN `event_video` VARCHAR(500) COMMENT 'Exhibition video URL (YouTube, Vimeo, or direct video URL - optional)' AFTER `cover_image`;

ALTER TABLE exhibitions ADD COLUMN `gallery_images` LONGTEXT COMMENT 'Gallery images as JSON array (e.g., ["path1.jpg", "path2.jpg"])' AFTER `event_video`;
