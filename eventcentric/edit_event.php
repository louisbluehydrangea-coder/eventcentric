<?php
$pageTitle = 'Edit Event';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
requireLogin();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

// Fetch event & verify ownership
$stmt = $db->prepare("SELECT * FROM events WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$event = $stmt->fetch();
if (!$event) { header('Location: ' . SITE_URL . '/dashboard.php'); exit; }

$ticket = $db->prepare("SELECT * FROM tickets WHERE event_id=? ORDER BY id ASC LIMIT 1");
$ticket->execute([$id]);
$ticket = $ticket->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $format      = trim($_POST['format'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $start       = trim($_POST['start_datetime'] ?? '');
    $end         = trim($_POST['end_datetime'] ?? '');

    if (!$title || !$location || !$start) {
        $errors[] = 'Title, location, and start date are required.';
    }

    if (empty($errors)) {
        $imagePath = $event['image_path'];
        if (!empty($_FILES['image']['name'])) {
            $newPath = uploadImage($_FILES['image']);
            if ($newPath) $imagePath = $newPath;
            else $errors[] = 'Image upload failed.';
        }

        if (empty($errors)) {
            $db->prepare("UPDATE events SET title=?,description=?,category=?,format=?,location=?,start_datetime=?,end_datetime=?,image_path=? WHERE id=?")
               ->execute([$title, $description, $category, $format, $location, $start, $end ?: null, $imagePath, $id]);

            if ($ticket) {
                $db->prepare("UPDATE tickets SET ticket_name=?,price=?,quantity_total=? WHERE id=?")
                   ->execute([trim($_POST['ticket_name']??''), (float)($_POST['ticket_price']??0), (int)($_POST['ticket_qty']??0), $ticket['id']]);
            }

            header('Location: ' . SITE_URL . '/event.php?id=' . $id);
            exit;
        }
    }
    // Re-populate
    $event = array_merge($event, $_POST);
}

$categories = ['Music','Nightlife','Performing & Visual Arts','Hobbies','Business','Food & Drink','Dating','Holidays','Sports & Fitness','Health & Wellness'];
$formats    = ['Concert','Conference','Festival','Workshop','Networking','Sports','Class','Other'];
require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-28 pb-xl px-4 max-w-[720px] mx-auto min-h-screen">
<h1 class="text-headline-lg font-bold text-on-background mb-2">Edit Event</h1>
<p class="text-body-md text-slate-500 mb-8">Update the details for your event.</p>

<?php if (!empty($errors)): ?>
<div class="flash-error mb-6">
    <?php foreach ($errors as $e): ?><p><?= sanitize($e) ?></p><?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="space-y-6">
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)]">
        <h2 class="text-headline-md font-bold mb-4">Event Details</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-label-bold mb-1">Event Title *</label>
                <input type="text" name="title" required value="<?= sanitize($event['title']) ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold mb-1">Description</label>
                <textarea name="description" rows="5" class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary resize-none"><?= sanitize($event['description']) ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-bold mb-1">Category</label>
                    <select name="category" class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= sanitize($cat) ?>" <?= $event['category']===$cat?'selected':'' ?>><?= sanitize($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-label-bold mb-1">Format</label>
                    <select name="format" class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none">
                        <?php foreach ($formats as $fmt): ?>
                        <option value="<?= sanitize($fmt) ?>" <?= $event['format']===$fmt?'selected':'' ?>><?= sanitize($fmt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)]">
        <h2 class="text-headline-md font-bold mb-4">Date, Time & Location</h2>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-bold mb-1">Start *</label>
                    <input type="datetime-local" name="start_datetime" required
                        value="<?= date('Y-m-d\TH:i', strtotime($event['start_datetime'])) ?>"
                        class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-label-bold mb-1">End</label>
                    <input type="datetime-local" name="end_datetime"
                        value="<?= $event['end_datetime'] ? date('Y-m-d\TH:i', strtotime($event['end_datetime'])) : '' ?>"
                        class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
                </div>
            </div>
            <div>
                <label class="block text-label-bold mb-1">Location *</label>
                <input type="text" name="location" required value="<?= sanitize($event['location']) ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
        </div>
    </div>

    <?php if ($ticket): ?>
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)]">
        <h2 class="text-headline-md font-bold mb-4">Ticket</h2>
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-3 md:col-span-1">
                <label class="block text-label-bold mb-1">Name</label>
                <input type="text" name="ticket_name" value="<?= sanitize($ticket['ticket_name']) ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold mb-1">Price ($)</label>
                <input type="number" name="ticket_price" min="0" step="0.01" value="<?= $ticket['price'] ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold mb-1">Total Qty</label>
                <input type="number" name="ticket_qty" min="1" value="<?= $ticket['quantity_total'] ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)]">
        <h2 class="text-headline-md font-bold mb-4">Banner Image</h2>
        <?php if ($event['image_path']): ?>
        <img src="<?= getEventImageUrl($event['image_path']) ?>" class="w-full h-40 object-cover rounded-lg mb-4"/>
        <?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="text-body-sm text-slate-500"/>
    </div>

    <div class="flex gap-4">
        <button type="submit" class="flex-1 bg-primary-container text-white font-bold py-4 rounded-lg hover:bg-primary transition-colors">Save Changes</button>
        <a href="<?= SITE_URL ?>/dashboard.php" class="px-8 py-4 border border-outline-variant text-on-surface-variant rounded-lg font-semibold hover:border-primary hover:text-primary transition-colors">Cancel</a>
    </div>
</form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
