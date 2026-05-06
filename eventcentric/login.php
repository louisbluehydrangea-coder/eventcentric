<?php
$pageTitle = 'Log In';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (isLoggedIn()) { header('Location: ' . SITE_URL . '/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (loginUser($email, $password)) {
        header('Location: ' . SITE_URL . '/dashboard.php');
        exit;
    }
    $error = 'Invalid email or password. Please try again.';
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-28 pb-xl px-4 flex justify-center min-h-screen bg-surface">
<div class="w-full max-w-md">
    <div class="bg-white rounded-xl shadow-[0px_4px_15px_rgba(0,0,0,0.08)] p-8">
        <h1 class="text-headline-md font-bold text-on-background mb-2 text-center">Welcome back</h1>
        <p class="text-body-sm text-slate-500 text-center mb-6">Log in to manage your events</p>

        <?php if ($error): ?>
        <div class="flash-error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Email</label>
                <input type="email" name="email" required placeholder="you@email.com"
                    value="<?= sanitize($_POST['email'] ?? '') ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <button type="submit" class="w-full bg-primary-container text-white font-bold py-3 rounded-lg hover:bg-primary transition-colors mt-2">
                Log In
            </button>
        </form>

        <p class="text-center text-body-sm text-slate-500 mt-6">
            Don't have an account?
            <a href="<?= SITE_URL ?>/register.php" class="text-primary font-semibold hover:underline">Sign Up</a>
        </p>
        <p class="text-center text-label-caps text-slate-400 mt-3">
            Demo: organizer@demo.com / password123
        </p>
    </div>
</div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
