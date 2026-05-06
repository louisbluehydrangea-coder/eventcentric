<?php
require_once __DIR__ . '/config.php';

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dbFile = __DIR__ . '/database.sqlite';
        $isNew = !file_exists($dbFile);
        
        $dsn = "sqlite:" . $dbFile;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, '', '', $options);
        $pdo->exec('PRAGMA foreign_keys = ON;');
        
        if ($isNew) {
            initializeSQLiteDB($pdo);
        }
    }
    return $pdo;
}

function initializeSQLiteDB(PDO $pdo) {
    $schema = "
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        category VARCHAR(100),
        format VARCHAR(100),
        location VARCHAR(255),
        start_datetime DATETIME NOT NULL,
        end_datetime DATETIME,
        image_path VARCHAR(255),
        is_featured INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS tickets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        event_id INTEGER NOT NULL,
        ticket_name VARCHAR(150) NOT NULL,
        price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        quantity_total INTEGER NOT NULL DEFAULT 0,
        quantity_sold INTEGER NOT NULL DEFAULT 0,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        event_id INTEGER NOT NULL,
        ticket_id INTEGER NOT NULL,
        buyer_name VARCHAR(150) NOT NULL,
        buyer_email VARCHAR(255) NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        total_price DECIMAL(10,2) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id),
        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
    );

    INSERT INTO users (name, email, password_hash) VALUES
    ('John Organizer', 'organizer@demo.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

    INSERT INTO events (user_id, title, description, category, format, location, start_datetime, end_datetime, image_path, is_featured) VALUES
    (1, 'Tech Innovators Summit 2024: The Future of AI', 'Join the brightest minds in technology for a full day of keynotes, workshops, and networking. Explore how AI is transforming industries from healthcare to finance.', 'Business', 'Conference', 'Javits Center, Manhattan, NY', '2024-10-12 09:00:00', '2024-10-12 18:00:00', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDI6Qgmo5rpkZxg1duMK1VoAAuS8LGOr7uvICUe6QqpF676XQWRMbDwrDEqTgI9bFi7N7whcODiOWN1mAd5miqNXfrCK0v5P5LAQMpnoIubg91WIOnJ9J0yCWPMMsaDzUTeEGJhft5lS0ZB6yN3-AeBp9KI0bE8yeHRx-YK4LUg1hYCq_UJHUt8tLJkaI4JvPtliqAhfTUrJFX63ZWe9EV05yOUE99YeJPE4hDXTUDnEQvEj8ZR9-RZ56DXc0swB0ceXn-OwzDjKF4', 1),
    (1, 'Urban Street Food & Craft Beer Festival', 'A celebration of local artisans and craft breweries. Sample over 50 food vendors and 30+ local craft beers in the heart of the city.', 'Food & Drink', 'Festival', 'Central Park South, NY', '2024-10-13 12:00:00', '2024-10-13 22:00:00', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCyKz8KJTJ9iecgBgYkLkdetWQN9HQuTqeDGhK9TCU1KZF6AFvtas-DUq5GngZDiLkliLUYTMVb-9UbgPAe-cLbkykzl_E5uJcIrSryvqZLeW9iIUWIAV9kkIDll_D9aqqg2Sqg4u1S18w3ssG7dmG5-4LAElp6d_xCuoLslPavtDa2Joi8hFYVSKZwOUWHFwe1baMpJf3paIGA_R8B24vLHXx9e1wiLsf42ZuuZpKd8Mg4eTYLmpZkQIpBzOLYtk6SRVq9X6KZ4lc', 1),
    (1, 'Mindful Mornings: Yoga & Meditation Workshop', 'Start your week with intention. Join certified instructors for a 2-hour outdoor yoga and meditation session overlooking the Brooklyn Bridge.', 'Hobbies', 'Workshop', 'Brooklyn Bridge Park, NY', '2024-10-16 06:30:00', '2024-10-16 08:30:00', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCLt08lEyJM5Ct2HA32XLJNMWgBoPhLA8wiuDWD6NhoEbP4pNri4z_0g77R1Tcp4Mu39EnvP_6zZ3IxtF-ucgIGM3l6DPcDbM6z7aHKidQOP7QVhMv9ffoGQXDwzU_A0Cq22hgaA-6lcwbmfHHn2bOyBjXDAVdFZlSOu5-Qk4atuGpVmQ2ATbG23Zp-TJfEPbrDILdb_ueLrnFOeAB3Jryiuu1AoIdr3lU9sAg2yqg2-6xRtVlrDoRVi7NtzAzVaKi75VgU60CiPaA', 0),
    (1, 'Neon Nights: Electronic Underground Party', 'Immerse yourself in a world of pulsating beats and neon light installations. Featuring 3 stages, top DJs, and an immersive art experience.', 'Nightlife', 'Festival', 'Output Club, Brooklyn, NY', '2024-10-18 22:00:00', '2024-10-19 04:00:00', 'https://lh3.googleusercontent.com/aida-public/AB6AXuA6vUXS8pn9OqLtv0jIxmJpbmTn39PBgiRh-Teu_HqPG8vVS8bp0LFUnDqhhaPPtsW1fkq4H4N0Q9om10HWO5nX6VUBBHtVIntgcj1myJp0b_FRiO3Q733vZSQx_RIj0KueH2OqSyT717nYm-MIhaSDGZU05UeU2HfUWMntrnM7BIbo9tshq3Ej2PjZCFJoeCsL-6nDjmeUfdktXyqTWVMnB5uJa07Xz7rJB5vz467VD45kenyoUa0qwl4lHESsl4Bwsg_p2lz9BoI', 0),
    (1, 'Jazz at the Rooftop — Autumn Edition', 'An intimate evening of live jazz under the stars. Enjoy curated cocktails, light bites, and soulful performances with panoramic city views.', 'Music', 'Concert', 'Le Bain Rooftop, Manhattan, NY', '2024-10-20 19:00:00', '2024-10-20 23:00:00', 'https://lh3.googleusercontent.com/aida-public/AB6AXuChjLWXOQ66XWBMNe-RumLp4v5mqMkg2ddoyr_sEXDxIrZcztV6gI8Fxo-1njOKauDv7InUrimmSP-6YbN1sBSXFpoMl2vgjGHU0B3GvXSWLPgy0g_JcLL5Rya-9OeeG5-peuZ_vRHuyK4XnijctLgu3K5kd2oHXb8qEClOaQ88vmRxTDLNstuwUPyQYZVo1B63pC5Khd6gZ1DaT8SYY-pxPxLl3d8MB2c1aOSlv-_6RuFiLM5g8WN4eIqMufPuqCM3AJfO9Hs7PhE', 1),
    (1, 'Startup Founders Pitch Night', 'Ten early-stage startups pitch to a panel of top VCs and angel investors. Open to entrepreneurs, investors, and curious minds.', 'Business', 'Conference', 'WeWork Bryant Park, NY', '2024-10-22 18:00:00', '2024-10-22 21:00:00', 'https://lh3.googleusercontent.com/aida-public/AB6AXuA3Gmp2XcERGVEf2T31bGwW5K6dKFgdOZoogZHUTF-ieUMe1BsZFzrM9dyNQJSuTOqUJgt_KZIvVsIEhzatGUrVm5Y5cWLt4wqQG7SRiDzmJJElxnzvysFy-WS5C2dGL9LHToqMVpsIs5U_AVcebDvJahjtopR5_KWM1PIHWsfWjnE6tcm4qB4__lN3GBSdtjeTUYkDSwbK-LgF8P3vGhArNTPCXWIT-fauwisbZD9VziZyDVtGe0Dv7kFkVoqpPtN4JUaepjY11m0', 0);

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
    ";
    
    $pdo->exec($schema);
}
