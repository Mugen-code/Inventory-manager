
<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Transaction</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('transactions/store') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select" name="product_id" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>">
                                        <?= esc($product['name']) ?> (Stock: <?= $product['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Transaction Type</label>
                            <select class="form-select" name="type" required>
                                <option value="inbound">Inbound (Stock In)</option>
                                <option value="outbound">Outbound (Stock Out)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Record Transaction</button>
                            <a href="<?= base_url('transactions') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>