<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Edit Category</h3>
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

            <form action="<?= site_url('categories/update/' . $category['id']) ?>" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= old('name', $category['name']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="3"><?= old('description', $category['description']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="<?= site_url('categories') ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>