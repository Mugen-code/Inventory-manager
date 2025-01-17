<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2 class="mb-4">Reports Dashboard</h2>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="<?= base_url('reports/low-stock') ?>" class="list-group-item list-group-item-action">
                            Low Stock Report
                        </a>
                        <a href="<?= base_url('reports/transactions') ?>" class="list-group-item list-group-item-action">
                            Transaction History
                        </a>
                        <a href="<?= base_url('reports/top-products') ?>" class="list-group-item list-group-item-action">
                            Top Selling Products
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Low Stock Alert</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockProducts as $product): ?>
                                    <tr>
                                        <td><?= esc($product['name']) ?></td>
                                        <td><?= esc($product['category_name']) ?></td>
                                        <td>
                                            <span class="badge bg-danger"><?= $product['stock'] ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Selling Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Total Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topSellingProducts as $product): ?>
                                    <tr>
                                        <td><?= esc($product['name']) ?></td>
                                        <td><?= esc($product['category_name']) ?></td>
                                        <td><?= $product['transaction_count'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>