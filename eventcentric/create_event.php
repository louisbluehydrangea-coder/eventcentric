<?php
$pageTitle = 'Create Event';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
requireLogin();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $format      = trim($_POST['format'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $start       = trim($_POST['start_datetime'] ?? '');
    $end         = trim($_POST['end_datetime'] ?? '');
    $ticketName  = trim($_POST['ticket_name'] ?? '');
    $ticketPrice = (float)($_POST['ticket_price'] ?? 0);
    $ticketQty   = (int)($_POST['ticket_qty'] ?? 0);

    if (!$title) $errors[] = 'Event title is required.';
    if (!$location) $errors[] = 'Location is required.';
    if (!$start) $errors[] = 'Start date is required.';
    if (!$ticketName) $errors[] = 'At least one ticket type is required.';

    if (empty($errors)) {
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $imagePath = uploadImage($_FILES['image']);
            if (!$imagePath) $errors[] = 'Image upload failed. Use jpg/png/webp under 5MB.';
        }

        if (empty($errors)) {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO events (user_id,title,description,category,format,location,start_datetime,end_datetime,image_path) VALUES(?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $category, $format, $location, $start, $end ?: null, $imagePath]);
            $eventId = $db->lastInsertId();

            $db->prepare("INSERT INTO tickets (event_id,ticket_name,price,quantity_total) VALUES(?,?,?,?)")
               ->execute([$eventId, $ticketName, $ticketPrice, $ticketQty]);

            header('Location: ' . SITE_URL . '/event.php?id=' . $eventId);
            exit;
        }
    }
}

$categories = ['Music','Nightlife','Performing & Visual Arts','Hobbies','Business','Food & Drink','Dating','Holidays','Sports & Fitness','Health & Wellness'];
$formats    = ['Concert','Conference','Festival','Workshop','Networking','Sports','Class','Other'];
require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-28 pb-xl px-4 max-w-[720px] mx-auto min-h-screen">
<h1 class="text-headline-lg font-bold text-on-background mb-2">Create a New Event</h1>
<p class="text-body-md text-slate-500 mb-8">Fill in the details below to publish your event.</p>

<?php if (!empty($errors)): ?>
<div class="flash-error mb-6">
    <ul class="list-disc list-inside space-y-1">
        <?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="space-y-6">

    <!-- Basic Info -->
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)]">
        <h2 class="text-headline-md font-bold text-on-background mb-4">Event Details</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Event Title *</label>
                <input type="text" name="title" required placeholder="e.g. Tech Summit 2024"
                    value="<?= sanitize($_POST['title'] ?? '') ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Description</label>
                <textarea name="description" rows="5" placeholder="Tell attendees what to expect..."
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary resize-none"><?= sanitize($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-bold text-on-surface mb-1">Category</label>
                    <select name="category" class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= sanitize($cat) ?>" <?= ($_POST['category']??'')===$cat?'selected':'' ?>><?= sanitize($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-label-bold text-on-surface mb-1">Format</label>
                    <select name="format" class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary">
                        <option value="">Select format</option>
                        <?php foreach ($formats as $fmt): ?>
                        <option value="<?= sanitize($fmt) ?>" <?= ($_POST['format']??'')===$fmt?'selected':'' ?>><?= sanitize($fmt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Date & Location -->
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)]">
        <h2 class="text-headline-md font-bold text-on-background mb-4">Date, Time & Location</h2>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-bold text-on-surface mb-1">Start Date & Time *</label>
                    <input type="datetime-local" name="start_datetime" required
                        value="<?= sanitize($_POST['start_datetime'] ?? '') ?>"
                        class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-label-bold text-on-surface mb-1">End Date & Time</label>
                    <input type="datetime-local" name="end_datetime"
                        value="<?= sanitize($_POST['end_datetime'] ?? '') ?>"
                        class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
                </div>
            </div>
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Location *</label>
                <input type="text" name="location" required placeholder="Venue name, City, State"
                    value="<?= sanitize($_POST['location'] ?? '') ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
        </div>
    </div>

    <!-- Ticket -->
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)]">
        <h2 class="text-headline-md font-bold text-on-background mb-4">Ticket Type</h2>
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-3 md:col-span-1">
                <label class="block text-label-bold text-on-surface mb-1">Ticket Name *</label>
                <input type="text" name="ticket_name" required placeholder="General Admission"
                    value="<?= sanitize($_POST['ticket_name'] ?? '') ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Price ($)</label>
                <input type="number" name="ticket_price" min="0" step="0.01" placeholder="0.00"
                    value="<?= sanitize($_POST['ticket_price'] ?? '0') ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Quantity</label>
                <input type="number" name="ticket_qty" min="1" placeholder="100"
                    value="<?= sanitize($_POST['ticket_qty'] ?? '') ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
        </div>
    </div>

    <!-- Image Upload -->
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)]">
        <h2 class="text-headline-md font-bold text-on-background mb-4">Event Banner Image</h2>
        <div class="border-2 border-dashed border-outline-variant rounded-xl p-8 text-center hover:border-primary transition-colors">
            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">image</span>
            <p class="text-body-sm text-slate-500 mb-4">Upload a banner image (JPG, PNG, WebP · Max 5MB)</p>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="mx-auto block text-body-sm text-slate-500"/>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex gap-4">
        <button type="submit" class="flex-1 bg-primary-container text-white font-bold py-4 rounded-lg hover:bg-primary transition-colors text-body-md">
            Publish Event
        </button>
        <a href="<?= SITE_URL ?>/dashboard.php" class="px-8 py-4 border border-outline-variant text-on-surface-variant rounded-lg font-semibold hover:border-primary hover:text-primary transition-colors">
            Cancel
        </a>
    </div>

</form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
