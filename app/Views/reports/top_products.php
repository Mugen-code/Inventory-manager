<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Top Selling Products</h2>
        <a href="<?= base_url('reports') ?>" class="btn btn-secondary">Back to Reports</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Total Sales</th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= esc($product['name']) ?></td>
                                <td><?= esc($product['category_name']) ?></td>
                                <td><?= $product['transaction_count'] ?></td>
                                <td><?= $product['stock'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>