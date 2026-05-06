<?php
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . SITE_URL . '/events.php'); exit; }

$db = getDB();
$stmt = $db->prepare("SELECT e.*, u.name as organizer_name FROM events e JOIN users u ON u.id=e.user_id WHERE e.id=?");
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) { header('Location: ' . SITE_URL . '/events.php'); exit; }

$pageTitle = $event['title'];

$tickets = $db->prepare("SELECT * FROM tickets WHERE event_id=? ORDER BY price ASC");
$tickets->execute([$id]);
$tickets = $tickets->fetchAll();

// Related events
$related = $db->prepare("SELECT e.*, MIN(t.price) as min_price FROM events e LEFT JOIN tickets t ON t.event_id=e.id WHERE e.category=? AND e.id!=? GROUP BY e.id ORDER BY e.start_datetime ASC LIMIT 3");
$related->execute([$event['category'], $id]);
$related = $related->fetchAll();

// Handle ticket order
$orderMsg = '';
$orderError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticketId = (int)$_POST['ticket_id'];
    $qty      = max(1, (int)$_POST['quantity']);
    $name     = trim($_POST['buyer_name'] ?? '');
    $email    = trim($_POST['buyer_email'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $orderError = 'Please enter a valid name and email address.';
    } else {
        $tStmt = $db->prepare("SELECT * FROM tickets WHERE id=? AND event_id=?");
        $tStmt->execute([$ticketId, $id]);
        $ticket = $tStmt->fetch();

        if (!$ticket) {
            $orderError = 'Invalid ticket selection.';
        } elseif (($ticket['quantity_total'] - $ticket['quantity_sold']) < $qty) {
            $orderError = 'Not enough tickets available.';
        } else {
            $total = $ticket['price'] * $qty;
            $db->prepare("INSERT INTO orders (event_id,ticket_id,buyer_name,buyer_email,quantity,total_price) VALUES(?,?,?,?,?,?)")
               ->execute([$id, $ticketId, $name, $email, $qty, $total]);
            $db->prepare("UPDATE tickets SET quantity_sold=quantity_sold+? WHERE id=?")->execute([$qty, $ticketId]);
            $orderMsg = "🎉 You're registered! " . ($total > 0 ? "Total charged: $" . number_format($total,2) : "It's free — enjoy the event!");
        }
    }
}
?>

<main class="pt-20 pb-xl">

<!-- Hero Image -->
<div class="w-full h-72 md:h-96 overflow-hidden relative">
    <img src="<?= getEventImageUrl($event['image_path']) ?>" alt="<?= sanitize($event['title']) ?>" class="w-full h-full object-cover"/>
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
    <?php if ($event['category']): ?>
    <div class="absolute top-6 left-6">
        <span class="bg-white/90 text-orange-700 font-semibold text-label-caps px-4 py-2 rounded-lg"><?= sanitize($event['category']) ?></span>
    </div>
    <?php endif; ?>
</div>

