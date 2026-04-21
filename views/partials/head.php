<?php
$pageTitle = $pageTitle ?? 'Mini Blog';
$metaDescription = $metaDescription ?? '';
$pageRobots = $pageRobots ?? '';
$canonicalUrl = $canonicalUrl ?? '';
$ogUrl = $ogUrl ?? '';
$ogImage = $ogImage ?? '';
$ogTitle = $ogTitle ?? '';
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
    <?php if ($canonicalUrl !== '') : ?>
        <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if ($ogUrl !== '') : ?>
        <meta property="og:type" content="article">
        <meta property="og:url" content="<?= htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($ogTitle !== '') : ?>
            <meta property="og:title" content="<?= htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <?php if ($metaDescription !== '') : ?>
            <meta property="og:description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <?php if ($ogImage !== '') : ?>
            <meta property="og:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <meta name="twitter:card" content="<?= $ogImage !== '' ? 'summary_large_image' : 'summary' ?>">
        <?php if ($ogTitle !== '') : ?>
            <meta name="twitter:title" content="<?= htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <?php if ($metaDescription !== '') : ?>
            <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <?php if ($ogImage !== '') : ?>
            <meta name="twitter:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($pageRobots !== '') : ?>
        <meta name="robots" content="<?= htmlspecialchars($pageRobots, ENT_QUOTES, 'UTF-8') ?>">
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
