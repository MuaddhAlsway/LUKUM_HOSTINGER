-- Add Arabic translation columns to events table
ALTER TABLE events ADD COLUMN title_ar VARCHAR(255) DEFAULT NULL AFTER title;
ALTER TABLE events ADD COLUMN description_ar LONGTEXT DEFAULT NULL AFTER description;
ALTER TABLE events ADD COLUMN location_ar VARCHAR(255) DEFAULT NULL AFTER location;

-- Add Arabic translation columns to blogs table
ALTER TABLE blogs ADD COLUMN title_ar VARCHAR(255) DEFAULT NULL AFTER title;
ALTER TABLE blogs ADD COLUMN content_ar LONGTEXT DEFAULT NULL AFTER content;
ALTER TABLE blogs ADD COLUMN excerpt_ar VARCHAR(500) DEFAULT NULL AFTER excerpt;

-- Add Arabic translation columns to press table
ALTER TABLE press ADD COLUMN title_ar VARCHAR(255) DEFAULT NULL AFTER title;
ALTER TABLE press ADD COLUMN content_ar LONGTEXT DEFAULT NULL AFTER content;
ALTER TABLE press ADD COLUMN excerpt_ar VARCHAR(500) DEFAULT NULL AFTER excerpt;

-- Add Arabic translation columns to pricing table
ALTER TABLE pricing ADD COLUMN name_ar VARCHAR(255) DEFAULT NULL AFTER name;
ALTER TABLE pricing ADD COLUMN description_ar LONGTEXT DEFAULT NULL AFTER description;
ALTER TABLE pricing ADD COLUMN features_ar LONGTEXT DEFAULT NULL AFTER features;
