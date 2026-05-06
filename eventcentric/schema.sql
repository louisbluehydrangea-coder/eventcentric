CREATE DATABASE IF NOT EXISTS eventcentric CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventcentric;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    format VARCHAR(100),
    location VARCHAR(255),
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME,
    image_path VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    ticket_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    quantity_total INT NOT NULL DEFAULT 0,
    quantity_sold INT NOT NULL DEFAULT 0,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    ticket_id INT NOT NULL,
    buyer_name VARCHAR(150) NOT NULL,
    buyer_email VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (ticket_id) REFERENCES tickets(id)
);

-- Seed organizer user (password: password123)
INSERT INTO users (name, email, password_hash) VALUES
('John Organizer', 'organizer@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Seed events
INSERT INTO events (user_id, title, description, category, format, location, start_datetime, end_datetime, image_path, is_featured) VALUES
(1, 'Tech Innovators Summit 2024: The Future of AI', 'Join the brightest minds in technology for a full day of keynotes, workshops, and networking. Explore how AI is transforming industries from healthcare to finance.', 'Business', 'Conference', 'Javits Center, Manhattan, NY', '2024-10-12 09:00:00', '2024-10-12 18:00:00', NULL, 1),
(1, 'Urban Street Food & Craft Beer Festival', 'A celebration of local artisans and craft breweries. Sample over 50 food vendors and 30+ local craft beers in the heart of the city.', 'Food & Drink', 'Festival', 'Central Park South, NY', '2024-10-13 12:00:00', '2024-10-13 22:00:00', NULL, 1),
(1, 'Mindful Mornings: Yoga & Meditation Workshop', 'Start your week with intention. Join certified instructors for a 2-hour outdoor yoga and meditation session overlooking the Brooklyn Bridge.', 'Hobbies', 'Workshop', 'Brooklyn Bridge Park, NY', '2024-10-16 06:30:00', '2024-10-16 08:30:00', NULL, 0),
(1, 'Neon Nights: Electronic Underground Party', 'Immerse yourself in a world of pulsating beats and neon light installations. Featuring 3 stages, top DJs, and an immersive art experience.', 'Nightlife', 'Festival', 'Output Club, Brooklyn, NY', '2024-10-18 22:00:00', '2024-10-19 04:00:00', NULL, 0),
(1, 'Jazz at the Rooftop — Autumn Edition', 'An intimate evening of live jazz under the stars. Enjoy curated cocktails, light bites, and soulful performances with panoramic city views.', 'Music', 'Concert', 'Le Bain Rooftop, Manhattan, NY', '2024-10-20 19:00:00', '2024-10-20 23:00:00', NULL, 1),
(1, 'Startup Founders Pitch Night', 'Ten early-stage startups pitch to a panel of top VCs and angel investors. Open to entrepreneurs, investors, and curious minds.', 'Business', 'Conference', 'WeWork Bryant Park, NY', '2024-10-22 18:00:00', '2024-10-22 21:00:00', NULL, 0);

-- Seed tickets
INSERT INTO tickets (event_id, ticket_name, price, quantity_total, quantity_sold) VALUES
(1, 'General Admission', 299.00, 500, 120),
(1, 'VIP Pass', 599.00, 50, 10),
(2, 'General Entry', 0.00, 2000, 850),
(3, 'Standard', 25.00, 100, 45),
(4, 'Early Bird', 45.00, 300, 200),
(4, 'General', 65.00, 500, 100),
(5, 'General Admission', 75.00, 150, 90),
(5, 'VIP Table (2 guests)', 200.00, 20, 8),
(6, 'General', 0.00, 300, 180);
