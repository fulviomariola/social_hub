<?php
require_once 'db.php';

// Recuperiamo l'elenco dei social account dal database
$stmt = $pdo->query("SELECT * FROM social_accounts");
$accounts = $stmt->fetchAll();

// Recuperiamo i dati aggregati per data per il grafico (ultimi 7 record/giorni)
$chart_stmt = $pdo->query("
    SELECT recorded_date, SUM(views) as total_views 
    FROM analytics_logs 
    GROUP BY recorded_date 
    ORDER BY recorded_date ASC 
    LIMIT 7
");
$chart_data = $chart_stmt->fetchAll();

// Prepariamo gli array PHP da convertire in JSON per JavaScript
$labels = [];
$views_data = [];

foreach ($chart_data as $row) {
    $labels[] = date('d/m', strtotime($row['recorded_date']));
    $views_data[] = $row['total_views'];
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Social Media Hub - Dashboard</title>

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

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-hub"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Social Hub</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard Generale</span></a>
            </li>

            <!-- Nav Item - Gestione Canali -->
            <li class="nav-item">
                <a class="nav-link" href="manage.php">
                    <i class="fas fa-fw fa-tools"></i>
                    <span>Gestione Canali</span></a>
            </li>

            <!-- Nav Item - Gestione Post & Link -->
            <li class="nav-item">
                <a class="nav-link" href="posts.php">
                    <i class="fas fa-fw fa-share-alt"></i>
                    <span>Gestione Post & Link</span></a>
            </li>

            <!-- Nav Item - Analytics -->
            <li class="nav-item">
                <a class="nav-link" href="analytics.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Analytics & Metriche</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Canali Collegati (Attivi)
            </div>

            <!-- Lista dinamica dei social -->
            <?php foreach ($accounts as $acc): ?>
                <?php if ($acc['is_active']): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-fw fa-circle text-success"></i>
                            <span><?php echo htmlspecialchars($acc['account_name']); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <span class="navbar-brand mb-0 h1">Gestione Locale Social Media Hub</span>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Panoramica Canali</h1>
                    </div>

                    <!-- Content Row - Cards -->
                    <div class="row">
                        <?php foreach ($accounts as $acc): ?>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-<?php echo $acc['is_active'] ? 'primary' : 'secondary'; ?> shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Piattaforma: <?php echo htmlspecialchars($acc['platform_slug']); ?></div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?php echo htmlspecialchars($acc['account_name']); ?>
                                                </div>
                                                <div class="mt-2 text-xs text-muted">
                                                    Stato: <strong><?php echo $acc['is_active'] ? 'Attivo' : 'Disattivato'; ?></strong>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-share-alt fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Content Row - Grafico -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Andamento Visualizzazioni Totali</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="viewsChart"></canvas>
                                    </div>
                                </div>
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

    <!-- Page level plugins (Chart.js incluso nel template) -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Inizializzazione Grafico Dinamico -->
    <script>
    var ctx = document.getElementById("viewsChart");
    var viewsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: "Visualizzazioni",
          lineTension: 0.3,
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "rgba(78, 115, 223, 1)",
          pointRadius: 3,
          pointBackgroundColor: "rgba(78, 115, 223, 1)",
          pointBorderColor: "rgba(78, 115, 223, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
          pointHoverBorderColor: "rgba(78, 115, 223, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: <?php echo json_encode($views_data); ?>,
        }],
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          xAxes: [{
            gridLines: { display: false }
          }],
          yAxes: [{
            ticks: { beginAtZero: true }
          }],
        },
        legend: { display: false }
      }
    });
    </script>

</body>

</html>