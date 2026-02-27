# LAKUM Admin Panel - Consolidated

## Overview
All admin pages have been consolidated into LUKUM/admin/ with consistent styling, navigation, and functionality.

## Pages Created

### Core Pages
- **login.html** - Admin login page
- **dashboard.html** - Main dashboard with statistics
- **settings.html** - Admin settings and links

### List Pages
- **events.html** - List all events
- **blogs.html** - List all blogs
- **press.html** - List all press releases
- **pricing.html** - List all pricing

### Add Pages
- **add-event.html** - Add new event with cover image and gallery
- **add-blog.html** - Add new blog with TinyMCE editor
- **add-press.html** - Add new press release
- **add-pricing.html** - Add new pricing

### Edit Pages
- **edit-event.html** - Edit existing event
- **edit-blog.html** - Edit existing blog
- **edit-press.html** - Edit existing press
- **edit-pricing.html** - Edit existing pricing

### Special Pages
- **legal-pages.html** - Manage Terms & Privacy Policy

## Features

### Consistent Design
- Cream background (#f6f6eb)
- Dark sidebar with navigation
- Minimalist styling
- Responsive layout

### Navigation
- Unified sidebar on all pages
- Language toggle (English/Arabic)
- Active page highlighting
- Quick access to all sections

### Functionality
- Image upload with preview
- Gallery management
- Form validation
- RTL support
- Mobile responsive

## File Structure
```
LUKUM/admin/
├── login.html
├── dashboard.html
├── events.html
├── blogs.html
├── press.html
├── pricing.html
├── legal-pages.html
├── settings.html
├── add-event.html
├── add-blog.html
├── add-press.html
├── add-pricing.html
├── edit-event.html
├── edit-blog.html
├── edit-press.html
├── edit-pricing.html
├── admin-style.css
├── event-form-style.css
├── admin.js
├── event-form.js
└── form-reset.js
```

## Path Updates
All relative paths have been updated to work from LUKUM/admin/:
- Assets: `../../assest/`
- API: `../../api/`
- Main site: `../../index.html`

## API Endpoints
Forms connect to these API endpoints:
- `../../api/add_event.php`
- `../../api/edit_event.php`
- `../../api/get_events.php`
- `../../api/add_blog.php`
- `../../api/edit_blog.php`
- `../../api/get_blogs.php`
- `../../api/add_press.php`
- `../../api/edit_press.php`
- `../../api/get_press.php`
- `../../api/add_pricing.php`
- `../../api/edit_pricing.php`
- `../../api/get_pricing.php`

## Styling
- **admin-style.css** - Main admin panel styles
- **event-form-style.css** - Form-specific styles
- Consistent color scheme throughout
- Responsive grid layouts

## JavaScript
- **admin.js** - Core admin functionality
- **event-form.js** - Event form handling
- **form-reset.js** - Form caching prevention

## RTL Support
All pages support RTL (Arabic) with:
- Language toggle in sidebar
- Proper text direction
- Responsive layout adjustments

## Responsive Design
- Mobile-first approach
- Tablet optimized
- Desktop full-featured
- Touch-friendly controls

## Next Steps
1. Connect API endpoints to backend
2. Implement authentication
3. Add data loading from database
4. Configure image upload paths
5. Test all forms and validations
