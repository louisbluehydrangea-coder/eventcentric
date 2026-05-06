<?php
// Expects $event array with id, title, location, start_datetime, min_price, image_path
?>
<a href="<?= SITE_URL ?>/event.php?id=<?= (int)$event['id'] ?>" class="block bg-white rounded-xl overflow-hidden shadow-[0px_4px_15px_rgba(0,0,0,0.05)] hover:shadow-[0px_8px_25px_rgba(0,0,0,0.1)] transition-all duration-300 group cursor-pointer">
    <div class="aspect-video relative overflow-hidden">
        <img src="<?= getEventImageUrl($event['image_path']) ?>"
             alt="<?= sanitize($event['title']) ?>"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
        <?php if (!empty($event['is_featured'])): ?>
        <div class="absolute bottom-4 left-4">
            <span class="bg-orange-700 text-white text-label-caps px-3 py-1 rounded-lg">Featured</span>
        </div>
        <?php endif; ?>
    </div>
    <div class="p-md">
        <div class="flex items-center gap-xs mb-base">
            <span class="text-primary font-semibold text-label-bold"><?= formatEventDate($event['start_datetime']) ?></span>
        </div>
        <h3 class="font-bold text-headline-md text-on-background mb-xs line-clamp-2"><?= sanitize($event['title']) ?></h3>
        <p class="text-body-sm text-slate-500 mb-md line-clamp-1"><?= sanitize($event['location']) ?></p>
        <div class="flex items-center justify-between">
            <span class="text-label-bold text-on-surface font-bold"><?= formatPrice((float)($event['min_price'] ?? 0)) ?></span>
            <?php if (!empty($event['category'])): ?>
            <span class="text-label-caps px-2 py-1 bg-surface-container-low rounded text-on-surface-variant"><?= sanitize($event['category']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</a>
