<?php
function formatPrice(float $price): string {
    return $price == 0 ? 'Free' : 'From $' . number_format($price, 2);
}

function formatEventDate(string $datetime): string {
    $ts = strtotime($datetime);
    return strtoupper(date('D, M j • g:i A', $ts));
}

function sanitize(string $val): string {
    return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
}

function getMinTicketPrice(int $eventId): float {
    $db = getDB();
    $stmt = $db->prepare("SELECT MIN(price) as min_price FROM tickets WHERE event_id = ?");
    $stmt->execute([$eventId]);
    $row = $stmt->fetch();
    return (float)($row['min_price'] ?? 0);
}

function uploadImage(array $file): ?string {
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    $allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($file['type'], $allowedMimes)) return null;
    if ($file['size'] > MAX_FILE_SIZE) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) return null;

    $filename = uniqid('evt_', true) . '.' . $ext;
    
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }
    
    $dest = UPLOAD_DIR . $filename;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $filename;
    }
    return null;
}

function getEventImageUrl(?string $imagePath): string {
    if ($imagePath) {
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        if (file_exists(UPLOAD_DIR . $imagePath)) {
            return UPLOAD_URL . $imagePath;
        }
    }
    
    // Dynamic fallbacks to make the site look beautiful even without uploaded images
    static $fallbacks = [
        'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80', // Concert/Party
        'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?w=800&q=80', // Networking/Business
        'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&q=80', // Food
        'https://images.unsplash.com/photo-1542840410-3092f99611a3?w=800&q=80', // Yoga/Health
    ];
    
    // Pick a deterministic image based on some global state or just random
    return $fallbacks[array_rand($fallbacks)];
}
