<?php
require_once 'db.php';

// Gestione dell'azione di aggiornamento stato (attivo/disattivo)
if (isset($_POST['toggle_id'])) {
    $id = intval($_POST['toggle_id']);
    $new_status = intval($_POST['new_status']);
    
    $stmt = $pdo->prepare("UPDATE social_accounts SET is_active = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    
    header("Location: manage.php");
    exit;
}

// Recupero di tutti gli account
$stmt = $pdo->query("SELECT * FROM social_accounts ORDER BY platform_slug ASC");
$accounts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Social Media Hub - Gestione Canali</title>

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

            <li class="nav-item active">
                <a class="nav-link" href="manage.php">
                    <i class="fas fa-fw fa-tools"></i>
                    <span>Gestione Canali</span></a>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <span class="navbar-brand mb-0 h1">Gestione e Configurazione Social Network</span>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">I tuoi Canali Collegati</h1>
                    </div>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Abilita o Disabilita Piattaforme</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Piattaforma</th>
                                            <th>Nome Account</th>
                                            <th>Stato Attuale</th>
                                            <th>Azione Rapida</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($accounts as $acc): ?>
                                            <tr>
                                                <td class="font-weight-bold text-capitalize"><?php echo htmlspecialchars($acc['platform_slug']); ?></td>
                                                <td><?php echo htmlspecialchars($acc['account_name']); ?></td>
                                                <td>
                                                    <?php if ($acc['is_active']): ?>
                                                        <span class="badge badge-success">Attivo</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Disattivato</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form method="POST" style="margin:0;">
                                                        <input type="hidden" name="toggle_id" value="<?php echo $acc['id']; ?>">
                                                        <?php if ($acc['is_active']): ?>
                                                            <input type="hidden" name="new_status" value="0">
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-power-off"></i> Disattiva
                                                            </button>
                                                        <?php else: ?>
                                                            <input type="hidden" name="new_status" value="1">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check"></i> Attiva
                                                            </button>
                                                        <?php endif; ?>
                                                    </form>
                                                </td>
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
