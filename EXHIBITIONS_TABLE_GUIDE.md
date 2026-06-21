# EXHIBITIONS TABLE - MySQL Schema & Guide

## Quick Reference

### Main Table: `exhibitions`

| Column | Type | Purpose | Example |
|--------|------|---------|---------|
| `id` | INT | Unique ID | 1, 2, 3... |
| `title_en` | VARCHAR(255) | English title | "Contemporary Art Showcase" |
| `title_ar` | VARCHAR(255) | Arabic title | "معرض الفن المعاصر" |
| `description_en` | LONGTEXT | English description | Full details... |
| `description_ar` | LONGTEXT | Arabic description | شرح مفصل... |
| `location_en` | VARCHAR(255) | English location | "Hall 1" |
| `location_ar` | VARCHAR(255) | Arabic location | "القاعة 1" |
| `exhibition_date` | DATE | Start date | 2026-03-15 |
| `exhibition_time` | TIME | Start time | 10:00:00 |
| `exhibition_end_time` | TIME | End time | 18:00:00 |
| `end_date` | DATE | End date (multi-day) | 2026-03-31 |
| `cover_image` | VARCHAR(500) | Cover image path | assest/img-4.png |
| `gallery_folder` | VARCHAR(255) | Gallery folder | exhibitions/img-gallery/ |
| `category` | VARCHAR(50) | Type | exhibition, workshop, event |
| `status` | ENUM | Status | upcoming, ongoing, past, archived |
| `is_featured` | TINYINT(1) | Featured? | 0 (no) or 1 (yes) |
| `views_count` | INT | Page views | 1234 |
| `created_at` | TIMESTAMP | Created date | 2026-01-15 10:30:45 |
| `updated_at` | TIMESTAMP | Last updated | 2026-01-20 14:22:10 |
| `deleted_at` | TIMESTAMP NULL | Soft delete | NULL or 2026-02-01 09:00:00 |

---

## Setup Instructions

### Option 1: Using phpMyAdmin (Easiest)

1. Open phpMyAdmin
2. Select your database (`lakum_artspace`)
3. Click **"Import"** tab
4. Upload file: `api/CREATE_EXHIBITIONS_TABLE.sql`
5. Click **"Go"**

### Option 2: Hostinger cPanel

1. Login to Hostinger cPanel
2. Go to **MySQL Databases** → phpMyAdmin
3. Click **"SQL"** tab
4. Paste entire script from `api/CREATE_EXHIBITIONS_TABLE.sql`
5. Click **"Go"**

### Option 3: Command Line (SSH)

```bash
mysql -u username -p database_name < api/CREATE_EXHIBITIONS_TABLE.sql
```

---

## Database Tables

### 1. `exhibitions` (Main Table)
Stores exhibition information with bilingual support.

**Key Indexes:**
- `idx_exhibition_date` - Speed up date queries
- `idx_category` - Speed up category filtering
- `idx_status` - Speed up status filtering
- `idx_is_featured` - Speed up featured queries
- `ft_title_en`, `ft_title_ar` - Full-text search

### 2. `exhibition_images` (Gallery)
Stores images for each exhibition.

**Links to:** `exhibitions` table via `exhibition_id` (Foreign Key)

**Cascade Delete:** When exhibition deleted, all images auto-deleted

### 3. `exhibition_categories` (Lookup)
Pre-defined categories:
- `exhibition` - Art exhibitions
- `workshop` - Hands-on workshops
- `event` - Events and gatherings
- `lecture` - Educational talks
- `performance` - Live performances

---

## Common SQL Queries

### Get Upcoming Exhibitions
```sql
SELECT * FROM exhibitions 
WHERE status = 'upcoming' 
ORDER BY exhibition_date ASC;
```

### Get Featured Exhibitions (Homepage)
```sql
SELECT * FROM exhibitions 
WHERE is_featured = 1 AND status IN ('upcoming', 'ongoing')
LIMIT 5;
```

### Search Exhibitions
```sql
SELECT * FROM exhibitions 
WHERE MATCH(title_en, description_en) AGAINST('art' IN BOOLEAN MODE);
```

### Get Single Exhibition with Images
```sql
SELECT e.*, COUNT(i.id) as image_count
FROM exhibitions e
LEFT JOIN exhibition_images i ON e.id = i.exhibition_id
WHERE e.id = 1
GROUP BY e.id;
```

### Get Exhibition Gallery
```sql
SELECT * FROM exhibition_images 
WHERE exhibition_id = 1 
ORDER BY sort_order ASC;
```

### Count by Category
```sql
SELECT category, COUNT(*) as total 
FROM exhibitions 
GROUP BY category;
```

### Get Past Exhibitions (Archive)
```sql
SELECT * FROM exhibitions 
WHERE status = 'past' 
ORDER BY exhibition_date DESC;
```

