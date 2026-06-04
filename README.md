# LAKUM Artspace — Official Website

> *Where Encounters Shape Culture*

The official website and admin management platform for **LAKUM Artspace**, Riyadh's cultural destination for contemporary art exhibitions, creative workshops, and cultural events.

**Live site:** [lakumartspace.com](https://lakumartspace.com)

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Features](#features)
- [Database](#database)
- [Authentication & Security](#authentication--security)
- [Email System](#email-system)
- [Bilingual Support](#bilingual-support)
- [Deployment](#deployment)
- [Local Development](#local-development)
- [Environment Variables](#environment-variables)

---

## Overview

LAKUM Artspace is a full-stack PHP web application with:

- A **public-facing website** for visitors to discover events, exhibitions, blogs, press releases, spaces, and contact the team
- A **private admin panel** (`/admin/`) for content managers to create and manage all site content
- A **REST API layer** (`/api/`) powering both the public site and admin panel
- Full **bilingual support** (English + Arabic with RTL layout)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP (no framework) |
| Database | MySQL via `mysqli` |
| Web Server | Apache (`.htaccess` for URL rewriting, caching, security) |
| Frontend | Vanilla JavaScript, HTML5, CSS3 |
| Icons | Remix Icons v4.5 (CDN) |
| Fonts | Greta Arabic (custom OTF, self-hosted) |
| Email | Raw SMTP over PHP socket — Gmail SMTP (`smtp.gmail.com:587`) |
| Auth | PHP sessions + bcrypt (`password_hash` / `password_verify`) |
| CI/CD | GitHub Actions + FTP deploy (`SamKirkland/FTP-Deploy-Action`) |
| Local Dev | XAMPP |

---

## Project Structure

```
/
├── index.php                  # Homepage
├── about.php                  # About page
├── exhibitions.php            # Exhibitions listing
├── event.php                  # Single event detail (slug-based URLs)
├── blog.php                   # Blog listing
├── blogPageDetails.php        # Single blog detail
├── press.php                  # Press releases listing
├── pressPageDetails.php       # Single press detail
├── spaces.php                 # Rentable spaces
├── contact.php                # Contact form
├── calendar.php               # Events calendar
├── shop.php                   # Shop page
├── terms.php                  # Terms & Conditions
├── privacy.php                # Privacy Policy
├── lakum-header-unified.php   # Shared navigation header
├── config.php                 # Config loader
├── config.local.php           # Local credentials (NOT in Git)
│
├── api/                       # REST API endpoints (260+ PHP files)
│   ├── config.php             # DB singleton, CORS, session, JWT
│   ├── auth.php               # Login / logout / session check
│   ├── submit-contact.php     # Contact form handler + Gmail SMTP
│   ├── forgot-password.php    # Password reset — sends email link
│   ├── reset-password.php     # Password reset — validates token & updates
│   ├── get_events.php         # Fetch events (public)
│   ├── get_blogs.php          # Fetch blogs (public)
│   ├── get_press.php          # Fetch press releases (public)
│   ├── get_pricing.php        # Fetch pricing plans (public)
│   ├── add_*.php              # Create endpoints
│   ├── edit_*.php             # Update endpoints
│   ├── delete_*.php           # Delete endpoints
│   ├── upload.php             # File/image upload
│   ├── search.php             # Site-wide search
│   └── *.sql                  # Database schema & seed files
│
├── admin/                     # Admin panel (HTML + Vanilla JS)
│   ├── login.html             # Login page
│   ├── dashboard.html         # Dashboard
│   ├── events.html            # Events management
│   ├── blogs.html             # Blogs management
│   ├── press.html             # Press management
│   ├── pricing.html           # Pricing management
│   ├── messages.html          # Contact form inbox
│   ├── hero-manager.html      # Per-page hero image & text editor
│   ├── site-settings.html     # Global site settings
│   ├── legal-pages.html       # Terms & Privacy editor
│   ├── forgot_password.html   # Forgot password page
│   ├── reset-password.html    # Set new password page
│   └── change-password.html   # Change password (while logged in)
│
├── includes/                  # Shared PHP partials
│   ├── footer.php
│   ├── stylesheets.php
│   ├── scripts.php
│   ├── hero-settings.php
│   └── site-settings.php
│
├── lang/                      # Translation system
│   ├── loader.php             # t() helper function
│   ├── url-router.php
│   ├── en/                    # English JSON files
│   └── ar/                    # Arabic JSON files
│
├── assest/                    # Static assets
│   ├── fonts/                 # Greta Arabic OTF files
│   ├── logo/                  # LAKUM logo parts
│   ├── blog-uploads/          # Uploaded blog images
│   ├── gallery/               # Event gallery images
│   └── *.js / *.css           # Scripts and styles
│
├── heroImage/                 # Hero section images
├── uploads/                   # Covers + event images
├── eventfile/                 # Event source files
├── fonts/                     # Root-level font files
├── .github/workflows/         # GitHub Actions CI/CD
│   ├── deploy-hostinger.yml   # Full deploy workflow
│   └── deploy-simple.yml      # FTP-only deploy
└── logs/                      # Server error logs
```

---

## Features

### Public Website

| Page | Description |
|---|---|
| Homepage | Featured event hero, upcoming/past exhibitions grid, CTA sections |
| Exhibitions | All events listing with filter by category |
| Event Detail | Full event page with gallery, date/time, description (EN/AR) |
| Blog | Article listing with cover images and excerpts |
| Blog Detail | Full article with related posts |
| Press | Press releases listing |
| Press Detail | Full press release page |
| Spaces | Venue hire with pricing plans |
| Contact | Form → saves to DB + email to admin + confirmation to visitor |
| Calendar | Events calendar view |
| Terms / Privacy | DB-managed legal pages (editable from admin) |

**Global features:**
- Language switcher (EN ↔ AR) with RTL layout
- Floating action button (phone, WhatsApp, email)
- Page loader animation
- Clean SEO URLs (`/blog/my-post`, `/event-slug`)
- `hreflang` alternate links
- Gzip compression, HTTP caching, security headers via `.htaccess`

### Admin Panel (`/admin/`)

| Section | Capabilities |
|---|---|
| Events | Create, edit, delete events + manage gallery images |
| Blogs | Create, edit, delete posts with cover image upload |
| Press | Create, edit, delete press releases |
| Pricing | Create, edit, delete space pricing plans |
| Hero Manager | Set hero image, title, subtitle per page |
| Site Settings | Edit CTA text, contact details, social links |
| Legal Pages | Edit Terms & Privacy content in EN and AR |
| Messages | View and respond to contact form submissions |
| Password Reset | Email-based reset flow with 1-hour expiry tokens |

---

## Database

**Database:** `u812122863_lakum_artspace` (Hostinger) / `lakum_artspace` (local)

| Table | Purpose |
|---|---|
| `events` | Art events and exhibitions |
| `event_gallery` | Per-event images |
| `event_translations` | EN/AR translations for events |
| `blogs` | Blog posts |
| `blog_translations` | EN/AR translations for blogs |
| `press` | Press releases |
| `press_translations` | EN/AR translations for press |
| `pricing` | Space rental pricing plans |
| `pricing_translations` | EN/AR translations for pricing |
| `admins` | Admin accounts (bcrypt passwords) |
| `contact_messages` | Contact form submissions |
| `legal_page_translations` | Terms & Privacy content |
| `password_resets` | Password reset tokens (1-hour expiry, single-use) |

All tables: `InnoDB`, `utf8mb4_unicode_ci`, cascading deletes on foreign keys.

---

## Authentication & Security

### Login Flow

1. Admin submits email + password at `admin/login.html`
2. POST to `api/auth.php?action=login`
3. Authorized emails checked: `info@lakumartspace.com`, `muaddhalsway@gmail.com`
4. Admin DB record fetched (by email, fallback to first row)
5. Password verified with `password_verify()` (bcrypt)
6. PHP session started with `admin_id`, `admin_email`, `admin_role`

### Password Reset Flow

1. Go to `admin/forgot_password.html`
2. Enter `info@lakumartspace.com` or `muaddhalsway@gmail.com`
3. Reset link emailed via Gmail SMTP (valid for **1 hour**, single-use)
4. Click link → `admin/reset-password.html?token=...`
5. Enter new password → `api/reset-password.php` validates token and updates the admin password

### Session Security

- Session cookie: `httponly: true`, `samesite: Lax`, `secure: true` (on HTTPS)
- Session timeout: 3600 seconds (1 hour)
- JWT secret defined for future API token use

### Admin Roles

| Role | Permissions |
|---|---|
| `super_admin` | All permissions |
| `admin` | View, Create, Edit, Delete |
| `editor` | View, Create, Edit |
| `viewer` | View only |

---

## Email System

Emails are sent via **raw PHP socket** directly to Gmail's SMTP server — no external library required.

- **Host:** `smtp.gmail.com:587` (STARTTLS)
- **From:** `info@lakumartspace.com`
- **App password:** Stored in `api/forgot-password.php` and `api/submit-contact.php`

### Emails sent

| Trigger | Recipients | Content |
|---|---|---|
| Contact form submitted | Admin (`info@lakumartspace.com`) | Visitor name, email, phone, subject, message |
| Contact form submitted | Visitor (their email) | Confirmation receipt |
| Password reset requested | The email used (`info@...` or Gmail) | Reset link (expires 1 hour) |

---

## Bilingual Support

The site supports **English** and **Arabic** with full RTL layout.

- Language stored in `localStorage` (`lakum_language`)
- PHP helper: `t('key', 'fallback')` for static strings
- JSON translation files in `lang/en/` and `lang/ar/`
- Dynamic content (events, blogs, press, pricing) stored with separate `_translations` tables in the DB
- RTL styles in `rtl.css` and `greta-arabic.css`
- Arabic font: Greta Arabic (self-hosted OTF)

---

## Deployment

### Hosting

**Production:** Hostinger shared hosting → `public_html/`

### CI/CD — GitHub Actions

Two workflows in `.github/workflows/`:

**`deploy-hostinger.yml`** (full)
- Triggers on push to `main`, `master`, or `deployment-hostinger`
- Steps: PHP lint → FTP incremental deploy → DB migration ping

**`deploy-simple.yml`** (minimal)
- FTP deploy only

### Required GitHub Secrets

| Secret | Description |
|---|---|
| `FTP_HOST` | Hostinger FTP hostname |
| `FTP_USER` | FTP username |
| `FTP_PASSWORD` | FTP password |
| `DEPLOYMENT_KEY` | Optional deployment trigger key |

### Files Excluded from Deploy

`.git`, `.env`, `logs/`, `uploads/`, `eventfile/`, `assest/blog-uploads/`, `assest/press-uploads/`, `*.md`

---

## Local Development

### Requirements

- XAMPP (Apache + MySQL + PHP 7.4+)
- Git

### Setup

```bash
# 1. Clone the repo
git clone https://github.com/MuaddhAlsway/LUKUM_HOSTINGER.git
cd LUKUM_HOSTINGER

# 2. Checkout the deployment branch
git checkout deployment-hostinger

# 3. Place in XAMPP htdocs
# Copy to: C:/xampp/htdocs/LUKUM/

# 4. Create the database
# Import: api/DATABASE_SETUP.sql or api/COMPLETE_DATABASE_SETUP.sql

# 5. Create config.local.php (see Environment Variables below)

# 6. Start XAMPP Apache + MySQL

# 7. Visit: http://localhost/LUKUM/
# Admin:   http://localhost/LUKUM/admin/login.html
```

---

## Environment Variables

Create `config.local.php` in the project root (this file is gitignored):

```php
<?php
return [
    'db' => [
        'host'     => 'localhost',          // Hostinger: srv2073.hstgr.io
        'user'     => 'root',               // Hostinger: u812122863_neama
        'password' => '',                   // Your DB password
        'database' => 'lakum_artspace',     // Hostinger: u812122863_lakum_artspace
        'port'     => 3306,
        'charset'  => 'utf8mb4',
    ],
    'site' => [
        'url'      => 'http://localhost/LUKUM',  // Production: https://lakumartspace.com
        'timezone' => 'Asia/Riyadh',
    ],
    'security' => [
        'jwt_secret'          => 'your-secret-key-here',
        'session_timeout'     => 3600,
        'max_upload_size'     => 5242880,
        'allowed_extensions'  => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'],
    ],
    'logging' => [
        'error_log_path' => __DIR__ . '/logs/error.log',
        'display_errors' => true,   // false in production
        'log_errors'     => true,
    ],
    'uploads' => [
        'directory' => __DIR__ . '/uploads/',
    ],
];
```

---

## Contact

**LAKUM Artspace**
Al Urubah Branch Rd, Umm Al Hamam Al Gharbi, Riyadh 12328, Saudi Arabia

- Email: [info@lakumartspace.com](mailto:info@lakumartspace.com)
- Phone: +966 920 012 083
- Instagram: [@lakumartspace](https://www.instagram.com/lakumartspace/)
- Twitter/X: [@Lakumartspace](https://x.com/Lakumartspace)

---

*© 2026 LAKUM Artspace. All rights reserved.*
