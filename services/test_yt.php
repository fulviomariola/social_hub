<?php
require_once 'db.php';
require_once 'services/YouTubeService.php';

// Supponiamo che l'account YouTube nel database abbia ID 1
$youtube = new YouTubeService($pdo, 1);
$youtube->syncAnalytics();

echo "Sincronizzazione YouTube completata con successo!";