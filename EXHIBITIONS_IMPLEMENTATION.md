# Exhibitions Feature Implementation Guide

## Overview
A new **Exhibitions** tab has been added to the LAKUM Admin Dashboard. Exhibitions are managed similarly to Events and displayed as "Past Events" on the spaces.php page.

## What Was Created

### Admin Panel Files

#### 1. **add-exhibition.html** (`/admin/add-exhibition.html`)
- Form to create new exhibitions
- Bilingual support (English & Arabic)
- Cover image upload
- Date/Time fields with multi-day support
- Location selection (Hall 1, Hall 2, Café, Meeting Room, or custom)
- Character counter for descriptions

#### 2. **edit-exhibition.html** (`/admin/edit-exhibition.html`)
- Form to edit existing exhibitions
- Same fields as add-exhibition
- Pre-populates with existing data
- Update functionality

#### 3. **exhibitions.html** (`/admin/exhibitions.html`)
- Management page for all exhibitions
- View, edit, delete exhibitions
- Filter by: All / Upcoming / Past
- Modal view for exhibition details
- Add new exhibition button

### API Endpoints (Backend)

#### 1. **create_exhibitions_table.php**
- Creates the `exhibitions` table in database
- **Run once**: Visit `api/create_exhibitions_table.php` in your browser
- Table structure mirrors the `events` table

#### 2. **add_exhibition.php**
- `POST /api/add_exhibition.php`
- Creates a new exhibition
- Required fields: `title_en`, `exhibition_date`, `location_en`
- Optional: Arabic translations, description, times

#### 3. **get_exhibitions.php**
- `GET /api/get_exhibitions.php?type=all&limit=1000`
- Retrieves all exhibitions
- Returns JSON array of exhibitions

#### 4. **get_exhibition.php**
- `GET /api/get_exhibition.php?id=123`
- Retrieves a single exhibition by ID

#### 5. **edit_exhibition.php**
- `POST /api/edit_exhibition.php`
- Updates an existing exhibition
- Accepts partial updates (only fields to change)

#### 6. **delete_exhibition.php**
- `POST /api/delete_exhibition.php`
- Deletes an exhibition by ID

### Database Changes

**New Table**: `exhibitions`

```sql
CREATE TABLE exhibitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_en VARCHAR(255) NOT NULL,
    title_ar VARCHAR(255),
    description_en LONGTEXT,
    description_ar LONGTEXT,
    location_en VARCHAR(255),
    location_ar VARCHAR(255),
    exhibition_date DATE NOT NULL,
    exhibition_time TIME,
    exhibition_end_time TIME,
    end_date DATE,
    cover_image VARCHAR(500),
    category VARCHAR(50) DEFAULT 'exhibition',
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (exhibition_date),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Frontend Changes

#### 1. **Dashboard Navigation**
- Added "Exhibitions" link to sidebar in:
  - `dashboard.html`
  - `add-event.html`
  - `edit-event.html`
  - `add-exhibition.html`
  - `edit-exhibition.html`

#### 2. **spaces.php Integration**
- Updated `loadPastEvents()` function to fetch both:
  - Events from `api/get_events.php`
  - Exhibitions from `api/get_exhibitions.php`
- Past exhibitions now display in the "Past Events" carousel
- Exhibitions link to event details page (event.php)

## Setup Instructions

### Step 1: Create Database Table
1. Visit: `http://localhost/api/create_exhibitions_table.php`
2. You should see: `{"success": true, "message": "Exhibitions table created successfully"}`

### Step 2: Access Admin Panel
1. Navigate to: `http://localhost/admin/dashboard.html`
2. Look for new "Exhibitions" tab in sidebar
3. Click "Exhibitions" to view management page

### Step 3: Add Your First Exhibition
1. Click "Add Exhibition" button
2. Fill in required fields:
   - Exhibition Title (English) *required*
   - Exhibition Date *required*
   - Location *required*
3. Add optional fields:
   - Arabic title & description
   - Cover image
   - Multi-day exhibition dates
   - Times
4. Click "Create Exhibition"

### Step 4: View Past Exhibitions
1. Go to `spaces.php` in the frontend
2. Scroll to "Past Events" section
3. Past exhibitions now appear alongside past events

## Field Mapping

### Events → Exhibitions
Exhibition fields map as follows:

| Exhibition Field | Purpose |
|---|---|
| `exhibition_date` | Start date of exhibition (like `event_date`) |
| `exhibition_time` | Start time (like `event_time`) |
| `exhibition_end_time` | End time (like `event_end_time`) |
| `end_date` | End date for multi-day exhibitions |
| `title_en` / `title_ar` | Display names |
| `location_en` / `location_ar` | Exhibition location |
| `cover_image` | Featured image |
| `category` | Always set to `'exhibition'` |

