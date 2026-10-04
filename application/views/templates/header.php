<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Sistem Point of Sale — NOL DERAJAT COFFEE">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo-ndc.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/images/logo-ndc.png') ?>">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - NOL DERAJAT COFFEE' : 'NOL DERAJAT COFFEE' ?></title>
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- NexaPOS Custom Theme -->
    <link href="<?= base_url('assets/css/nexapos.css?v=3.0') ?>" rel="stylesheet">
    <!-- Tailwind compiled utilities (built via npm) -->
    <link href="<?= base_url('assets/css/tailwind.css?v=1.0') ?>" rel="stylesheet">
</head>
<body>
<div class="layout-overlay" id="layoutOverlay"></div>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
