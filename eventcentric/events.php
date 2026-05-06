<?php
$pageTitle = 'Find Events';
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// Collect filters
$q        = trim($_GET['q'] ?? '');
$location = trim($_GET['location'] ?? '');
$category = trim($_GET['category'] ?? '');
$format   = trim($_GET['format'] ?? '');
$price    = trim($_GET['price'] ?? '');   // 'free' | 'paid'
$date     = trim($_GET['date'] ?? '');    // 'today' | 'tomorrow' | 'weekend'
$sort     = $_GET['sort'] ?? 'date';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 8;

// Build dynamic query
$where = ["1=1"];
$params = [];

if ($q !== '') {
    $where[] = "(e.title LIKE ? OR e.description LIKE ? OR e.location LIKE ?)";
    $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%";
}
if ($location !== '') {
    $where[] = "e.location LIKE ?";
    $params[] = "%$location%";
}
if ($category !== '') {
    $where[] = "e.category = ?";
    $params[] = $category;
}
if ($format !== '') {
    $where[] = "e.format = ?";
    $params[] = $format;
}
if ($price === 'free') {
    $where[] = "(SELECT MIN(price) FROM tickets WHERE event_id=e.id) = 0";
} elseif ($price === 'paid') {
    $where[] = "(SELECT MIN(price) FROM tickets WHERE event_id=e.id) > 0";
}
if ($date === 'today') {
    $where[] = "DATE(e.start_datetime) = date('now', 'localtime')";
} elseif ($date === 'tomorrow') {
    $where[] = "DATE(e.start_datetime) = date('now', '+1 day', 'localtime')";
} elseif ($date === 'weekend') {
    $where[] = "cast(strftime('%w', e.start_datetime) as integer) IN (0,6)";
}

$orderClause = $sort === 'price' ? "MIN(t.price) ASC" : "e.start_datetime ASC";
$whereSQL = implode(' AND ', $where);

// Count total
$countStmt = $db->prepare("SELECT COUNT(DISTINCT e.id) FROM events e LEFT JOIN tickets t ON t.event_id=e.id WHERE $whereSQL");
$countStmt->execute($params);
$totalCount = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalCount / $perPage));
$offset = ($page - 1) * $perPage;

