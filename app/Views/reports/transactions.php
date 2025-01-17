<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Transaction History</h2>
        <a href="<?= base_url('reports') ?>" class="btn btn-secondary">Back to Reports</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="<?= $startDate ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="<?= $endDate ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>User</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= esc($transaction['created_at']) ?></td>
                                <td><?= esc($transaction['product_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $transaction['type'] === 'inbound' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($transaction['type']) ?>
                                    </span>
                                </td>
                                <td><?= $transaction['quantity'] ?></td>
                                <td><?= esc($transaction['username']) ?></td>
                                <td><?= esc($transaction['notes']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>