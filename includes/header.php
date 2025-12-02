<?php
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config.php';
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <h1><a href="<?php echo BASE_URL; ?>"><?php echo SITE_NAME; ?></a></h1>
        <p class="tagline">Calm, practical guidance for high-conflict co-parenting.</p>
    </div>
</header>
<main class="container">