---

## PHP Usage Examples

### Insert New Exhibition
```php
$mysqli = new mysqli("localhost", "user", "password", "lakum_artspace");

$title_en = "Contemporary Art Showcase";
$title_ar = "معرض الفن المعاصر";
$description_en = "A stunning collection of contemporary artworks...";
$description_ar = "مجموعة رائعة من الأعمال الفنية...";
$location_en = "Hall 1";
$location_ar = "القاعة 1";
$exhibition_date = "2026-03-15";
$exhibition_time = "10:00:00";
$exhibition_end_time = "18:00:00";
$cover_image = "assest/img-4.png";
$category = "exhibition";

$sql = "INSERT INTO exhibitions 
        (title_en, title_ar, description_en, description_ar, 
         location_en, location_ar, exhibition_date, exhibition_time, 
         exhibition_end_time, cover_image, category) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sssssssssss", 
    $title_en, $title_ar, $description_en, $description_ar,
    $location_en, $location_ar, $exhibition_date, $exhibition_time,
    $exhibition_end_time, $cover_image, $category);
$stmt->execute();
```

### Get Upcoming Exhibitions
```php
$result = $mysqli->query("
    SELECT id, title_en, title_ar, description_en, description_ar,
           exhibition_date, exhibition_time, cover_image, category
    FROM exhibitions 
    WHERE status = 'upcoming'
    ORDER BY exhibition_date ASC
");

while ($exhibition = $result->fetch_assoc()) {
    echo $exhibition['title_en'];
    echo $exhibition['exhibition_date'];
}
```

### Update Exhibition Status
```php
$new_status = "ongoing";
$exhibition_id = 1;

$mysqli->query("UPDATE exhibitions SET status = ? WHERE id = ?");
$stmt = $mysqli->prepare("UPDATE exhibitions SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $exhibition_id);
$stmt->execute();
```

### Soft Delete (Mark as Deleted, Don't Remove)
```php
$exhibition_id = 1;
$mysqli->query("UPDATE exhibitions SET deleted_at = NOW() WHERE id = $exhibition_id");
```

### Get With Images
```php
$result = $mysqli->query("
    SELECT e.*, 
           GROUP_CONCAT(i.image_path ORDER BY i.sort_order SEPARATOR ',') as images
    FROM exhibitions e
    LEFT JOIN exhibition_images i ON e.id = i.exhibition_id
    WHERE e.id = 1
    GROUP BY e.id
");
$exhibition = $result->fetch_assoc();
```

---

## Bilingual Support

### Display in English
```php
$title = $exhibition['title_en'];
$description = $exhibition['description_en'];
$location = $exhibition['location_en'];
```

### Display in Arabic
```php
$title = $exhibition['title_ar'];
$description = $exhibition['description_ar'];
$location = $exhibition['location_ar'];
```

---

## Status Values

| Status | Meaning | Use Case |
|--------|---------|----------|
| `upcoming` | Future exhibition | Show in "upcoming" section |
| `ongoing` | Currently happening | Show as "live now" |
| `past` | Completed | Show in archive |
| `archived` | Old, not relevant | Hide from public |

---

## Best Practices

✅ **DO:**
- Use `title_en` and `title_ar` for bilingual content
- Use `status` field to control visibility
- Use `is_featured` for homepage highlights
- Use `exhibition_date` for sorting
- Use `deleted_at` for soft deletes (don't permanently delete)

❌ **DON'T:**
- Permanently delete exhibitions (use `deleted_at` instead)
- Store image files in the database (store paths only)
- Mix language content in one field
- Use hardcoded dates (use `exhibition_date` from DB)

---

## Backup Your Database

Before running the script, backup your database:

```bash
# Backup
mysqldump -u username -p database_name > backup.sql

# Restore if needed
mysql -u username -p database_name < backup.sql
```

---

## Support

**File Location:** `api/CREATE_EXHIBITIONS_TABLE.sql`

**Related Files:**
- `api/exhibitions.php` - PHP API for exhibitions
- `exhibitions.php` - Frontend exhibitions page
- `admin/edit-exhibition.html` - Admin edit form

---

## Troubleshooting

### "Table already exists"
The script uses `CREATE TABLE IF NOT EXISTS`, so it won't recreate existing tables.
To reset: `DROP TABLE exhibitions; DROP TABLE exhibition_images;` then re-run.

### "Foreign key constraint error"
Make sure `exhibitions` table exists before creating `exhibition_images`.

### "Charset error"
The script uses `utf8mb4_unicode_ci` for Arabic support. Make sure your MySQL supports it.

---

**Last Updated:** June 2026
**Schema Version:** 1.0
**Compatible:** MySQL 5.7+, MySQL 8.0+
