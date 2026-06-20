# Hostinger Database Setup for Exhibitions

## How to Create Exhibitions Table on Hostinger

### Method 1: Using Hostinger cPanel (EASIEST)

#### Step 1: Access Your Hostinger cPanel
1. Go to [hPanel.hostinger.com](https://hpanel.hostinger.com)
2. Log in with your credentials
3. Click on your domain

#### Step 2: Open MySQL Databases
1. Find and click **"MySQL Databases"** or **"Databases"**
2. Select your database name (usually something like `u12345_lakum`)

#### Step 3: Open phpMyAdmin
1. Click the **"Manage"** button next to your database
2. This opens phpMyAdmin

#### Step 4: Create the Table
1. Look for the **"SQL"** tab at the top
2. Click it
3. You'll see an empty SQL query box
4. Copy and paste this SQL code:

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

5. Click **"Go"** button (bottom right)

#### Step 5: Verify Success
You should see: `Query executed successfully`

---

### Method 2: Using phpMyAdmin Import

#### Step 1: Download the SQL File
- File location: `/api/EXHIBITIONS_TABLE.sql`
- Or copy the SQL code from Method 1

#### Step 2: Access phpMyAdmin
1. Go to Hostinger hPanel
2. Click "MySQL Databases" 
3. Click "Manage" on your database
4. Click **"Import"** tab

#### Step 3: Import File
1. Click **"Choose File"**
2. Select `EXHIBITIONS_TABLE.sql`
3. Click **"Go"**

#### Step 4: Wait for Success
Message: `Import has been successfully finished`

---

### Method 3: Using SSH (Advanced)

If you have SSH access:

```bash
mysql -h your_host -u your_username -p your_database < EXHIBITIONS_TABLE.sql
```

Replace:
- `your_host` - your MySQL host
- `your_username` - your MySQL username
- `your_database` - your database name

---

## Table Structure Details

### Fields Explanation:

| Field | Type | Purpose |
|-------|------|---------|
| `id` | INT | Unique exhibition ID (auto-increment) |
| `title_en` | VARCHAR(255) | Exhibition title in English (required) |
| `title_ar` | VARCHAR(255) | Exhibition title in Arabic (optional) |
| `description_en` | LONGTEXT | Long description in English |
| `description_ar` | LONGTEXT | Long description in Arabic |
| `location_en` | VARCHAR(255) | Location in English |
| `location_ar` | VARCHAR(255) | Location in Arabic |
| `exhibition_date` | DATE | Start date |
| `exhibition_time` | TIME | Start time |
| `exhibition_end_time` | TIME | End time (same day) |
| `end_date` | DATE | End date (for multi-day) |
| `cover_image` | VARCHAR(500) | Path to cover image |
| `category` | VARCHAR(50) | Type (always 'exhibition') |
| `is_featured` | TINYINT(1) | Featured flag (0 or 1) |
| `created_at` | TIMESTAMP | Auto-created timestamp |
| `updated_at` | TIMESTAMP | Auto-updated timestamp |

---

## Verify Table Was Created

After creating the table, verify it by:

1. In phpMyAdmin, click your database name
2. You should see `exhibitions` listed in the tables
3. Click on `exhibitions` to see the structure

Or run this SQL query:

```sql
DESCRIBE exhibitions;
```

You should see all the columns listed.

---

## Now What?

After creating the table:

1. Go to your website: `https://yourdomain.com/admin/dashboard.html`
2. Click **"Exhibitions"** in the sidebar
3. Click **"Add Exhibition"**
4. Fill in the form and save
5. Your exhibitions will now display!

---

## Troubleshooting

### "Table already exists"
- This is fine! Just means the table was already created
- You can proceed to add exhibitions

### "Access denied"
- Make sure you're logged in to the correct database
- Check your username/password

### "Syntax error"
- Copy the SQL code exactly as shown
- Make sure there are no extra characters
- Try pasting into a text editor first to remove formatting

### "Database connection failed"
- Check your database credentials in `/api/config.php`
- Make sure the database name matches what's in Hostinger
- Verify username and password

---

## Quick Reference

**SQL File Location:** `/api/EXHIBITIONS_TABLE.sql`

**Database Credentials File:** `/api/config.php`

**Admin Dashboard:** `/admin/dashboard.html` → Click "Exhibitions" tab

**Add Exhibition:** `/admin/add-exhibition.html`

**Edit Exhibition:** `/admin/edit-exhibition.html`

---

## Support

If you encounter issues:

1. ✅ Make sure XAMPP/Hostinger MySQL is running
2. ✅ Verify database credentials in `/api/config.php`
3. ✅ Check Hostinger phpMyAdmin shows the table created
4. ✅ Refresh the admin dashboard

For Hostinger support: [support.hostinger.com](https://support.hostinger.com)

