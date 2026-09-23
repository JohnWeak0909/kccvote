<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Elections - KEVS (KCC e-Voting System)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/theme-unified.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/burger-menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <style>
        .kev-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            min-height: 100dvh;
            height: 100dvh;
            background: rgba(0,0,0,0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 1rem;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .kev-modal-backdrop.open { display: flex; }
        .kev-modal {
            background: #0B2748;
            color: #ffffff;
            border-radius: 10px;
            max-width: 720px;
            width: 100%;
            padding: 1.25rem;
            box-shadow: 0 8px 32px rgba(15, 39, 68, 0.35);
            margin: auto;
            max-height: calc(100dvh - 2rem);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .kev-modal h4,
        .kev-modal h5 {
            color: #ffd699;
            margin-top: 0;
            margin-bottom: 0.75rem;
        }
        .kev-modal p {
            color: #b8c9d9;
        }
        .kev-modal .close {
            float: right;
            background: transparent;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #ffffff;
        }
        .kev-modal .form-group label {
            color: #ffd699;
            font-weight: 600;
            display: block;
            margin-bottom: 0.35rem;
        }
        .kev-modal .form-control {
            background: #163d5c;
            color: #e8f0f7;
            border: 2px solid #2d5a7b;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .kev-modal .form-control:focus {
            background: #1a4b70;
            border-color: #FCD34D;
            box-shadow: 0 0 0 4px rgba(252, 211, 77, 0.15);
            color: #e8f0f7;
            outline: none;
        }
        .kev-modal .form-control::placeholder {
            color: #7a96af;
        }
        .kev-modal .form-control option {
            background: #0f2744;
            color: #e8f0f7;
        }
        .kev-modal .btn-primary {
            background: #0B2748;
            color: #0f1f35;
            border: none;
            font-weight: 600;
            border-radius: 6px;
        }
        .kev-modal .alert-info {
            background: rgba(252, 211, 77, 0.15);
            color: #fff7d6;
            border: 1px solid rgba(252, 211, 77, 0.35);
        }
        .kev-modal .section-box {
            border: 1px solid #2d5a7b;
            background: rgba(15, 39, 68, 0.35);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .kev-modal .mini-row {
            display: flex;
            gap: 0.75rem;
            align-items: end;
            flex-wrap: wrap;
        }
        .kev-modal .mini-row .form-group {
            margin-bottom: 0;
            flex: 1;
            min-width: 180px;
        }

        @media (max-width: 768px) {
            .kev-modal {
                padding: 1rem;
                max-height: 85vh;
                overflow-y: auto;
            }
            .kev-modal .mini-row {
                flex-direction: column;
                align-items: stretch;
            }
            .kev-modal .mini-row .form-group {
                min-width: 100%;
            }
            .kev-modal .section-box {
                padding: 0.65rem;
            }
            .kev-modal .btn-primary {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .kev-modal-backdrop {
                padding: 0.5rem;
            }
            .kev-modal {
                padding: 0.75rem;
            }
            .kev-modal h4 {
                font-size: 1.1rem;
            }
            .kev-modal .close {
                font-size: 1.1rem;
            }
        }

        .election-table {
            width: 100%;
            border-collapse: collapse;
        }
        .election-table thead th {
            background: #0B2748;
            color: #f8fbff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid rgba(255,255,255,0.16);
        }
        .election-table th,
        .election-table td {
            white-space: normal;
            vertical-align: top;
        }
        .election-table .status-form {
            display: flex;
            gap: 0.35rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .election-table .status-select {
            min-width: 120px;
        }
        .election-table .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        @media (max-width: 900px) {
            .election-table thead {
                display: none;
            }
            .election-table,
            .election-table tbody,
            .election-table tr,
            .election-table td {
                display: block;
                width: 100%;
            }
            .election-table tr {
                border: 1px solid #2d5a7b;
                background: rgba(15, 39, 68, 0.35);
                border-radius: 8px;
                padding: 0.75rem;
                margin-bottom: 0.75rem;
                display: block;
                box-sizing: border-box;
            }
            .election-table td {
                border: none;
                padding: 0.45rem 0;
                display: block;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                word-break: break-word;
                overflow-wrap: anywhere;
                line-height: 1.45;
                white-space: normal;
            }
            .election-table td::before {
                content: attr(data-label);
                display: block;
                font-weight: 600;
                color: #ffd699;
                margin-bottom: 0.2rem;
                line-height: 1.3;
            }
            .election-table td:last-child {
                display: block;
            }
            .election-table .status-form {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                display: flex;
                gap: 0.4rem;
            }
            .election-table .status-select {
                min-width: 100%;
            }
            .election-table .action-buttons {
                flex-direction: column;
            }
            .election-table .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <script src="<?= base_url('js/burger-menu.js') ?>" defer></script>
    <script src="<?= base_url('js/main.js') ?>" defer></script>
</head>
<body class="dashboard-page">
    <?= view('admin/_sidebar') ?>
    <div class="admin-container">
        <main class="content">
            <header class="dashboard-header">
                <div class="header-content">
                    <h1 style="color: #FFFFFF;">Manage Elections</h1>
                    <p>Review and track configured election cycles.</p>
                </div>
                <div class="header-actions">
                    <a href="#" class="btn btn-primary" data-modal-target="#modal-add">Create Election Setup</a>
                </div>
            </header>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="dashboard-grid">
                <div class="dashboard-card" style="width: 100%;">
                    <div class="card-header"><h3>Election Records</h3></div>
                    <div class="card-content">
                        <?php if (empty($elections)): ?>
                            <p>No elections have been created yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped election-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($elections as $election): ?>
                                            <tr>
                                                <td data-label="#"><?= esc($election['id']) ?></td>
                                                <td data-label="Title"><?= esc($election['title']) ?></td>
                                                <td data-label="Start Time"><?= esc($election['start_time']) ?></td>
                                                <td data-label="End Time"><?= esc($election['end_time']) ?></td>
                                                <td data-label="Actions">
                                                    <div class="action-buttons">
                                                        <a href="<?= base_url('admin/elections/edit/' . $election['id']) ?>" class="btn btn-sm btn-primary" title="Edit election details (title, dates, access)"><i class="bi bi-pencil-square"></i> Edit</a>
                                                        <form action="<?= base_url('admin/elections/delete/' . $election['id']) ?>" method="post" class="delete-form">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this election?')" title="Delete election"><i class="bi bi-trash3"></i> Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Combined workflow modal -->
    <div id="modal-add" class="kev-modal-backdrop" aria-hidden="true">
        <div class="kev-modal" role="dialog" aria-modal="true" style="max-width: 720px; max-height: 90vh; overflow-y: auto;">
            <button class="close" data-modal-close>&times;</button>
            <h4 style="margin-top:0;">Create Election Setup</h4>
            <p style="margin-bottom:1rem; color:#b8c9d9;">Create the election and add a position and candidate for that election in one flow.</p>

            <form id="form-election-setup" action="<?= base_url('admin/elections/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="section-box">
                    <h5>Election Details</h5>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="datetime-local" name="start_time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="datetime-local" name="end_time" class="form-control">
                    </div>
                </div>

                <div class="section-box">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                        <h5 style="margin:0;">Positions</h5>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-position-row">+ Add Position</button>
                    </div>
                    <div id="position-rows"></div>
                </div>

                <div class="section-box">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                        <h5 style="margin:0;">Parties</h5>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-party-row">+ Add Party</button>
                    </div>
                    <div id="party-rows"></div>
                </div>

                <div class="section-box">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                        <h5 style="margin:0;">Candidates</h5>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-candidate-row">+ Add Candidate</button>
                    </div>
                    <div id="candidate-rows"></div>
                </div>

                <div id="setup-status" class="alert alert-info" style="display:none;"></div>

                <div style="text-align:right; margin-top:0.75rem;">
                    <button type="submit" class="btn btn-primary">Create Election Setup</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(targetSelector){
            const modal = document.querySelector(targetSelector);
            if (!modal) return;
            modal.classList.add('open');
        }
        function closeModal(modal){ modal.classList.remove('open'); }

        document.querySelectorAll('[data-modal-target]').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault();
                openModal(this.getAttribute('data-modal-target'));
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', function(){
                closeModal(this.closest('.kev-modal-backdrop'));
            });
        });

        document.querySelectorAll('.kev-modal-backdrop').forEach(backdrop => {
            backdrop.addEventListener('click', function(e){
                if (e.target === this) closeModal(this);
            });
        });

        (function(){
            const form = document.getElementById('form-election-setup');
            const statusBox = document.getElementById('setup-status');
            const positionRows = document.getElementById('position-rows');
            const partyRows = document.getElementById('party-rows');
            const candidateRows = document.getElementById('candidate-rows');
            const addPositionBtn = document.getElementById('add-position-row');
            const addPartyBtn = document.getElementById('add-party-row');
            const addCandidateBtn = document.getElementById('add-candidate-row');
            if (!form || !statusBox || !positionRows || !partyRows || !candidateRows) return;

            const setStatus = (message, kind = 'info') => {
                statusBox.style.display = 'block';
                statusBox.className = 'alert alert-' + kind;
                statusBox.textContent = message;
            };

            function getPositionOptions() {
                const options = [];
                positionRows.querySelectorAll('input[name^="positions"][name$="[title]"]').forEach((input) => {
                    const value = input.value.trim();
                    if (value) {
                        options.push({ value: value, label: value });
                    }
                });
                return options;
            }

            function getPartyOptions() {
                const options = [];
                partyRows.querySelectorAll('input[name^="parties"][name$="[name]"]').forEach((input) => {
                    const value = input.value.trim();
                    if (value) {
                        options.push({ value: value, label: value });
                    }
                });
                return options;
            }

            function refreshCandidatePositionOptions() {
                const options = getPositionOptions();
                candidateRows.querySelectorAll('select[name^="candidates"][name$="[position_id]"]').forEach((select) => {
                    const currentValue = select.value;
                    const selectedValue = currentValue && options.some((opt) => opt.value === currentValue) ? currentValue : '';
                    select.innerHTML = '<option value="">Select a position</option>' + options.map((opt) => `<option value="${opt.value}" ${opt.value === selectedValue ? 'selected' : ''}>${opt.label}</option>`).join('');
                    select.value = selectedValue;
                });
            }

            function refreshCandidatePartyOptions() {
                const options = getPartyOptions();
                candidateRows.querySelectorAll('select[name^="candidates"][name$="[party_id]"]').forEach((select) => {
                    const currentValue = select.value;
                    const selectedValue = currentValue && options.some((opt) => opt.value === currentValue) ? currentValue : '';
                    select.innerHTML = '<option value="">Independent</option>' + options.map((opt) => `<option value="${opt.value}" ${opt.value === selectedValue ? 'selected' : ''}>${opt.label}</option>`).join('');
                    select.value = selectedValue;
                });
            }

            function addPositionRow() {
                const index = positionRows.children.length;
                const row = document.createElement('div');
                row.className = 'section-box';
                row.style.marginBottom = '0.5rem';
                row.style.padding = '0.6rem';
                row.innerHTML = `
                    <div class="mini-row">
                        <div class="form-group">
                            <label>Position Name</label>
                            <input type="text" name="positions[${index}][title]" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Votes Required</label>
                            <input type="number" name="positions[${index}][votes_required]" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                `;
                positionRows.appendChild(row);
                row.querySelector('input[name$="[title]"]').addEventListener('input', refreshCandidatePositionOptions);
                refreshCandidatePositionOptions();
            }

            function addPartyRow() {
                const index = partyRows.children.length;
                const row = document.createElement('div');
                row.className = 'section-box';
                row.style.marginBottom = '0.5rem';
                row.style.padding = '0.6rem';
                row.innerHTML = `
                    <div class="form-group">
                        <label>Party Name</label>
                        <input type="text" name="parties[${index}][name]" class="form-control" required>
                    </div>
                `;
                partyRows.appendChild(row);
                row.querySelector('input[name$="[name]"]').addEventListener('input', refreshCandidatePartyOptions);
                refreshCandidatePartyOptions();
            }

            function addCandidateRow() {
                const index = candidateRows.children.length;
                const row = document.createElement('div');
                row.className = 'section-box';
                row.style.marginBottom = '0.5rem';
                row.style.padding = '0.6rem';
                row.innerHTML = `
                    <div class="mini-row">
                        <div class="form-group">
                            <label>Candidate Name</label>
                            <input type="text" name="candidates[${index}][full_name]" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Position</label>
                            <select name="candidates[${index}][position_id]" class="form-control">
                                <option value="">Select a position</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Party</label>
                            <select name="candidates[${index}][party_id]" class="form-control">
                                <option value="">Independent</option>
                            </select>
                        </div>
                    </div>
                `;
                candidateRows.appendChild(row);
                refreshCandidatePositionOptions();
                refreshCandidatePartyOptions();
            }

            addPositionBtn && addPositionBtn.addEventListener('click', addPositionRow);
            addPartyBtn && addPartyBtn.addEventListener('click', addPartyRow);
            addCandidateBtn && addCandidateBtn.addEventListener('click', addCandidateRow);
            addPositionRow();
            addPartyRow();
            addCandidateRow();

            form.addEventListener('submit', function(e){
                e.preventDefault();

                const csrfInput = form.querySelector('input[name^="csrf"]');
                const csrfName = csrfInput ? csrfInput.name : '';
                const csrfValue = csrfInput ? csrfInput.value : '';
                const electionData = new FormData(form);

                setStatus('Creating election...');

                fetch(form.action, { method: 'POST', body: electionData, credentials: 'same-origin' })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!data.success || !data.election_id) {
                            setStatus(data.message || 'Unable to create election.', 'danger');
                            return;
                        }

                        const newElectionId = data.election_id;
                        const redirectTo = '/admin/elections';

                        const positions = [];
                        const positionTitleToId = {};
                        for (const [key, value] of electionData.entries()) {
                            const match = key.match(/^positions\[(\d+)\]\[(title|votes_required)\]$/);
                            if (!match) continue;
                            const idx = match[1];
                            const field = match[2];
                            if (!positions[idx]) positions[idx] = {};
                            positions[idx][field] = value;
                        }

                        for (const item of positions.filter(Boolean)) {
                            if (!item.title) continue;
                            setStatus('Saving positions...');
                            const positionData = new FormData();
                            positionData.append('title', item.title);
                            positionData.append('sort_order', '0');
                            positionData.append('description', '');
                            positionData.append('votes_required', item.votes_required || '1');
                            positionData.append('redirect_to', redirectTo);
                            if (csrfName && csrfValue) {
                                positionData.append(csrfName, csrfValue);
                            }
                            const res = await fetch('<?= base_url('admin/positions/store') ?>', { method: 'POST', body: positionData, credentials: 'same-origin' });
                            const positionResult = await res.json().catch(() => ({}));
                            if (positionResult.position_id) {
                                positionTitleToId[item.title] = positionResult.position_id;
                            }
                        }

                        const parties = [];
                        const partyNameToId = {};
                        for (const [key, value] of electionData.entries()) {
                            const match = key.match(/^parties\[(\d+)\]\[(name)\]$/);
                            if (!match) continue;
                            const idx = match[1];
                            const field = match[2];
                            if (!parties[idx]) parties[idx] = {};
                            parties[idx][field] = value;
                        }

                        for (const item of parties.filter(Boolean)) {
                            if (!item.name) continue;
                            setStatus('Saving parties...');
                            const partyData = new FormData();
                            partyData.append('name', item.name);
                            partyData.append('election_id', newElectionId);
                            partyData.append('redirect_to', redirectTo);
                            if (csrfName && csrfValue) {
                                partyData.append(csrfName, csrfValue);
                            }
                            const res = await fetch('<?= base_url('admin/parties/store') ?>', { method: 'POST', body: partyData, credentials: 'same-origin' });
                            const partyResult = await res.json().catch(() => ({}));
                            if (partyResult.party_id) {
                                partyNameToId[item.name] = partyResult.party_id;
                            }
                        }

                        const candidates = [];
                        for (const [key, value] of electionData.entries()) {
                            const match = key.match(/^candidates\[(\d+)\]\[(full_name|position_id|party_id)\]$/);
                            if (!match) continue;
                            const idx = match[1];
                            const field = match[2];
                            if (!candidates[idx]) candidates[idx] = {};
                            candidates[idx][field] = value;
                        }

                        for (const item of candidates.filter(Boolean)) {
                            if (!item.full_name) continue;
                            setStatus('Saving candidates...');
                            const candidateData = new FormData();
                            candidateData.append('full_name', item.full_name);
                            const selectedPositionTitle = item.position_id || '';
                            const resolvedPositionId = selectedPositionTitle ? (positionTitleToId[selectedPositionTitle] || '') : '';
                            candidateData.append('position_id', resolvedPositionId);
                            const selectedPartyName = item.party_id || '';
                            const resolvedPartyId = selectedPartyName ? (partyNameToId[selectedPartyName] || '') : '';
                            candidateData.append('party_id', resolvedPartyId);
                            candidateData.append('election_id', newElectionId);
                            candidateData.append('redirect_to', redirectTo);
                            if (csrfName && csrfValue) {
                                candidateData.append(csrfName, csrfValue);
                            }
                            await fetch('<?= base_url('admin/candidates/store') ?>', { method: 'POST', body: candidateData, credentials: 'same-origin' });
                        }

                        setStatus('Election setup completed successfully.', 'success');
                        window.setTimeout(() => window.location = redirectTo, 800);
                    })
                    .catch(() => {
                        setStatus('Something went wrong. Please try again.', 'danger');
                    });
            });
        })();
    </script>
</body>
</html>

