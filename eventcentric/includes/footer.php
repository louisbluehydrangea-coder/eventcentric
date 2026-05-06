<!-- Footer -->
<footer class="bg-slate-50 border-t border-slate-200">
<div class="max-w-[1080px] mx-auto py-12 px-4 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
    <div class="col-span-2 md:col-span-1">
        <div class="text-lg font-bold text-slate-900 mb-4"><?= SITE_NAME ?></div>
        <p class="text-slate-500 max-w-xs">Organize, find, and attend the world's most vibrant experiences.</p>
    </div>
    <div class="flex flex-col gap-2">
        <h4 class="text-slate-900 font-semibold mb-2">Plan Events</h4>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="<?= SITE_URL ?>/create_event.php">Create Events</a>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="#">Sell Tickets</a>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="#">Online Events</a>
    </div>
    <div class="flex flex-col gap-2">
        <h4 class="text-slate-900 font-semibold mb-2">About</h4>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="#">About Us</a>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="#">Blog</a>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="#">Careers</a>
    </div>
    <div class="flex flex-col gap-2">
        <h4 class="text-slate-900 font-semibold mb-2">Support</h4>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="#">Help Center</a>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="#">Terms</a>
        <a class="text-slate-500 hover:underline hover:text-orange-700 cursor-pointer" href="#">Privacy</a>
    </div>
</div>
<div class="max-w-[1080px] mx-auto pb-8 px-4 flex flex-col md:flex-row justify-between items-center border-t border-slate-200 pt-8 gap-4">
    <p class="text-slate-500 text-sm">© <?= date('Y') ?> <?= SITE_NAME ?></p>
</div>
</footer>
</body>
</html>
