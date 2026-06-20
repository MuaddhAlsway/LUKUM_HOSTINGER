# Database Files for Exhibitions - Hostinger MySQL

## Files Created

### 1. **EXHIBITIONS_TABLE.sql** (in `/api/` folder)
- Complete SQL script to create exhibitions table
- Can be imported directly into phpMyAdmin
- Includes sample data commented out
- File: `/api/EXHIBITIONS_TABLE.sql`

### 2. **HOSTINGER_SETUP_GUIDE.md** (in root folder)
- Step-by-step guide for Hostinger cPanel
- Multiple methods (cPanel, phpMyAdmin, SSH)
- Troubleshooting section
- File: `/HOSTINGER_SETUP_GUIDE.md`

### 3. **EXHIBITIONS_MYSQL_CREATE.txt** (in root folder)
- Quick copy-paste SQL code
- Easy reference for busy setup
- File: `/EXHIBITIONS_MYSQL_CREATE.txt`

---

## Quick Setup for Hostinger

### 3-Minute Setup:

1. **Go to Hostinger hPanel**
   - Login at https://hpanel.hostinger.com
   - Select your domain

2. **Open phpMyAdmin**
   - Click "MySQL Databases"
   - Click "Manage" next to your database
   - This opens phpMyAdmin

3. **Create Table**
   - Click "SQL" tab
   - Copy this code:
   ```sql
   CREATE TABLE IF NOT EXISTS `exhibitions` (
       `id` INT AUTO_INCREMENT PRIMARY KEY,
       `title_en` VARCHAR(255) NOT NULL,
       `title_ar` VARCHAR(255),
       `description_en` LONGTEXT,
       `description_ar` LONGTEXT,
       `location_en` VARCHAR(255),
       `location_ar` VARCHAR(255),
       `exhibition_date` DATE NOT NULL,
       `exhibition_time` TIME,
       `exhibition_end_time` TIME,
       `end_date` DATE,
       `cover_image` VARCHAR(500),
       `category` VARCHAR(50) DEFAULT 'exhibition',
       `is_featured` TINYINT(1) DEFAULT 0,
       `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       INDEX `idx_date` (`exhibition_date`),
       INDEX `idx_category` (`category`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```
   - Paste into the SQL box
   - Click "Go"
   - Done! ✓

---

## Database Table Structure

### exhibitions table

| Column | Type | Default | Notes |
|--------|------|---------|-------|
| id | INT | AUTO_INCREMENT | Primary key |
| title_en | VARCHAR(255) | NULL | Exhibition title (English) - REQUIRED |
| title_ar | VARCHAR(255) | NULL | Exhibition title (Arabic) - Optional |
| description_en | LONGTEXT | NULL | Description (English) |
| description_ar | LONGTEXT | NULL | Description (Arabic) |
| location_en | VARCHAR(255) | NULL | Location (English) - REQUIRED |
| location_ar | VARCHAR(255) | NULL | Location (Arabic) |
| exhibition_date | DATE | NULL | Start date - REQUIRED |
| exhibition_time | TIME | NULL | Start time |
| exhibition_end_time | TIME | NULL | End time |
| end_date | DATE | NULL | End date (multi-day) |
| cover_image | VARCHAR(500) | NULL | Image file path |
| category | VARCHAR(50) | 'exhibition' | Category type |
| is_featured | TINYINT(1) | 0 | Featured flag |
| created_at | TIMESTAMP | CURRENT_TIMESTAMP | Created timestamp |
| updated_at | TIMESTAMP | CURRENT_TIMESTAMP | Updated timestamp |

---

## After Creating the Table

### 1. Test the Dashboard
- Visit: `https://yourdomain.com/admin/dashboard.html`
- Click "Exhibitions" in sidebar
- Should show "No exhibitions found" (which is correct)

### 2. Add Your First Exhibition
- Click "Add Exhibition"
- Fill in:
  - Title (English) ✓ required
  - Date ✓ required
  - Location ✓ required
  - Optional: Arabic text, image, times
- Click "Create Exhibition"

### 3. Verify on Frontend
- Visit: `https://yourdomain.com/spaces.php`
- Scroll to "Past Events"
- Your exhibition should appear if date is in the past

---

## Troubleshooting

### Issue: "Table already exists"
✓ This is normal - just means it was already created
✓ You can safely proceed to add exhibitions

### Issue: "Access denied for user"
1. Check you're logged into the correct database
2. Verify username/password in `/api/config.php`
3. Make sure database name matches Hostinger setting

### Issue: "Syntax error in SQL"
1. Copy code exactly as shown (no extra characters)
2. Paste into fresh text editor first
3. Check for special characters or formatting

### Issue: Can't find phpMyAdmin
1. In hPanel, go to "MySQL Databases"
2. Find your database
3. Click the "Manage" button (often a wrench icon)

### Issue: Exhibitions don't show on dashboard
1. Hard refresh page (Ctrl+Shift+R)
2. Clear browser cache
3. Check browser console for errors (F12)
4. Verify database connection in `/api/config.php`

---

## Database Config File

Make sure `/api/config.php` has correct settings:

```php
define('DB_HOST', 'your_mysql_host');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database_name');
```

Get these from Hostinger hPanel under MySQL Databases section.

---

## File Locations

| File | Location | Purpose |
|------|----------|---------|
| SQL Script | `/api/EXHIBITIONS_TABLE.sql` | Import into phpMyAdmin |
| Quick SQL | `/EXHIBITIONS_MYSQL_CREATE.txt` | Copy-paste code |
| Setup Guide | `/HOSTINGER_SETUP_GUIDE.md` | Full setup instructions |
| Config | `/api/config.php` | Database credentials |
| Dashboard | `/admin/dashboard.html` | Manage exhibitions |
| Add Form | `/admin/add-exhibition.html` | Create exhibitions |
| Edit Form | `/admin/edit-exhibition.html` | Edit exhibitions |

---

## API Endpoints (After Table Created)

These will work once table is created:

```
POST   /api/add_exhibition.php           - Create exhibition
GET    /api/get_exhibitions.php          - Fetch all exhibitions
GET    /api/get_exhibition.php?id=123    - Fetch one exhibition
POST   /api/edit_exhibition.php          - Update exhibition
POST   /api/delete_exhibition.php        - Delete exhibition
```

---

## Support Resources

- **Hostinger Help**: https://support.hostinger.com
- **MySQL Docs**: https://dev.mysql.com/doc/
- **phpMyAdmin Help**: https://www.phpmyadmin.net/docs/

---

## Next Steps

1. ✅ Create exhibitions table using SQL code above
2. ✅ Verify table appears in phpMyAdmin
3. ✅ Test dashboard at `/admin/dashboard.html`
4. ✅ Add your first exhibition
5. ✅ Check `/spaces.php` to see it displayed

**You're all set! 🎉**

