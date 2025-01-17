<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Dashboard</h1>
        <div>
            <span class="text-muted">Welcome, <?= session()->get('username') ?></span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalProducts ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Categories Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalCategories ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $lowStockProducts ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Transactions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentTransactions)): ?>
                                    <?php foreach ($recentTransactions as $transaction): ?>
                                        <tr>
                                            <td><?= $transaction['id'] ?></td>
                                            <td><?= $transaction['product_name'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $transaction['type'] === 'inbound' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst($transaction['type']) ?>
                                                </span>
                                            </td>
                                            <td><?= $transaction['quantity'] ?></td>
                                            <td><?= $transaction['created_at'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No recent transactions</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($isAdmin): ?>
    <!-- Admin Only Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="<?= base_url('products/new') ?>" class="btn btn-primary btn-block mb-3">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('categories/new') ?>" class="btn btn-success btn-block mb-3">
                                <i class="fas fa-folder-plus"></i> Add Category
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('transactions/create') ?>" class="btn btn-info btn-block mb-3">
                                <i class="fas fa-exchange-alt"></i> New Transaction
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('reports') ?>" class="btn btn-secondary btn-block mb-3">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>