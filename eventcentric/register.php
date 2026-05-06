<?php
$pageTitle = 'Sign Up';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (isLoggedIn()) { header('Location: ' . SITE_URL . '/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if (!$name || !$email || !$pass) {
        $error = 'All fields are required.';
    } elseif ($pass !== $pass2) {
        $error = 'Passwords do not match.';
    } elseif (strlen($pass) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (registerUser($name, $email, $pass)) {
        header('Location: ' . SITE_URL . '/dashboard.php');
        exit;
    } else {
        $error = 'An account with that email already exists.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-28 pb-xl px-4 flex justify-center min-h-screen bg-surface">
<div class="w-full max-w-md">
    <div class="bg-white rounded-xl shadow-[0px_4px_15px_rgba(0,0,0,0.08)] p-8">
        <h1 class="text-headline-md font-bold text-on-background mb-2 text-center">Create your account</h1>
        <p class="text-body-sm text-slate-500 text-center mb-6">Start organizing or attending events today</p>

        <?php if ($error): ?>
        <div class="flash-error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Full Name</label>
                <input type="text" name="name" required placeholder="Jane Doe"
                    value="<?= sanitize($_POST['name'] ?? '') ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Email</label>
                <input type="email" name="email" required placeholder="you@email.com"
                    value="<?= sanitize($_POST['email'] ?? '') ?>"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Password</label>
                <input type="password" name="password" required placeholder="Min. 8 characters"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <div>
                <label class="block text-label-bold text-on-surface mb-1">Confirm Password</label>
                <input type="password" name="password2" required placeholder="Repeat password"
                    class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm outline-none focus:border-primary"/>
            </div>
            <button type="submit" class="w-full bg-primary-container text-white font-bold py-3 rounded-lg hover:bg-primary transition-colors mt-2">
                Create Account
            </button>
        </form>

        <p class="text-center text-body-sm text-slate-500 mt-6">
            Already have an account?
            <a href="<?= SITE_URL ?>/login.php" class="text-primary font-semibold hover:underline">Log In</a>
        </p>
    </div>
</div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
