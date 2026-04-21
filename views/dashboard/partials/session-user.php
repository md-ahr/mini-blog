<?php
/**
 * Logged-in user chip for dashboard chrome.
 *
 * @var string $sessionUserVariant 'aside' | 'toolbar'
 */
$sessionUserVariant = $sessionUserVariant ?? 'aside';
$u = auth_user();
if ($u === null) {
    return;
}
$email = htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8');
$name = htmlspecialchars((string) ($u['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$src = htmlspecialchars(auth_user_avatar_src($u), ENT_QUOTES, 'UTF-8');
$alt = htmlspecialchars(auth_user_avatar_alt($u), ENT_QUOTES, 'UTF-8');

$wrap = $sessionUserVariant === 'toolbar'
    ? 'flex items-center gap-2'
    : 'border-b border-stone-100 px-5 py-4';

if ($sessionUserVariant === 'toolbar') : ?>
    <div class="<?= $wrap ?>" aria-label="Signed in as" title="<?= $email ?>">
        <img src="<?= $src ?>"
             alt="<?= $alt ?>"
             width="32"
             height="32"
             class="h-8 w-8 shrink-0 rounded-full bg-stone-200 object-cover ring-2 ring-stone-200/80"
             loading="lazy"
             decoding="async"/>
        <div class="hidden min-w-0 sm:block">
            <p class="truncate text-xs font-semibold text-stone-900"><?= $name !== '' ? $name : 'Account' ?></p>
            <p class="truncate text-[11px] text-stone-500"><?= $email ?></p>
        </div>
    </div>
<?php else : ?>
    <div class="<?= $wrap ?>">
        <div class="flex items-center gap-3">
            <img src="<?= $src ?>"
                 alt="<?= $alt ?>"
                 width="40"
                 height="40"
                 class="h-10 w-10 shrink-0 rounded-full bg-stone-200 object-cover ring-2 ring-stone-200/80 shadow-sm"
                 loading="lazy"
                 decoding="async"/>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-stone-900"><?= $name !== '' ? $name : 'Signed in' ?></p>
                <p class="truncate text-xs text-stone-500" title="<?= $email ?>"><?= $email ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>
