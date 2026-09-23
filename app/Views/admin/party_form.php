<?php
$redirectTo = \Config\Services::request()->getGet('redirect_to');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode === 'edit' ? 'Edit Party' : 'Add Party' ?> - KEVS</title>
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
                    <h1 style="color: #FFFFFF;"><?= $mode === 'edit' ? 'Edit Party' : 'Add Party' ?></h1>
                    <p><?= $mode === 'edit' ? 'Update party details.' : 'Create a new party record.' ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?= $redirectTo ?? base_url('admin/parties') ?>" class="btn btn-secondary">Back to <?= $redirectTo ? 'Election' : 'Parties' ?></a>
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
                <div class="card-header"><h3>Party Form</h3></div>
                <div class="card-content">
                    <form action="<?= base_url($mode === 'edit' ? 'admin/parties/update/' . $party['id'] : 'admin/parties/store') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if ($redirectTo): ?>
                            <input type="hidden" name="redirect_to" value="<?= esc($redirectTo) ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="name">Party Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= esc(old('name', $party['name'] ?? '')) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="election_id">Election</label>
                            <select id="election_id" name="election_id" class="form-control">
                                <option value="">-- Select an election --</option>
                                <?php foreach ($elections as $election): ?>
                                    <option value="<?= esc($election['id']) ?>" <?= (old('election_id', $party['election_id'] ?? '') == $election['id']) ? 'selected' : '' ?>>
                                        <?= esc($election['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary"><?= $mode === 'edit' ? 'Update Party' : 'Save Party' ?></button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