// Fetch events
$stmt = $db->prepare("
    SELECT e.*, MIN(t.price) as min_price
    FROM events e
    LEFT JOIN tickets t ON t.event_id = e.id
    WHERE $whereSQL
    GROUP BY e.id
    ORDER BY $orderClause
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$events = $stmt->fetchAll();

$categories = $db->query("SELECT DISTINCT category FROM events WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$formats    = $db->query("SELECT DISTINCT format FROM events WHERE format IS NOT NULL ORDER BY format")->fetchAll(PDO::FETCH_COLUMN);
?>

<main class="pt-24 pb-xl px-4 md:px-8 max-w-[1080px] mx-auto min-h-screen">

<!-- Search Header -->
<div class="mb-lg">
    <h1 class="text-headline-lg font-bold text-on-background mb-base">
        <?= $q ? 'Results for "' . sanitize($q) . '"' : ($category ? sanitize($category) . ' Events' : 'All Events') ?>
    </h1>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md">
        <p class="text-body-md text-slate-500"><?= number_format($totalCount) ?> results found</p>
        <div class="flex items-center gap-sm">
            <span class="text-label-bold text-on-surface-variant">Sort by:</span>
            <select onchange="applySort(this.value)" class="bg-white border border-outline-variant rounded-lg text-body-sm px-4 py-2 focus:ring-primary outline-none">
                <option value="date" <?= $sort==='date'?'selected':'' ?>>Date</option>
                <option value="price" <?= $sort==='price'?'selected':'' ?>>Price: Low to High</option>
            </select>
        </div>
    </div>
</div>

<div class="flex flex-col md:flex-row gap-gutter">

<!-- Sidebar Filters -->
<aside class="w-full md:w-64 flex-shrink-0">
<form method="GET" action="events.php" id="filterForm">
    <input type="hidden" name="q" value="<?= sanitize($q) ?>"/>
    <input type="hidden" name="sort" value="<?= sanitize($sort) ?>"/>
    <div class="sticky top-24 space-y-8">

        <!-- Date -->
        <section>
            <h3 class="font-semibold text-label-bold text-on-surface mb-sm">Date</h3>
            <div class="space-y-base">
                <?php foreach ([''=>'All dates','today'=>'Today','tomorrow'=>'Tomorrow','weekend'=>'This weekend'] as $val=>$label): ?>
                <label class="flex items-center gap-sm cursor-pointer group">
                    <input type="radio" name="date" value="<?= $val ?>" <?= $date===$val?'checked':'' ?> onchange="document.getElementById('filterForm').submit()" class="w-5 h-5 border-outline text-primary"/>
                    <span class="text-body-sm text-on-surface-variant group-hover:text-primary"><?= $label ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Price -->
        <section>
            <h3 class="font-semibold text-label-bold text-on-surface mb-sm">Price</h3>
            <div class="flex flex-wrap gap-xs">
                <?php foreach ([''=>'Any','free'=>'Free','paid'=>'Paid'] as $val=>$label): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['price'=>$val, 'page'=>1])) ?>"
                   class="px-sm py-base text-label-caps rounded-lg border cursor-pointer transition-colors <?= $price===$val ? 'bg-primary-container text-white border-primary-container' : 'bg-surface-container-low text-on-surface-variant border-outline-variant hover:border-primary hover:text-primary' ?>">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Format -->
        <?php if (!empty($formats)): ?>
        <section>
            <h3 class="font-semibold text-label-bold text-on-surface mb-sm">Format</h3>
            <div class="space-y-base">
                <?php foreach ($formats as $f): ?>
                <label class="flex items-center gap-sm cursor-pointer group">
                    <input type="checkbox" name="format" value="<?= sanitize($f) ?>" <?= $format===$f?'checked':'' ?> onchange="document.getElementById('filterForm').submit()" class="w-5 h-5 border-outline text-primary"/>
                    <span class="text-body-sm text-on-surface-variant group-hover:text-primary"><?= sanitize($f) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Category -->
        <?php if (!empty($categories)): ?>
        <section>
            <h3 class="font-semibold text-label-bold text-on-surface mb-sm">Category</h3>
            <div class="space-y-base">
                <?php foreach ($categories as $cat): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['category'=>$cat==$category?'':$cat, 'page'=>1])) ?>"
                   class="flex items-center gap-sm text-body-sm <?= $category===$cat ? 'text-primary font-semibold' : 'text-on-surface-variant hover:text-primary' ?>">
                    <?= $category===$cat ? '✓ ' : '' ?><?= sanitize($cat) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </div>
</form>
</aside>

<!-- Event Grid -->
<div class="flex-1">
    <?php if (empty($events)): ?>
    <div class="text-center py-24">
        <span class="material-symbols-outlined text-6xl text-slate-300">event_busy</span>
        <p class="text-body-lg text-slate-500 mt-4">No events found. Try different filters.</p>
        <a href="events.php" class="inline-block mt-4 text-primary font-semibold hover:underline">Clear all filters</a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
        <?php foreach ($events as $event): ?>
        <?php include __DIR__ . '/includes/event_card.php'; ?>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="mt-xl flex justify-center items-center gap-base">
        <?php if ($page > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])) ?>" class="p-2 rounded-lg border border-outline-variant hover:border-primary text-on-surface-variant hover:text-primary">
            <span class="material-symbols-outlined">chevron_left</span>
        </a>
        <?php endif; ?>
        <?php for ($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
        <a href="?<?= http_build_query(array_merge($_GET,['page'=>$p])) ?>"
           class="w-10 h-10 flex items-center justify-center rounded-lg font-semibold <?= $p===$page ? 'bg-primary-container text-white' : 'border border-outline-variant text-on-surface-variant hover:border-primary hover:text-primary' ?>">
            <?= $p ?>
        </a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
        <a href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])) ?>" class="p-2 rounded-lg border border-outline-variant hover:border-primary text-on-surface-variant hover:text-primary">
            <span class="material-symbols-outlined">chevron_right</span>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

</div>
</main>

<script>
function applySort(val) {
    const url = new URL(window.location);
    url.searchParams.set('sort', val);
    url.searchParams.set('page', 1);
    window.location = url;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
