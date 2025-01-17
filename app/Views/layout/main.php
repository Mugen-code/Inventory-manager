<!-- app/Views/layouts/main.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventory Management System' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url() ?>">IMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('products') ?>">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('categories') ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('transactions') ?>">Transactions</a>
                    </li>
                    <?php if (session()->get('role') === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('users') ?>">Users</a>
                    </li>
                    <?php endif; ?>
                    <?php if(session()->get('role') === 'admin'): ?>
                     <li class="nav-item">
                       <a class="nav-link" href="<?= base_url('reports') ?>">Reports</a>
                     </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('logged_in')): ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?= session()->get('username') ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/logout') ?>">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/login') ?>">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-<?= session()->getFlashdata('type') ?> alert-dismissible fade show">
                <?= session()->getFlashdata('message') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">Inventory Management System © <?= date('Y') ?></span>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>