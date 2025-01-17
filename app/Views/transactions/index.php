<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container">
    <h2 class="mb-4">Transactions</h2>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Transactions</h5>
            <a href="<?= base_url('transactions/create') ?>" class="btn btn-primary">New Transaction</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>User</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($transactions)): ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= esc($transaction['id']) ?></td>
                                <td><?= esc($transaction['product_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $transaction['type'] === 'inbound' ? 'success' : 'danger' ?>">
                                        <?= ucfirst(esc($transaction['type'])) ?>
                                    </span>
                                </td>
                                <td><?= esc($transaction['quantity']) ?></td>
                                <td><?= esc($transaction['username']) ?></td>
                                <td><?= esc($transaction['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No transactions found</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>