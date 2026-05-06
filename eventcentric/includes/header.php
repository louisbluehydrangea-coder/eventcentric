<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
startSession();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?><?= SITE_NAME ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    body { font-family: 'Inter', sans-serif; }
    .flash-success { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:16px; }
    .flash-error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; }
</style>
<script>
tailwind.config = {
    darkMode: "class",
    theme: { extend: {
        colors: {
            "primary": "#a92f00", "primary-container": "#d1410c", "on-primary": "#ffffff",
            "on-primary-container": "#fffaf9", "secondary-container": "#ff6f35",
            "surface": "#fbf8ff", "surface-container-low": "#f3f2ff",
            "surface-container": "#ececff", "on-surface": "#171b2c",
            "on-surface-variant": "#5a4139", "outline-variant": "#e3bfb4",
            "on-background": "#171b2c", "inverse-surface": "#2c2f42",
        },
        borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
        spacing: { xs:"4px", sm:"12px", base:"8px", md:"24px", gutter:"24px", lg:"48px", xl:"64px" },
        fontSize: {
            "headline-xl": ["48px",{lineHeight:"56px",letterSpacing:"-0.02em",fontWeight:"800"}],
            "headline-lg": ["32px",{lineHeight:"40px",letterSpacing:"-0.01em",fontWeight:"700"}],
            "headline-md": ["24px",{lineHeight:"32px",fontWeight:"700"}],
            "body-lg": ["18px",{lineHeight:"28px",fontWeight:"400"}],
            "body-md": ["16px",{lineHeight:"24px",fontWeight:"400"}],
            "body-sm": ["14px",{lineHeight:"20px",fontWeight:"400"}],
            "label-bold": ["14px",{lineHeight:"20px",fontWeight:"600"}],
            "label-caps": ["12px",{lineHeight:"16px",fontWeight:"700"}],
        }
    }}
}
</script>
</head>
<body class="bg-surface text-on-surface antialiased">

<!-- Navbar -->
<header class="bg-white border-b border-slate-200 top-0 z-50 fixed w-full">
<div class="flex justify-between items-center w-full px-4 md:px-8 h-16 max-w-[1080px] mx-auto">
    <div class="flex items-center gap-8">
        <a class="text-2xl font-black text-orange-700" href="<?= SITE_URL ?>/index.php"><?= SITE_NAME ?></a>
        <form action="<?= SITE_URL ?>/events.php" method="GET" class="hidden md:flex items-center bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 w-80">
            <span class="material-symbols-outlined text-slate-400 mr-2">search</span>
            <input class="bg-transparent border-none focus:ring-0 text-sm w-full outline-none" placeholder="Search events" type="text" name="q" value="<?= sanitize($_GET['q'] ?? '') ?>"/>
        </form>
    </div>
    <nav class="hidden md:flex items-center gap-6">
        <a class="<?= basename($_SERVER['PHP_SELF']) === 'events.php' ? 'text-orange-700 font-bold border-b-2 border-orange-700 pb-1' : 'text-slate-600 font-medium hover:text-orange-800' ?> transition-colors" href="<?= SITE_URL ?>/events.php">Find Events</a>
        <a class="text-slate-600 font-medium hover:text-orange-800 transition-colors" href="<?= SITE_URL ?>/create_event.php">Create Events</a>
    </nav>
    <div class="flex items-center gap-4">
        <?php if ($currentUser): ?>
            <a href="<?= SITE_URL ?>/dashboard.php" class="text-slate-600 font-medium px-4 py-2 hover:text-orange-800 transition-colors">
                Hi, <?= sanitize(explode(' ', $currentUser['name'])[0]) ?>
            </a>
            <a href="<?= SITE_URL ?>/logout.php" class="bg-slate-100 text-slate-700 px-6 py-2 rounded-lg font-semibold hover:bg-slate-200 transition-colors">Log Out</a>
        <?php else: ?>
            <a href="<?= SITE_URL ?>/login.php" class="text-slate-600 font-medium px-4 py-2 hover:text-orange-800 transition-colors">Log In</a>
            <a href="<?= SITE_URL ?>/register.php" class="bg-primary-container text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary transition-colors">Sign Up</a>
        <?php endif; ?>
    </div>
</div>
</header>
