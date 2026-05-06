# Event Centric — PHP + XAMPP Setup Guide

## Prerequisites
- XAMPP installed (Apache + MySQL running)
- PHP 8.0+

## Setup Steps

### 1. Copy project files
Place the entire `eventcentric` folder in your XAMPP `htdocs` directory:
```
C:/xampp/htdocs/eventcentric/   (Windows)
/Applications/XAMPP/htdocs/eventcentric/   (Mac)
```

### 2. Create the database
1. Open your browser → go to `http://localhost/phpmyadmin`
2. Click **Import** in the top menu
3. Choose `schema.sql` from this project folder
4. Click **Go** — this creates the database, tables, and seed data

### 3. Configure database credentials
Open `config.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventcentric');
define('DB_USER', 'root');
define('DB_PASS', '');  // Leave empty for default XAMPP
```

### 4. Set uploads folder permissions
Make sure the `/uploads/` folder is writable. On Mac/Linux:
```bash
chmod 755 /Applications/XAMPP/htdocs/eventcentric/uploads/
```

### 5. Visit the site
Open: `http://localhost/eventcentric/`

---

## Demo Login
- **Email:** organizer@demo.com
- **Password:** password123

---

## File Structure
```
eventcentric/
├── config.php          # DB credentials & constants
├── db.php              # PDO connection
├── schema.sql          # Database + seed data
├── index.php           # Homepage
├── events.php          # Browse & filter events
├── event.php           # Event detail + ticket booking
├── create_event.php    # Create new event (auth required)
├── edit_event.php      # Edit event (auth required)
├── dashboard.php       # Organizer dashboard (auth required)
├── login.php           # Login page
├── register.php        # Registration page
├── logout.php          # Session logout
├── uploads/            # User-uploaded event images
└── includes/
    ├── header.php      # Shared navbar + <head>
    ├── footer.php      # Shared footer + </html>
    ├── auth.php        # Login/register/session helpers
    ├── helpers.php     # Formatting & upload utilities
    └── event_card.php  # Reusable event card component
```

## Pages Overview
| URL | Description |
|-----|-------------|
| `/index.php` | Homepage with featured & upcoming events |
| `/events.php` | Browse all events with filters |
| `/event.php?id=X` | Event detail + ticket registration |
| `/create_event.php` | Create a new event |
| `/edit_event.php?id=X` | Edit an existing event |
| `/dashboard.php` | Organizer stats & event management |
| `/login.php` | Log in |
| `/register.php` | Register new account |
