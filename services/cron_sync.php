<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/YouTubeService.php';
require_once __DIR__ . '/TelegramService.php';

echo "=== AVVIO CRON SYNC ===\n";

// ---------------------------------------------------------
// PARTE 1: SINCRONIZZAZIONE ANALYTICS DEGLI ACCOUNT ATTIVI
// ---------------------------------------------------------
$stmt = $pdo->query("SELECT * FROM social_accounts WHERE is_active = 1");
$accounts = $stmt->fetchAll();

foreach ($accounts as $acc) {
    $platform = $acc['platform_slug'];
    $accountId = $acc['id'];
    $accountName = $acc['account_name'];

    echo "[Analytics] Elaborazione: $accountName ($platform)...\n";

    if ($platform === 'youtube') {
        $service = new YouTubeService($pdo, $accountId);
        $service->syncAnalytics();
        echo "--> Metriche YouTube aggiornate (Account ID: $accountId)\n";
    } elseif ($platform === 'telegram') {
        $service = new TelegramService($pdo, $accountId);
        $service->syncAnalytics();
        echo "--> Metriche Telegram aggiornate (Account ID: $accountId)\n";
    }
}

// ---------------------------------------------------------
// PARTE 2: ELABORAZIONE CODA POST IN ATTESA (pending)
// ---------------------------------------------------------
$pendingStmt = $pdo->query("SELECT * FROM posts_log WHERE status = 'pending' ORDER BY created_at ASC");
$pendingPosts = $pendingStmt->fetchAll();

if (count($pendingPosts) > 0) {
    echo "\n[Coda Post] Trovati " . count($pendingPosts) . " contenuti da pubblicare...\n";

    foreach ($pendingPosts as $post) {
        $postId = $post['id'];
        $title = $post['title'];
        $sourceUrl = $post['source_url'];
        $targetAccountIds = explode(',', $post['platforms_targeted']);

        echo "Elaborazione Post ID #$postId: '$title'\n";

        foreach ($targetAccountIds as $accId) {
            $accId = trim($accId);
            if (empty($accId)) continue;

            // Recuperiamo i dettagli dell'account destinatario
            $accStmt = $pdo->prepare("SELECT * FROM social_accounts WHERE id = ? AND is_active = 1");
            $accStmt->execute([$accId]);
            $targetAccount = $accStmt->fetch();

            if ($targetAccount) {
                $platform = $targetAccount['platform_slug'];

                if ($platform === 'youtube') {
                    $ytService = new YouTubeService($pdo, $targetAccount['id']);
                    $ytService->publishPost($title, $sourceUrl);
                    echo "--> Pubblicato su YouTube (Account: {$targetAccount['account_name']})\n";
                } elseif ($platform === 'telegram') {
                    $tgService = new TelegramService($pdo, $targetAccount['id']);
                    $tgService->publishPost($title, $sourceUrl);
                    echo "--> Pubblicato su Telegram (Account: {$targetAccount['account_name']})\n";
                }
            }
        }

        // Aggiorniamo lo stato del post da 'pending' a 'published'
        $updateStmt = $pdo->prepare("UPDATE posts_log SET status = 'published' WHERE id = ?");
        $updateStmt->execute([$postId]);
        echo "--> Stato Post ID #$postId aggiornato a 'published'.\n";
    }
} else {
    echo "\n[Coda Post] Nessun post in attesa di pubblicazione.\n";
}

echo "\n=== CRON SYNC COMPLETATO ===\n";