-- Add bilingual columns to events table
-- This migration adds Arabic translation support to the events table

USE `lakum_artspace`;

-- Add bilingual columns to events table
ALTER TABLE events ADD COLUMN IF NOT EXISTS title_en VARCHAR(255);
ALTER TABLE events ADD COLUMN IF NOT EXISTS title_ar VARCHAR(255);
ALTER TABLE events ADD COLUMN IF NOT EXISTS description_en LONGTEXT;
ALTER TABLE events ADD COLUMN IF NOT EXISTS description_ar LONGTEXT;
ALTER TABLE events ADD COLUMN IF NOT EXISTS location_en VARCHAR(255);
ALTER TABLE events ADD COLUMN IF NOT EXISTS location_ar VARCHAR(255);

-- Migrate existing data to English columns (since current data is in English)
UPDATE events SET title_en = title WHERE title_en IS NULL;
UPDATE events SET description_en = description WHERE description_en IS NULL;
UPDATE events SET location_en = location WHERE location_en IS NULL;

-- Add bilingual columns to blogs table
ALTER TABLE blogs ADD COLUMN IF NOT EXISTS title_en VARCHAR(255);
ALTER TABLE blogs ADD COLUMN IF NOT EXISTS title_ar VARCHAR(255);
ALTER TABLE blogs ADD COLUMN IF NOT EXISTS content_en LONGTEXT;
ALTER TABLE blogs ADD COLUMN IF NOT EXISTS content_ar LONGTEXT;
ALTER TABLE blogs ADD COLUMN IF NOT EXISTS excerpt_en VARCHAR(500);
ALTER TABLE blogs ADD COLUMN IF NOT EXISTS excerpt_ar VARCHAR(500);

-- Migrate existing blog data to English columns
UPDATE blogs SET title_en = title WHERE title_en IS NULL;
UPDATE blogs SET content_en = content WHERE content_en IS NULL;
UPDATE blogs SET excerpt_en = excerpt WHERE excerpt_en IS NULL;

-- Add bilingual columns to press table
ALTER TABLE press ADD COLUMN IF NOT EXISTS title_en VARCHAR(255);
ALTER TABLE press ADD COLUMN IF NOT EXISTS title_ar VARCHAR(255);
ALTER TABLE press ADD COLUMN IF NOT EXISTS content_en LONGTEXT;
ALTER TABLE press ADD COLUMN IF NOT EXISTS content_ar LONGTEXT;
ALTER TABLE press ADD COLUMN IF NOT EXISTS excerpt_en VARCHAR(500);
ALTER TABLE press ADD COLUMN IF NOT EXISTS excerpt_ar VARCHAR(500);

-- Migrate existing press data to English columns
UPDATE press SET title_en = title WHERE title_en IS NULL;
UPDATE press SET content_en = content WHERE content_en IS NULL;
UPDATE press SET excerpt_en = excerpt WHERE excerpt_en IS NULL;

-- Add bilingual columns to pricing table
ALTER TABLE pricing ADD COLUMN IF NOT EXISTS name_en VARCHAR(255);
ALTER TABLE pricing ADD COLUMN IF NOT EXISTS name_ar VARCHAR(255);
ALTER TABLE pricing ADD COLUMN IF NOT EXISTS description_en LONGTEXT;
ALTER TABLE pricing ADD COLUMN IF NOT EXISTS description_ar LONGTEXT;
ALTER TABLE pricing ADD COLUMN IF NOT EXISTS features_en LONGTEXT;
ALTER TABLE pricing ADD COLUMN IF NOT EXISTS features_ar LONGTEXT;

-- Migrate existing pricing data to English columns
UPDATE pricing SET name_en = name WHERE name_en IS NULL;
UPDATE pricing SET description_en = description WHERE description_en IS NULL;
UPDATE pricing SET features_en = features WHERE features_en IS NULL;
