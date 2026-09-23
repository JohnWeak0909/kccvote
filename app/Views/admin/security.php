<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Access Settings - KEVS (KCC e-Voting System)</title>
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
                    <h1 style="color: #FFFFFF;">Campus Access Settings</h1>
                    <p>Set the campus access radius and campus center coordinates.</p>
                </div>
            </header>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="dashboard-card" style="padding: 1.5rem; max-width: 720px;">
                <form action="<?= base_url('admin/security/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="campus_latitude">Campus Latitude</label>
                        <input type="text" id="campus_latitude" name="campus_latitude" class="form-control" value="<?= esc($campus_latitude) ?>" placeholder="e.g. 14.6500">
                        <small class="text-muted">Enter the latitude of the campus center.</small>
                    </div>
                    <div class="form-group">
                        <label for="campus_longitude">Campus Longitude</label>
                        <input type="text" id="campus_longitude" name="campus_longitude" class="form-control" value="<?= esc($campus_longitude) ?>" placeholder="e.g. 121.0700">
                        <small class="text-muted">Enter the longitude of the campus center.</small>
                    </div>
                    <div class="form-group">
                        <label for="campus_access_radius">Allowed Radius (meters)</label>
                        <input type="number" id="campus_access_radius" name="campus_access_radius" class="form-control" value="<?= esc($campus_access_radius) ?>" min="0" step="1" placeholder="e.g. 500">
                        <small class="text-muted">Users can only access the system when their device is inside this radius from campus center.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Campus Access Settings</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

