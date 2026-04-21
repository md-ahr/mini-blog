<?php
/**
 * Contact form.
 *
 * @var string $pageTitle
 * @var string $metaDescription
 * @var bool $sent
 * @var array<string, string> $errors
 * @var array<string, string> $values
 * @var string $csrfToken
 */
require_once base_path('views/partials/head.php');
require_once base_path('views/partials/navbar.php');

$contactUrl = blog_url('contact');
$csrfToken = $csrfToken ?? auth_csrf_token();
$hasErrors = $errors !== [];
$fieldBase = 'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-sm text-stone-900 shadow-inner shadow-stone-900/5 transition placeholder:text-stone-400 focus:outline-none focus:ring-2';
$fieldOk = 'border-stone-200 focus:border-amber-400/90 focus:ring-amber-500/25';
$fieldErr = 'border-red-300 bg-red-50/30 focus:border-red-400 focus:ring-red-400/30';
?>

<main id="main-content" tabindex="-1" class="mx-auto w-full max-w-6xl flex-1 px-4 pb-24 pt-10 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-xl">
        <header class="text-center sm:text-left">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Contact</p>
            <h1 class="font-editorial mt-3 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">
                Say hello
            </h1>
            <p class="mt-4 text-base leading-relaxed text-stone-600">
                Questions, feedback, or collaboration ideas—send a note. Submissions are emailed to the site owner;
                you can also reply via the address you provide.
            </p>
        </header>

        <?php if ($sent) : ?>
            <div class="mt-10 rounded-2xl border border-emerald-200/90 bg-emerald-50/60 px-5 py-4 text-sm text-emerald-950 shadow-sm ring-1 ring-emerald-100/80"
                 role="status"
                 aria-live="polite">
                <p class="font-semibold text-emerald-900">Message received</p>
                <p class="mt-1 leading-relaxed text-emerald-900/90">
                    Thanks for reaching out. Your message has been sent.
                </p>
                <p class="mt-4">
                    <a href="<?= htmlspecialchars(blog_url('contact'), ENT_QUOTES, 'UTF-8') ?>"
                       class="text-sm font-semibold text-emerald-900 underline decoration-emerald-400 underline-offset-4 hover:decoration-emerald-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600/80 focus-visible:ring-offset-2 focus-visible:ring-offset-emerald-50 rounded-sm">
                        Send another message
                    </a>
                </p>
            </div>
        <?php else : ?>
            <?php if ($hasErrors) : ?>
                <div class="mt-8 rounded-2xl border border-red-200/90 bg-red-50/50 px-5 py-4 text-sm text-red-900 shadow-sm ring-1 ring-red-100/80"
                     role="alert"
                     aria-live="assertive">
                    <?php if (isset($errors['general'])) : ?>
                        <p class="font-semibold"><?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if (count($errors) > 1) : ?>
                            <p class="mt-2">Also check the messages below each field.</p>
                        <?php endif; ?>
                    <?php else : ?>
                        <p class="font-semibold">Some fields need your attention—check the messages below each one.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form class="mt-10 space-y-6"
                  method="post"
                  action="<?= htmlspecialchars($contactUrl, ENT_QUOTES, 'UTF-8') ?>"
                  novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                <p class="sr-only">Fields marked as required must be filled before sending.</p>

                <div class="hidden" aria-hidden="true">
                    <label for="contact-website">Website</label>
                    <input id="contact-website" type="text" name="website" tabindex="-1" autocomplete="off"/>
                </div>

                <div>
                    <label for="contact-name" class="block text-sm font-semibold text-stone-800">
                        Name <span class="font-normal text-red-600" aria-hidden="true">*</span>
                    </label>
                    <input id="contact-name"
                           name="name"
                           type="text"
                           required
                           maxlength="120"
                           autocomplete="name"
                           value="<?= htmlspecialchars($values['name'], ENT_QUOTES, 'UTF-8') ?>"
                           class="<?= $fieldBase ?> <?= isset($errors['name']) ? $fieldErr : $fieldOk ?>"
                           aria-required="true"
                           <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="err-name"' : '' ?>/>
                    <?php if (isset($errors['name'])) : ?>
                        <p id="err-name" class="mt-1.5 text-sm text-red-700"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="contact-email" class="block text-sm font-semibold text-stone-800">
                        Email <span class="font-normal text-red-600" aria-hidden="true">*</span>
                    </label>
                    <input id="contact-email"
                           name="email"
                           type="email"
                           required
                           maxlength="191"
                           autocomplete="email"
                           value="<?= htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8') ?>"
                           class="<?= $fieldBase ?> <?= isset($errors['email']) ? $fieldErr : $fieldOk ?>"
                           aria-required="true"
                           <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="err-email"' : '' ?>/>
                    <?php if (isset($errors['email'])) : ?>
                        <p id="err-email" class="mt-1.5 text-sm text-red-700"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="contact-subject" class="block text-sm font-semibold text-stone-800">
                        Subject <span class="text-xs font-normal text-stone-500">(optional)</span>
                    </label>
                    <input id="contact-subject"
                           name="subject"
                           type="text"
                           maxlength="200"
                           value="<?= htmlspecialchars($values['subject'], ENT_QUOTES, 'UTF-8') ?>"
                           class="<?= $fieldBase ?> <?= isset($errors['subject']) ? $fieldErr : $fieldOk ?>"
                           <?= isset($errors['subject']) ? 'aria-invalid="true" aria-describedby="err-subject"' : '' ?>/>
                    <?php if (isset($errors['subject'])) : ?>
                        <p id="err-subject" class="mt-1.5 text-sm text-red-700"><?= htmlspecialchars($errors['subject'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="contact-message" class="block text-sm font-semibold text-stone-800">
                        Message <span class="font-normal text-red-600" aria-hidden="true">*</span>
                    </label>
                    <textarea id="contact-message"
                              name="message"
                              required
                              rows="6"
                              maxlength="8000"
                              class="<?= $fieldBase ?> min-h-[9rem] resize-y <?= isset($errors['message']) ? $fieldErr : $fieldOk ?>"
                              aria-required="true"
                              <?= isset($errors['message']) ? 'aria-invalid="true" aria-describedby="err-message"' : '' ?>><?= htmlspecialchars($values['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    <p class="mt-1.5 text-xs text-stone-500">Minimum 10 characters, up to 8,000.</p>
                    <?php if (isset($errors['message'])) : ?>
                        <p id="err-message" class="mt-1.5 text-sm text-red-700"><?= htmlspecialchars($errors['message'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-stone-500">
                        <span class="text-red-600" aria-hidden="true">*</span> Required fields
                    </p>
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-stone-900 px-5 py-3 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 sm:w-auto">
                        Send message
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <p class="mt-12 text-center sm:text-left">
            <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
               class="inline-flex items-center gap-2 text-sm font-semibold text-stone-900 underline decoration-stone-300 underline-offset-4 transition hover:decoration-stone-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                ← Back to home
            </a>
        </p>
    </div>
</main>

<?php require_once base_path('views/partials/footer.php'); ?>