## How Past Exhibitions Display

### In Admin Dashboard
- Under "Exhibitions" tab
- Can filter: All / Upcoming / Past
- Shows in a table with date, location, status
- Click any row to see full details in modal

### On Website (spaces.php)
- "Past Events" carousel now includes both events AND exhibitions
- Combined list sorted by date (newest first)
- Limited to 12 items in carousel
- Click to view details

## Features

### Bilingual Support
- ✅ English (required) and Arabic (optional) fields
- ✅ Separate locations for each language
- ✅ Full description support in both languages

### Date Handling
- ✅ Single-day exhibitions: Set date and times
- ✅ Multi-day exhibitions: Toggle "Multi-Day Exhibition", set end date
- ✅ Past dates supported (for historical exhibitions)

### Image Management
- ✅ Cover image upload
- ✅ Automatic image optimization
- ✅ Fallback to default image

### Admin Controls
- ✅ Add new exhibitions
- ✅ Edit existing exhibitions
- ✅ Delete exhibitions
- ✅ View details in modal
- ✅ Filter by status (upcoming/past)

## Testing

### Test 1: Create Exhibition
1. Go to Exhibitions > Add Exhibition
2. Fill fields and save
3. Verify it appears in exhibitions list

### Test 2: Edit Exhibition
1. Click any exhibition
2. Click "Edit Exhibition"
3. Modify fields and save
4. Verify changes appear

### Test 3: Delete Exhibition
1. Click any exhibition
2. Click "Delete Exhibition"
3. Confirm deletion
4. Verify it's removed from list

### Test 4: View on Website
1. Create an exhibition with past date
2. Visit spaces.php
3. Scroll to "Past Events"
4. Verify exhibition appears in carousel

### Test 5: Filter
1. In Exhibitions admin page
2. Click "Upcoming", "Past", or "All"
3. Verify filtering works correctly

## API Response Examples

### Add Exhibition
```json
{
  "success": true,
  "message": "Exhibition created successfully",
  "exhibition_id": 1
}
```

### Get Exhibitions
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title_en": "Contemporary Art Showcase",
      "title_ar": "معرض الفن المعاصر",
      "description_en": "A collection of modern artworks...",
      "description_ar": "مجموعة من الأعمال الفنية الحديثة...",
      "location_en": "Hall 1",
      "location_ar": "القاعة 1",
      "exhibition_date": "2026-03-15",
      "exhibition_time": "10:00:00",
      "exhibition_end_time": "18:00:00",
      "end_date": null,
      "cover_image": "uploads/exhibition_cover.jpg",
      "category": "exhibition",
      "is_featured": 0,
      "created_at": "2026-06-20 19:06:00",
      "updated_at": "2026-06-20 19:06:00"
    }
  ],
  "count": 1
}
```

## Troubleshooting

### Table doesn't exist
- Run: `http://localhost/api/create_exhibitions_table.php`
- Check database permissions

### Images not uploading
- Verify `uploads/` directory exists and is writable
- Check file size limits in PHP config

### Exhibitions not showing on spaces.php
- Verify exhibitions table exists and has data
- Check browser console for fetch errors
- Verify past date (before today)

### Arabic text not displaying
- Check database charset: should be `utf8mb4`
- Verify `dir="rtl"` on Arabic input fields

## File Locations Summary

### Admin Files
```
/admin/
  - add-exhibition.html
  - edit-exhibition.html
  - exhibitions.html
```

### API Files
```
/api/
  - create_exhibitions_table.php
  - add_exhibition.php
  - get_exhibitions.php
  - get_exhibition.php
  - edit_exhibition.php
  - delete_exhibition.php
```

### Modified Files
```
- admin/dashboard.html (added sidebar link)
- admin/add-event.html (added sidebar link)
- admin/edit-event.html (added sidebar link)
- spaces.php (updated loadPastEvents function)
```

## Next Steps

1. ✅ Run `create_exhibitions_table.php` to create the database table
2. ✅ Add exhibitions through the admin panel
3. ✅ View exhibitions in the admin management page
4. ✅ Check spaces.php to see past exhibitions displayed
5. ✅ Test bilingual support (add Arabic titles/descriptions)

## Notes

- Exhibitions use the same styling and structure as events for consistency
- Past exhibitions are determined by comparing exhibition_date with today's date
- Exhibitions appear alongside events in the "Past Events" carousel on spaces.php
- All dates are stored in UTC/server time
- Cover images are required to upload; a default fallback is used if none provided

