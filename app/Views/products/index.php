<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
     <div class="d-flex justify-content-between align-items-center mb-4">
       <h2><?= lang('products') ?></h2>
       <a href="<?= site_url('products/new') ?>" class="btn btn-primary"><?= lang('Add product') ?></a>
     </div>

    <!-- Add Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= base_url('products') ?>" method="get">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" 
                               placeholder="<?= lang('Search by name or SKU') ?>" 
                               value="<?= $search ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="category">
                            <option value=""><?= lang('All categories') ?></option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= (isset($selectedCategory) && $selectedCategory == $category['id']) ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><?= lang('search') ?></button>
                    </div>
                    <?php if(isset($search) || isset($selectedCategory)): ?>
                        <div class="col-md-2">
                            <a href="<?= base_url('products') ?>" class="btn btn-secondary w-100"><?= lang('clear') ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if(session()->getFlashdata('message')): ?>
        <div class="alert alert-success">
            <?= lang((string) session()->getFlashdata('message')) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <!-- Desktop view -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?= lang('sku') ?></th>
                            <th><?= lang('name') ?></th>
                            <th><?= lang('category') ?></th>
                            <th><?= lang('price') ?></th>
                            <th><?= lang('stock') ?></th>
                            <th><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                            <tr data-product-id="<?= $product['id'] ?>">
                                <td><?= esc($product['sku']) ?></td>
                                <td><?= esc($product['name']) ?></td>
                                <td><?= esc($product['category_name']) ?></td>
                                <td><?= number_format($product['price'], 2) ?></td>
                                <td class="stock-value"><?= $product['stock'] ?></td>
                                <td>
                                    <a href="<?= site_url('products/edit/' . $product['id']) ?>" class="btn btn-sm btn-info"><?= lang('edit') ?></a>
                                    <a href="<?= site_url('products/delete/' . $product['id']) ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('<?= lang('Are you sure you want to delete this product?') ?>');">
                                       <?= lang('delete') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile view -->
            <div class="d-md-none">
                <?php foreach($products as $product): ?>
                    <div class="card mb-3" data-product-id="<?= $product['id'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($product['name']) ?></h5>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted"><?= lang('sku') ?>:</small><br>
                                    <?= esc($product['sku']) ?>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted"><?= lang('category') ?>:</small><br>
                                    <?= esc($product['category_name']) ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted"><?= lang('price') ?>:</small><br>
                                    $<?= number_format($product['price'], 2) ?>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted"><?= lang('stock') ?>:</small><br>
                                    <span class="stock-value"><?= $product['stock'] ?></span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= site_url('products/edit/' . $product['id']) ?>" 
                                   class="btn btn-info flex-grow-1"><?= lang('edit') ?></a>
                                <a href="<?= site_url('products/delete/' . $product['id']) ?>" 
                                   class="btn btn-danger flex-grow-1"
                                   onclick="return confirm('<?= lang('confirm_delete') ?>');"><?= lang('delete') ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<style>
.stock-updated {
    animation: highlightStock 2s ease-in-out;
}

@keyframes highlightStock {
    0% { background-color: transparent; }
    50% { background-color: #ffd700; }
    100% { background-color: transparent; }
}
</style>
<?= $this->endSection() ?>