<?php
$redirectTo = \Config\Services::request()->getGet('redirect_to');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode === 'edit' ? 'Edit Position' : 'Add Position' ?> - KEVS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/theme-unified.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/burger-menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <script src="<?= base_url('js/burger-menu.js') ?>" defer></script>
    <script src="<?= base_url('js/main.js') ?>" defer></script>
</head>
<body class="dashboard-page">
    <?= view('admin/_sidebar') ?>
    <div class="admin-container">
        <main class="content">
            <header class="dashboard-header">
                <div class="header-content">
                    <h1 style="color: #FFFFFF;"><?= $mode === 'edit' ? 'Edit Position' : 'Add Position' ?></h1>
                    <p><?= $mode === 'edit' ? 'Update position details.' : 'Create a new ballot position.' ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?= $redirectTo ?? base_url('admin/positions') ?>" class="btn btn-secondary">Back to <?= $redirectTo ? 'Election' : 'Positions' ?></a>
                </div>
            </header>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="dashboard-card">
                <div class="card-header"><h3>Position Form</h3></div>
                <div class="card-content">
                    <form action="<?= base_url($mode === 'edit' ? 'admin/positions/update/' . $position['id'] : 'admin/positions/store') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if ($redirectTo): ?>
                            <input type="hidden" name="redirect_to" value="<?= esc($redirectTo) ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?= esc(old('title', $position['title'] ?? '')) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="sort_order">Sort Order</label>
                            <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= esc(old('sort_order', $position['sort_order'] ?? 0)) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="votes_required">Votes Required</label>
                            <input type="number" id="votes_required" name="votes_required" class="form-control" value="<?= esc(old('votes_required', $position['votes_required'] ?? 1)) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control"><?= esc(old('description', $position['description'] ?? '')) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary"><?= $mode === 'edit' ? 'Update Position' : 'Save Position' ?></button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

