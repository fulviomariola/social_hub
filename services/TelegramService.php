<?php
class TelegramService {
    private $pdo;
    private $accountId;

    public function __construct($pdo, $accountId) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
        $this->accountId = $accountId;
    }

    // Metodo per sincronizzare le metriche (chiamato dalla Parte 1 del cron)
    public function syncAnalytics() {
        // Inserimento allineato alle colonne reali della tabella analytics_logs
        $stmt = $this->pdo->prepare("INSERT INTO analytics_logs (social_account_id, views, followers_subscribers, engagement_rate, recorded_date) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt->execute([
            $this->accountId, 
            rand(500, 3000), // views simulate
            rand(50, 200),   // followers/subscribers simulati
            1.50             // engagement rate simulato
        ]);
    }

    // Metodo per pubblicare un post (chiamato dalla Parte 2 del cron)
    public function publishPost($content, $sourceUrl) {
        // Qui in futuro integrerai le vere chiamate cURL alle API di Telegram
        // Al momento registriamo l'azione sul log o simuliamo l'invio
        error_log("TelegramService [Account ID: {$this->accountId}] - Pubblicazione in corso: " . $content);
        return true;
    }
}