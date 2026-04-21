<?php
/**
 * @var string $heading
 * @var string|null $subheading
 * @var string $pageActions Optional HTML for right-side actions
 */
$subheading = $subheading ?? null;
$pageActions = $pageActions ?? '';
?>
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Dashboard</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-stone-900 sm:text-3xl">
            <?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <?php if ($subheading !== null && $subheading !== '') : ?>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-stone-600">
                <?= htmlspecialchars($subheading, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>
    </div>
    <?php if ($pageActions !== '') : ?>
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <?= $pageActions ?>
        </div>
    <?php endif; ?>
</div>
