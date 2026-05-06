<?php
$pageTitle = 'My Dashboard';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
requireLogin();
require_once __DIR__ . '/includes/header.php';

$db = getDB();
$userId = $_SESSION['user_id'];

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event'])) {
    $delId = (int)$_POST['delete_event'];
    $db->prepare("DELETE FROM events WHERE id=? AND user_id=?")->execute([$delId, $userId]);
    header('Location: ' . SITE_URL . '/dashboard.php?deleted=1');
    exit;
}

// Fetch user's events with ticket stats
$events = $db->prepare("
    SELECT e.*,
        COALESCE(SUM(t.quantity_sold), 0) as total_sold,
        COALESCE(SUM(t.quantity_total), 0) as total_capacity,
        COALESCE(SUM(t.quantity_sold * t.price), 0) as total_revenue,
        MIN(t.price) as min_price
    FROM events e
    LEFT JOIN tickets t ON t.event_id = e.id
    WHERE e.user_id = ?
    GROUP BY e.id
    ORDER BY e.start_datetime DESC
");
$events->execute([$userId]);
$events = $events->fetchAll();

// Summary stats
$totalEvents   = count($events);
$totalSold     = array_sum(array_column($events, 'total_sold'));
$totalRevenue  = array_sum(array_column($events, 'total_revenue'));
?>

<main class="pt-28 pb-xl px-4 max-w-[1080px] mx-auto min-h-screen">

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-headline-lg font-bold text-on-background">My Dashboard</h1>
        <p class="text-body-md text-slate-500">Welcome back, <?= sanitize($currentUser['name']) ?></p>
    </div>
    <a href="<?= SITE_URL ?>/create_event.php" class="bg-primary-container text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary transition-colors flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">add</span> Create Event
    </a>
</div>

<?php if (!empty($_GET['deleted'])): ?>
<div class="flash-success mb-6">Event deleted successfully.</div>
<?php endif; ?>

<!-- Stats -->
<div class="grid grid-cols-3 gap-gutter mb-10">
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)] text-center">
        <p class="text-label-caps text-slate-400 mb-1">TOTAL EVENTS</p>
        <p class="text-headline-lg font-black text-on-background"><?= $totalEvents ?></p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)] text-center">
        <p class="text-label-caps text-slate-400 mb-1">TICKETS SOLD</p>
        <p class="text-headline-lg font-black text-primary"><?= number_format($totalSold) ?></p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-[0px_4px_15px_rgba(0,0,0,0.05)] text-center">
        <p class="text-label-caps text-slate-400 mb-1">TOTAL REVENUE</p>
        <p class="text-headline-lg font-black text-on-background">$<?= number_format($totalRevenue, 2) ?></p>
    </div>
</div>

<!-- Events Table -->
<div class="bg-white rounded-xl shadow-[0px_4px_15px_rgba(0,0,0,0.05)] overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="text-headline-md font-bold text-on-background">Your Events</h2>
    </div>
    <?php if (empty($events)): ?>
    <div class="py-16 text-center">
        <span class="material-symbols-outlined text-6xl text-slate-200">event</span>
        <p class="text-body-md text-slate-500 mt-3">You haven't created any events yet.</p>
        <a href="<?= SITE_URL ?>/create_event.php" class="inline-block mt-4 text-primary font-semibold hover:underline">Create your first event →</a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="w-full text-body-sm">
        <thead class="bg-surface-container-low text-on-surface-variant text-label-caps">
            <tr>
                <th class="px-6 py-3 text-left">EVENT</th>
                <th class="px-6 py-3 text-left">DATE</th>
                <th class="px-6 py-3 text-center">TICKETS SOLD</th>
                <th class="px-6 py-3 text-right">REVENUE</th>
                <th class="px-6 py-3 text-center">ACTIONS</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($events as $ev): ?>
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="<?= getEventImageUrl($ev['image_path']) ?>" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0"/>
                        <div>
                            <p class="font-semibold text-on-surface line-clamp-1"><?= sanitize($ev['title']) ?></p>
                            <p class="text-label-caps text-slate-400"><?= sanitize($ev['category'] ?: '—') ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-on-surface-variant"><?= date('M j, Y', strtotime($ev['start_datetime'])) ?></td>
                <td class="px-6 py-4 text-center">
                    <span class="font-semibold text-on-surface"><?= number_format($ev['total_sold']) ?></span>
                    <span class="text-slate-400"> / <?= number_format($ev['total_capacity']) ?></span>
                </td>
                <td class="px-6 py-4 text-right font-semibold text-on-surface">
                    <?= $ev['total_revenue'] > 0 ? '$'.number_format($ev['total_revenue'],2) : 'Free' ?>
                </td>
                <td class="px-6 py-4">
                    <div class="flex justify-center items-center gap-2">
                        <a href="<?= SITE_URL ?>/event.php?id=<?= $ev['id'] ?>" class="p-2 text-slate-400 hover:text-primary rounded-lg hover:bg-orange-50 transition-colors" title="View">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </a>
                        <a href="<?= SITE_URL ?>/edit_event.php?id=<?= $ev['id'] ?>" class="p-2 text-slate-400 hover:text-primary rounded-lg hover:bg-orange-50 transition-colors" title="Edit">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </a>
                        <form method="POST" onsubmit="return confirm('Delete this event? This cannot be undone.')">
                            <input type="hidden" name="delete_event" value="<?= $ev['id'] ?>"/>
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
