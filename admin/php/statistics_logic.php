<?php

/**
 * Logique métier pour admin/statistics.php
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/Stats.php';

// Vérifier si l'admin est connecté
if (!isAdminLoggedIn()) {
    redirect('admin/login.php');
}

$stats = new Stats();
$global_stats = $stats->getGlobalStats();
$recent_users = $stats->getRecentUsers(5);
$recent_posts = $stats->getRecentPosts(5);
