# Eventbrite Clone PHP Project Structure

This document outlines the architecture for the responsive Eventbrite clone PHP project. The project is designed with a clean separation of concerns, using common components to avoid code duplication.

## Directory Structure

```text
/eventbrite-clone
├── /assets
│   ├── /css
│   │   └── style.css (Extracted styles from design system)
│   ├── /js
│   │   └── main.js
│   └── /images (Dummy images for events)
├── /components
│   ├── header.php (Navigation, Search Bar)
│   ├── footer.php (Links, Copyright)
│   └── event-card.php (Reusable card component)
├── index.php (Homepage)
├── event-listing.php (Search results/Browse)
└── event-detail.php (Individual event page)
```

## Key Files & Logic

### 1. `components/header.php`
Contains the `<head>` section, links to CSS/JS, and the global navigation bar. It should dynamically handle active states for navigation links.

### 2. `components/footer.php`
Contains the global footer and the closing `</body>` and `</html>` tags.

### 3. `index.php`
- Defines an array of `$featured_events` and `$trending_categories`.
- Includes `header.php`.
- Renders the Hero section and Discovery carousels using loops.
- Includes `footer.php`.

### 4. `event-listing.php`
- Defines an array of `$search_results` with diverse event data.
- Implements the sidebar filter layout.
- Uses a loop to render `components/event-card.php` for each result.

### 5. `event-detail.php`
- Defines a `$single_event` associative array containing all specific details (description, organizer, ticket price).
- Renders the hero image, event information, and the sticky ticket sidebar.

## Dynamic Data Model (Example)

Each PHP file will include a mock data array at the top:

```php
$events = [
    [
        'id' => 1,
        'title' => 'Tech Innovators Summit 2024',
        'date' => 'SAT, OCT 12 • 9:00 AM',
        'location' => 'Javits Center, Manhattan, NY',
        'price' => 'From $299.00',
        'image' => 'assets/images/event1.jpg'
    ],
    // ... more events
];
```

## Development Instructions
1. This project is ready to run on any standard PHP server (XAMPP, MAMP, WAMP).
2. All CSS is bundled in `style.css` to match the exact Design System tokens.
3. Replace the mock data arrays in the future with database queries (MySQL/PDO) for a live production environment.
