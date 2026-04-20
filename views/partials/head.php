<?php
$pageTitle = $pageTitle ?? 'Mini Blog';
$metaDescription = $metaDescription ?? '';
?>
<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <?php if ($metaDescription !== '') : ?>
        <meta name="description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://rsms.me/"/>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css"/>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        :root {
            --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col bg-stone-50 text-stone-900 antialiased [font-feature-settings:'kern'_1,'liga'_1]">
