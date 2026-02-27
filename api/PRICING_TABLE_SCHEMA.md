# Pricing Table - Actual Schema

## Actual Column Names

The pricing table uses the following columns (NOT what's in DATABASE_SETUP.sql):

```sql
CREATE TABLE pricing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,           -- NOT "name"
    price INT,                              -- Price amount
    price_unit VARCHAR(10) DEFAULT 'SAR',  -- NOT "currency"
    price_sec VARCHAR(100),                 -- NOT "duration"
    vat_note VARCHAR(500),                  -- NOT "features"
    content LONGTEXT,                       -- NOT "description"
    display_order INT DEFAULT 0,            -- Display order
    is_active TINYINT(1) DEFAULT 1,        -- Active status
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Column Mapping

| Actual Column | Wrong Column (in DATABASE_SETUP.sql) | Type | Purpose |
|---|---|---|---|
| title | name | VARCHAR(255) | Pricing plan name |
| price | price | INT | Price amount |
| price_unit | currency | VARCHAR(10) | Currency (SAR, USD, etc.) |
| price_sec | duration | VARCHAR(100) | Duration/Period (per month, per year, etc.) |
| vat_note | features | VARCHAR(500) | VAT note or features |
| content | description | LONGTEXT | Full description |
| display_order | - | INT | Display order |
| is_active | is_active | TINYINT | Active status |
| created_at | created_at | TIMESTAMP | Created timestamp |
| updated_at | updated_at | TIMESTAMP | Updated timestamp |

## API Usage

### Add Pricing
```json
{
    "title": "Monthly Membership",
    "price": 499,
    "price_unit": "SAR",
    "price_sec": "per month",
    "vat_note": "VAT included",
    "content": "Full description here",
    "display_order": 1,
    "is_active": true
}
```

### Edit Pricing
Same fields as add, plus `id`:
```json
{
    "id": 1,
    "title": "Monthly Membership",
    "price": 499,
    ...
}
```

## Database Setup Issue

The DATABASE_SETUP.sql file has incorrect column names:
- Uses `name` instead of `title`
- Uses `currency` instead of `price_unit`
- Uses `duration` instead of `price_sec`
- Uses `features` instead of `vat_note`
- Uses `description` instead of `content`

## Correct SQL

```sql
CREATE TABLE IF NOT EXISTS pricing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    price INT,
    price_unit VARCHAR(10) DEFAULT 'SAR',
    price_sec VARCHAR(100),
    vat_note VARCHAR(500),
    content LONGTEXT,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Files Using Correct Column Names

- ✅ `api/add_pricing.php` - Uses correct columns
- ✅ `api/edit_pricing.php` - Uses correct columns
- ✅ `api/get_pricing.php` - Uses correct columns (if exists)
- ✅ `api/delete_pricing.php` - Uses correct columns (if exists)

## Files with Incorrect Column Names

- ❌ `api/DATABASE_SETUP.sql` - Has wrong column names

## Action Required

If creating a new database, use the correct SQL above, NOT the DATABASE_SETUP.sql file for the pricing table.

If database already exists with correct columns, no action needed.
