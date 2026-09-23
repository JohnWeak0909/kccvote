<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Positions - KEVS (KCC e-Voting System)</title>
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
                    <h1 style="color: #FFFFFF;">Manage Positions</h1>
                    <p>Create, update, and remove ballot positions.</p>
                </div>
                <div class="header-actions">
                    <a href="<?= base_url('admin/positions/create') ?>" class="btn btn-primary">Add Position</a>
                </div>
            </header>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="dashboard-card">
                <div class="card-header"><h3>Positions</h3></div>
                <div class="card-content">
                    <?php if (empty($positions)): ?>
                        <p>No positions have been created yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Sort Order</th>
                                        <th>Votes Required</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($positions as $position): ?>
                                        <tr>
                                            <td><?= esc($position['id']) ?></td>
                                            <td><?= esc($position['title']) ?></td>
                                            <td><?= esc($position['sort_order']) ?></td>
                                            <td><?= esc($position['votes_required'] ?? 1) ?></td>
                                            <td><?= esc($position['description']) ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?= base_url('admin/positions/edit/' . $position['id']) ?>" class="btn btn-sm btn-warning" title="Edit position"><i class="bi bi-pencil-square"></i> Edit</a>
                                                    <form action="<?= base_url('admin/positions/delete/' . $position['id']) ?>" method="post" class="delete-form">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this position?')" title="Delete position"><i class="bi bi-trash3"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

