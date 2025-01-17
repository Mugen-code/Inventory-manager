<!-- app/Views/products/create.php -->
<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Add New Product</h3>
        </div>
        <div class="card-body">
            <?php if(session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach(session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('products/create') ?>" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" class="form-control" id="sku" name="sku" value="<?= old('sku') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= old('price') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="<?= old('stock') ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Create Product</button>
                <a href="<?= site_url('products') ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>