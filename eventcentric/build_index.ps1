$html = Get-Content -Raw -Path "Home.html"

$phpTop = @"
<?php
`$pageTitle = 'Home';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
startSession();
`$currentUser = getCurrentUser();

`$db = getDB();
`$upcoming = `$db->query("SELECT e.*, MIN(t.price) as min_price FROM events e LEFT JOIN tickets t ON t.event_id = e.id GROUP BY e.id ORDER BY e.start_datetime ASC LIMIT 4")->fetchAll();
?>
"@

# Replace navbar
$navPattern = '<nav class="hidden md:flex items-center gap-6">[\s\S]*?</nav>'
$navReplace = '<nav class="hidden md:flex items-center gap-6">
<a class="text-orange-700 dark:text-orange-500 font-bold border-b-2 border-orange-700 pb-1 hover:text-orange-800 dark:hover:text-orange-400 transition-colors duration-200" href="<?= SITE_URL ?>/events.php">Find Events</a>
<a class="text-slate-600 dark:text-slate-400 font-medium hover:text-orange-800 dark:hover:text-orange-400 transition-colors duration-200" href="<?= SITE_URL ?>/create_event.php">Create Events</a>
<?php if ($currentUser): ?>
    <a href="<?= SITE_URL ?>/dashboard.php" class="text-slate-600 font-medium px-4 py-2 hover:text-orange-800 transition-colors">Hi, <?= sanitize(explode('' '', $currentUser[''name''])[0]) ?></a>
    <a href="<?= SITE_URL ?>/logout.php" class="bg-slate-100 text-slate-700 px-6 py-2 rounded-lg font-semibold hover:bg-slate-200 transition-colors">Log Out</a>
<?php else: ?>
    <a href="<?= SITE_URL ?>/login.php" class="text-slate-600 dark:text-slate-400 font-semibold hover:text-orange-800 transition-colors">Log In</a>
    <a href="<?= SITE_URL ?>/register.php" class="bg-[#D1410C] text-white px-6 py-2 rounded-lg font-label-bold active:opacity-80 transition-opacity">Sign Up</a>
<?php endif; ?>
</nav>'
$html = [regex]::Replace($html, $navPattern, $navReplace)

# Replace search form
$html = $html.Replace('<div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-2 flex flex-col md:flex-row items-center gap-2">', '<form action="<?= SITE_URL ?>/events.php" method="GET" class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-2 flex flex-col md:flex-row items-center gap-2">')
$html = $html.Replace('</button>
</div>
</div>
</section>', '</button>
</form>
</div>
</section>')

$html = $html.Replace('placeholder="Search events, organizers, or themes" type="text"/>', 'name="q" placeholder="Search events, organizers, or themes" type="text"/>')
$html = $html.Replace('placeholder="London, UK" type="text"/>', 'name="location" placeholder="London, UK" type="text"/>')

# Replace event grid
$gridPattern = '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">[\s\S]*?<div class="mt-12 text-center">'
$gridReplace = '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
<?php foreach ($upcoming as $event): ?>
<a href="<?= SITE_URL ?>/event.php?id=<?= $event[''id''] ?>" class="flex flex-col bg-white rounded-xl shadow-[0px_4px_15px_rgba(0,0,0,0.05)] hover:shadow-[0px_10px_25px_rgba(0,0,0,0.1)] transition-all duration-300 group cursor-pointer border border-[#DBDAE3]">
<div class="relative overflow-hidden rounded-t-xl aspect-[4/3]">
<img src="<?= getEventImageUrl($event[''image_path'']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
</div>
<div class="p-sm flex flex-col h-full">
<p class="text-orange-700 font-label-bold text-xs mb-1"><?= formatEventDate($event[''start_datetime'']) ?></p>
<h3 class="font-headline-md text-base text-on-surface line-clamp-2 mb-2"><?= sanitize($event[''title'']) ?></h3>
<p class="text-[#6F7287] font-body-sm mb-4"><?= sanitize($event[''location''] ?: ''Online'') ?></p>
<div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
<span class="text-[#39364F] font-label-bold"><?= formatPrice($event[''min_price'']) ?></span>
</div>
</div>
</a>
<?php endforeach; ?>
</div>
<div class="mt-12 text-center">'
$html = [regex]::Replace($html, $gridPattern, $gridReplace)

# Write to index.php
$finalContent = $phpTop + "`n" + $html
Set-Content -Path "index.php" -Value $finalContent -Encoding UTF8
Write-Host "Success!"
