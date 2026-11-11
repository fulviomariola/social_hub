<?php
require_once 'db.php';

// Gestione dell'inserimento di un nuovo log di metriche
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $social_account_id = intval($_POST['social_account_id']);
    $views = intval($_POST['views']);
    $followers = intval($_POST['followers_subscribers']);
    $engagement = floatval($_POST['engagement_rate']);
    $recorded_date = $_POST['recorded_date'];
    
    if ($social_account_id > 0 && !empty($recorded_date)) {
        $stmt = $pdo->prepare("INSERT INTO analytics_logs (social_account_id, views, followers_subscribers, engagement_rate, recorded_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$social_account_id, $views, $followers, $engagement, $recorded_date]);
        
        $success_message = "Metriche salvate con successo!";
    }
}

// Recuperiamo i social attivi per il form di inserimento
$stmt = $pdo->query("SELECT * FROM social_accounts WHERE is_active = 1 ORDER BY platform_slug ASC");
$active_accounts = $stmt->fetchAll();

// Recuperiamo lo storico delle metriche unito al nome dell'account
$logs_stmt = $pdo->query("
    SELECT a.*, s.account_name, s.platform_slug 
    FROM analytics_logs a 
    JOIN social_accounts s ON a.social_account_id = s.id 
    ORDER BY a.recorded_date DESC
");
$analytics_logs = $logs_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Social Media Hub - Analytics</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-hub"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Social Hub</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard Generale</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="manage.php">
                    <i class="fas fa-fw fa-tools"></i>
                    <span>Gestione Canali</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="posts.php">
                    <i class="fas fa-fw fa-share-alt"></i>
                    <span>Gestione Post & Link</span></a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="analytics.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Analytics & Metriche</span></a>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <span class="navbar-brand mb-0 h1">Monitoraggio e Statistiche</span>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Analytics dei Canali</h1>
                    </div>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Inserimento Metriche -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Registra Nuova Rilevazione Metriche</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="social_account_id">Seleziona Canale</label>
                                        <select class="form-control" id="social_account_id" name="social_account_id" required>
                                            <option value="">-- Scegli canale --</option>
                                            <?php foreach ($active_accounts as $acc): ?>
                                                <option value="<?php echo $acc['id']; ?>">
                                                    <?php echo htmlspecialchars($acc['account_name'] . ' (' . $acc['platform_slug'] . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="views">Visualizzazioni Totali</label>
                                        <input type="number" class="form-control" id="views" name="views" value="0" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="followers_subscribers">Iscritti / Followers</label>
                                        <input type="number" class="form-control" id="followers_subscribers" name="followers_subscribers" value="0" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="engagement_rate">Engagement Rate (%)</label>
                                        <input type="number" step="0.01" class="form-control" id="engagement_rate" name="engagement_rate" value="0.00" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="recorded_date">Data Rilevazione</label>
                                        <input type="date" class="form-control" id="recorded_date" name="recorded_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salva Metriche
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabella Storico Analytics -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Storico Rilevazioni</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Canale</th>
                                            <th>Visualizzazioni</th>
                                            <th>Iscritti/Followers</th>
                                            <th>Engagement Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($analytics_logs as $log): ?>
                                            <tr>
                                                <td><?php echo $log['recorded_date']; ?></td>
                                                <td class="font-weight-bold"><?php echo htmlspecialchars($log['account_name']); ?> <span class="text-muted">(<?php echo $log['platform_slug']; ?>)</span></td>
                                                <td><?php echo number_format($log['views']); ?></td>
                                                <td><?php echo number_format($log['followers_subscribers']); ?></td>
                                                <td><?php echo $log['engagement_rate']; ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>