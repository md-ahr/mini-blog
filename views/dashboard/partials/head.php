<?php
$pageTitle = $pageTitle ?? 'Dashboard — Mini Blog';
$metaDescription = $metaDescription ?? '';
require_once base_path('views/dashboard/partials/form-tokens.php');
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
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://rsms.me/"/>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css"/>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        :root {
            --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
        }

        .dashboard-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2357534e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.125rem 1.125rem;
        }

        .dashboard-select:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }
    </style>
</head>
<body class="font-sans min-h-screen bg-stone-100 text-stone-900 antialiased [font-feature-settings:'kern'_1,'liga'_1]">
