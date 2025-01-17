<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Low Stock Report</h2>
        <a href="<?= base_url('reports') ?>" class="btn btn-secondary">Back to Reports</a>
    </div>

    <div class="card">
        <div class="card-header">
            <form class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="col-form-label">Stock Threshold:</label>
                </div>
                <div class="col-auto">
                    <input type="number" name="threshold" class="form-control" 
                           value="<?= $threshold ?>" min="1">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= esc($product['name']) ?></td>
                                <td><?= esc($product['sku']) ?></td>
                                <td><?= esc($product['category_name']) ?></td>
                                <td>
                                    <span class="badge bg-danger"><?= $product['stock'] ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('transactions/create?product_id=' . $product['id']) ?>" 
                                       class="btn btn-sm btn-primary">
                                        Add Stock
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>