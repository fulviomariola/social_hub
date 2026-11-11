<?php
require_once 'db.php';

// Gestione dell'invio del form per un nuovo post/condivisione
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $source_url = trim($_POST['source_url']);
    $platforms = isset($_POST['platforms']) ? $_POST['platforms'] : [];
    
    if (!empty($title) && !empty($platforms)) {
        // Convertiamo l'array delle piattaforme selezionate in una stringa separata da virgole
        $platforms_str = implode(',', $platforms);
        
        $stmt = $pdo->prepare("INSERT INTO posts_log (title, source_url, platforms_targeted, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$title, $source_url, $platforms_str]);
        
        $success_message = "Contenuto aggiunto con successo alla coda di pubblicazione!";
    }
    
}

// Recuperiamo i social attivi per mostrarli come opzioni nel form
$stmt = $pdo->query("SELECT * FROM social_accounts WHERE is_active = 1 ORDER BY platform_slug ASC");
$active_accounts = $stmt->fetchAll();

// Recuperiamo lo storico dei post inseriti
posts_query:
$posts_stmt = $pdo->query("SELECT * FROM posts_log ORDER BY created_at DESC");
$posts = $posts_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Social Media Hub - Gestione Contenuti</title>

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

            <li class="nav-item active">
                <a class="nav-link" href="posts.php">
                    <i class="fas fa-fw fa-share-alt"></i>
                    <span>Gestione Post & Link</span></a>
            </li>

            <li class="nav-item">
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
                    <span class="navbar-brand mb-0 h1">Pubblicazione e Cross-Posting</span>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Nuovo Contenuto / Condivisione</h1>
                    </div>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form di Inserimento -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Condividi da YouTube verso altri canali</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label for="title">Titolo del Contenuto / Descrizione</label>
                                    <input type="text" class="form-control" id="title" name="title" required placeholder="Es. Uscito ora il nuovo video tutorial!">
                                </div>
                                <div class="form-group">
                                    <label for="source_url">Link Sorgente (es. YouTube URL)</label>
                                    <input type="url" class="form-control" id="source_url" name="source_url" placeholder="https://www.youtube.com/watch?v=...">
                                </div>
                                <div class="form-group">
                                    <label>Seleziona i canali di destinazione:</label>
                                    <div class="row">
                                        <?php foreach ($active_accounts as $acc): ?>
                                            <div class="col-md-3 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="platform_<?php echo $acc['id']; ?>" name="platforms[]" value="<?php echo $acc['platform_slug']; ?>">
                                                    <label class="custom-control-label text-capitalize" for="platform_<?php echo $acc['id']; ?>">
                                                        <?php echo htmlspecialchars($acc['account_name']); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Registra in Coda
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabella Storico Post -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Storico Contenuti Inseriti</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Titolo</th>
                                            <th>Link Sorgente</th>
                                            <th>Canali Destinazione</th>
                                            <th>Stato</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($posts as $post): ?>
                                            <tr>
                                                <td><?php echo $post['created_at']; ?></td>
                                                <td><?php echo htmlspecialchars($post['title']); ?></td>
                                                <td>
                                                    <?php if (!empty($post['source_url'])): ?>
                                                        <a href="<?php echo htmlspecialchars($post['source_url']); ?>" target="_blank">Apri Link</a>
                                                    <?php else: ?>
                                                        Nessuno
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="badge badge-info"><?php echo htmlspecialchars($post['platforms_targeted']); ?></span></td>
                                                <td>
                                                    <span class="badge badge-warning"><?php echo htmlspecialchars($post['status']); ?></span>
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