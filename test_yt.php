<?php
require_once 'db.php';
require_once 'services/YouTubeService.php';

// Assicurati di avere almeno un account inserito nella tabella social_accounts (es. con ID 1)
$accountId = 1; 

try {
    // Istanziamo il servizio YouTube per quell'account
    $youtube = new YouTubeService($pdo, $accountId);

    // Eseguiamo la sincronizzazione delle metriche (simulata)
    $youtube->syncAnalytics();

    echo "<h3>Test completato con successo!</h3>";
    echo "<p>Controlla la tabella <code>analytics_logs</code> nel database o la pagina <code>analytics.php</code> per verificare che sia stata inserita la nuova riga.</p>";

} catch (Exception $e) {
    echo "Errore durante il test: " . $e.getMessage();
}