<!-- Content -->
<div class="max-w-[1080px] mx-auto px-4 py-8">
<div class="flex flex-col md:flex-row gap-8">

    <!-- Left: Event Details -->
    <div class="flex-1">
        <h1 class="text-headline-lg font-bold text-on-background mb-4"><?= sanitize($event['title']) ?></h1>

        <!-- Date / Location / Organizer chips -->
        <div class="flex flex-col gap-3 mb-8">
            <div class="flex items-center gap-3 text-body-md text-slate-600">
                <span class="material-symbols-outlined text-primary">calendar_today</span>
                <span><?= formatEventDate($event['start_datetime']) ?>
                    <?php if ($event['end_datetime']): ?>
                    — <?= formatEventDate($event['end_datetime']) ?>
                    <?php endif; ?>
                </span>
            </div>
            <div class="flex items-center gap-3 text-body-md text-slate-600">
                <span class="material-symbols-outlined text-primary">location_on</span>
                <span><?= sanitize($event['location']) ?></span>
            </div>
            <div class="flex items-center gap-3 text-body-md text-slate-600">
                <span class="material-symbols-outlined text-primary">person</span>
                <span>Organized by <strong><?= sanitize($event['organizer_name']) ?></strong></span>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)] mb-8">
            <h2 class="text-headline-md font-bold text-on-background mb-4">About this event</h2>
            <p class="text-body-md text-slate-600 leading-relaxed whitespace-pre-line"><?= sanitize($event['description']) ?></p>
        </div>

        <!-- Tags -->
        <div class="flex gap-2 flex-wrap mb-8">
            <?php if ($event['category']): ?>
            <span class="px-sm py-base bg-surface-container-low text-on-surface-variant text-label-caps rounded-lg border border-outline-variant"><?= sanitize($event['category']) ?></span>
            <?php endif; ?>
            <?php if ($event['format']): ?>
            <span class="px-sm py-base bg-surface-container-low text-on-surface-variant text-label-caps rounded-lg border border-outline-variant"><?= sanitize($event['format']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Ticket Box -->
    <div class="w-full md:w-80 flex-shrink-0">
    <div class="sticky top-24 bg-white rounded-xl shadow-[0px_4px_15px_rgba(0,0,0,0.08)] p-6">
        <h2 class="text-headline-md font-bold text-on-background mb-4">Get Tickets</h2>

        <?php if ($orderMsg): ?>
        <div class="flash-success"><?= sanitize($orderMsg) ?></div>
        <?php endif; ?>
        <?php if ($orderError): ?>
        <div class="flash-error"><?= sanitize($orderError) ?></div>
        <?php endif; ?>

        <?php if (empty($tickets)): ?>
        <p class="text-body-sm text-slate-500">No tickets available yet.</p>
        <?php else: ?>
        <form method="POST">
            <!-- Ticket types -->
            <div class="space-y-3 mb-4">
                <?php foreach ($tickets as $t):
                    $available = $t['quantity_total'] - $t['quantity_sold'];
                ?>
                <label class="flex items-center justify-between p-3 border border-outline-variant rounded-lg cursor-pointer hover:border-primary has-[:checked]:border-primary has-[:checked]:bg-orange-50">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="ticket_id" value="<?= $t['id'] ?>" <?= $available<=0?'disabled':'' ?> class="text-primary" required/>
                        <div>
                            <p class="font-semibold text-body-sm text-on-surface"><?= sanitize($t['ticket_name']) ?></p>
                            <p class="text-label-caps text-slate-400"><?= $available ?> left</p>
                        </div>
                    </div>
                    <span class="font-bold text-primary"><?= $t['price']==0 ? 'Free' : '$'.number_format($t['price'],2) ?></span>
                </label>
                <?php endforeach; ?>
            </div>

            <!-- Quantity -->
            <div class="mb-4">
                <label class="block text-label-bold text-on-surface mb-1">Quantity</label>
                <select name="quantity" class="w-full border border-outline-variant rounded-lg px-4 py-2 text-body-sm outline-none focus:border-primary">
                    <?php for ($i=1;$i<=10;$i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Buyer Info -->
            <div class="mb-4">
                <label class="block text-label-bold text-on-surface mb-1">Your Name</label>
                <input type="text" name="buyer_name" required placeholder="Full name"
                    value="<?= sanitize($_POST['buyer_name'] ?? ($currentUser['name'] ?? '')) ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-2 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div class="mb-6">
                <label class="block text-label-bold text-on-surface mb-1">Email Address</label>
                <input type="email" name="buyer_email" required placeholder="you@email.com"
                    value="<?= sanitize($_POST['buyer_email'] ?? ($currentUser['email'] ?? '')) ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-2 text-body-sm outline-none focus:border-primary"/>
            </div>

            <button type="submit" class="w-full bg-primary-container text-white font-bold py-3 rounded-lg hover:bg-primary transition-colors">
                Register / Get Tickets
            </button>
        </form>
        <?php endif; ?>
    </div>
    </div>

</div>

<!-- Related Events -->
<?php if (!empty($related)): ?>
<section class="mt-12">
    <h2 class="text-headline-md font-bold text-on-background mb-6">More <?= sanitize($event['category']) ?> Events</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
        <?php foreach ($related as $event): ?>
        <?php include __DIR__ . '/includes/event_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

</div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
