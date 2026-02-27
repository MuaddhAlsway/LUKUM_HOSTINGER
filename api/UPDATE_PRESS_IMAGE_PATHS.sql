-- Update Press Image Paths to Match Public Press Page
-- This script updates the press table to use the correct image paths

USE `lakum-art`;

-- Update press items with correct image paths from public press page
UPDATE press SET cover_image = 'uploads/uploads/press/press_1_1765953905.jpg' WHERE id = 1;
UPDATE press SET cover_image = 'uploads/uploads/press/press_2_1765953905.jpg' WHERE id = 2;
UPDATE press SET cover_image = 'uploads/uploads/press/press_3_1765953905.svg' WHERE id = 3;
UPDATE press SET cover_image = 'uploads/uploads/press/press_5_1765953905.png' WHERE id = 4;
UPDATE press SET cover_image = 'uploads/uploads/press/press_5_1765953905.png' WHERE id = 5;
UPDATE press SET cover_image = 'uploads/uploads/press/press_6_1765953905.svg' WHERE id = 6;
UPDATE press SET cover_image = 'uploads/uploads/press/press_7_1765953905.png' WHERE id = 7;
UPDATE press SET cover_image = 'uploads/uploads/press/press_8_1765953905.jpg' WHERE id = 8;

-- Verify the updates
SELECT id, title, cover_image FROM press ORDER BY id;
