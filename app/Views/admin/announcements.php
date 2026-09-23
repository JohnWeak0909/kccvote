<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Announcements - KEVS (KCC e-Voting System)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/theme-unified.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/burger-menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <style>
        .announcement-page {
            padding: 1.5rem 0;
            color: #fff;
        }
        .announcement-grid {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 1.5rem;
        }
        .announcement-card,
        .announcement-form {
            background: rgba(15, 39, 68, 0.82);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 1.25rem;
            box-shadow: 0 18px 48px rgba(0,0,0,0.18);
        }
        .announcement-card .card-header,
        .announcement-form .card-header {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .announcement-card table {
            width: 100%;
            border-collapse: collapse;
            background: #0B2748;
            border-radius: 12px;
            overflow: hidden;
        }
        .announcement-card th,
        .announcement-card td {
            padding: 0.85rem 0.65rem;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            text-align: left;
        }
        .announcement-card th {
            background: #0B2748;
            color: #f8fbff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .announcement-card td {
            color: #f3f7ff;
            background: rgba(7, 18, 58, 0.55);
        }
        .announcement-card tr:last-child td {
            border-bottom: none;
        }
        .announcement-card .badge {
            display: inline-flex;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            background: rgba(96,165,250,0.15);
            color: #a5d8ff;
            font-size: 0.83rem;
            font-weight: 600;
        }
        .announcement-form label {
            display: block;
            margin-bottom: 0.35rem;
            color: #cbd6ea;
            font-weight: 600;
        }
        .announcement-form input,
        .announcement-form textarea,
        .announcement-form select {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            color: #f8fbff;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            margin-bottom: 1rem;
            font-family: inherit;
            font-size: 0.95rem;
        }
        .announcement-form textarea {
            min-height: 170px;
            resize: vertical;
        }
        .announcement-form .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            align-items: center;
        }
        .announcement-form .btn {
            min-width: 140px;
        }
        .announcement-alert {
            background: rgba(34, 65, 114, 0.85);
            border: 1px solid rgba(96, 165, 250, 0.18);
            padding: 0.95rem 1rem;
            border-radius: 14px;
            margin-bottom: 1.25rem;
            color: #e7f3ff;
        }
        .announcement-alert.success {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.24);
            color: #d1fae5;
        }
        .announcement-alert.error {
            background: rgba(244, 63, 94, 0.12);
            border-color: rgba(244, 63, 94, 0.24);
            color: #ffe4e8;
        }
        @media (max-width: 960px) {
            .announcement-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="dashboard-page">
    <?= view('admin/_sidebar') ?>
    <div class="admin-container">
        <main class="content announcement-page">
            <header class="dashboard-header">
                <div class="header-content">
                    <h1>Announcement Management</h1>
                    <p>Publish and manage homepage announcements for students and staff.</p>
                </div>
            </header>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="announcement-alert success"><?= htmlspecialchars(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="announcement-alert error"><?= htmlspecialchars(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="announcement-grid">
                <section class="announcement-form">
                    <div class="card-header">
                        <div>
                            <h3><?= isset($announcement) ? 'Edit Announcement' : 'Create Announcement' ?></h3>
                            <p style="margin:0; color:#a5c3e2;">Add a new announcement that appears on the public homepage.</p>
                        </div>
                    </div>
                    <form method="post" action="<?= isset($announcement) ? base_url('admin/announcements/update/' . $announcement['id']) : base_url('admin/announcements/store') ?>" enctype="multipart/form-data">
                        <label for="title">Title</label>
                        <input id="title" name="title" type="text" placeholder="Announcement title" value="<?= isset($announcement) ? htmlspecialchars($announcement['title']) : '' ?>" required>

                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Announcement message" required><?= isset($announcement) ? htmlspecialchars($announcement['message']) : '' ?></textarea>

                        <label for="image">Announcement Image</label>
                        <input id="image" name="image" type="file" accept="image/*">
                        <p style="margin:-0.5rem 0 1rem; color:#9bb7d5; font-size:0.9rem;">Upload any image size. It will be displayed fully and centered in the homepage hero area.</p>

                        <label for="is_active">Status</label>
                        <select id="is_active" name="is_active">
                            <option value="1" <?= isset($announcement) && $announcement['is_active'] == 1 ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= isset($announcement) && $announcement['is_active'] == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>

                        <div class="form-actions">
                            <button class="btn btn-primary" type="submit"><?= isset($announcement) ? 'Save Announcement' : 'Post Announcement' ?></button>
                            <?php if (isset($announcement)) : ?>
                                <a class="btn btn-outline" href="<?= base_url('admin/announcements') ?>">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </section>

                <section class="announcement-card">
                    <div class="card-header">
                        <h3>Existing Announcements</h3>
                    </div>
                    <?php if (!empty($announcements)) : ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($announcements as $item) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['title']) ?></td>
                                        <td><span class="badge"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                                        <td><?= htmlspecialchars($item['created_at'] ?? '') ?></td>
                                        <td>
                                            <a class="btn btn-outline" href="<?= base_url('admin/announcements/edit/' . $item['id']) ?>">Edit</a>
                                            <form method="post" action="<?= base_url('admin/announcements/delete/' . $item['id']) ?>" style="display:inline-block; margin:0;">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this announcement?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p>No announcements yet. Use the form to create the first announcement.</p>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
