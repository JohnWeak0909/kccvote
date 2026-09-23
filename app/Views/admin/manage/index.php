<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>
<div class="page-header">
    <div class="page-title-group">
        <h1 class="page-title">Department &amp; Course Management</h1>
        <p class="page-copy">Manage departments and the courses available under each department.</p>
    </div>
</div>

<div style="display:grid; grid-template-columns: minmax(320px, 1.05fr) minmax(420px, 1.35fr); gap: 20px; margin-top: 20px;">
    <section class="card" style="min-height: 420px;">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
            <span class="card-title">Departments</span>
            <button type="button" id="addDeptBtn" class="btn btn-primary" onclick="openDepartmentModal('add')">+ Add Department</button>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 14px;">
                <input id="deptSearch" type="search" class="form-control" placeholder="Search department..." />
            </div>
            <div class="table-responsive">
                <table class="student-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Courses</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="departmentsTableBody"></tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="card" style="min-height: 420px;">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
            <span class="card-title">Courses</span>
            <button type="button" id="addCourseBtn" class="btn btn-primary" onclick="openCourseModal('add')">+ Add Course</button>
        </div>
        <div class="card-body">
            <div style="display:flex; gap:10px; margin-bottom: 14px; flex-wrap:wrap;">
                <input id="courseSearch" type="search" class="form-control" placeholder="Search course..." style="flex:1; min-width: 180px;" />
                <select id="courseDepartmentFilter" class="form-control" style="min-width: 180px;">
                    <option value="">All Departments</option>
                </select>
            </div>
            <div class="table-responsive">
                <table class="student-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="coursesTableBody"></tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div id="departmentModal" class="modal" style="display:none;">
    <div class="modal-content card register-modal" data-draggable-modal="true" style="max-width: 640px; width: min(92vw, 640px); margin: 8vh auto; padding: 22px 22px 18px; border-radius: 18px; border: 1px solid rgba(15, 23, 42, 0.08); box-shadow: 0 22px 60px rgba(15, 23, 42, 0.18); background: #fff; position: relative; cursor: default;">
        <h3 id="departmentModalTitle" data-drag-handle="true" style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; cursor: move; user-select: none; touch-action: none;">Add Department</h3>
        <form id="departmentForm" method="post" enctype="multipart/form-data" style="margin-top: 16px;">
            <?= csrf_field() ?>
            <div style="margin-top:12px;">
                <label>Department Name</label>
                <input id="department_name" name="department_name" class="form-control" required>
            </div>
            <div style="margin-top:12px;">
                <label>Status</label>
                <select id="department_status" name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div style="margin-top:16px; text-align:right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" id="departmentCancel" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Department</button>
            </div>
        </form>
    </div>
</div>

<div id="courseModal" class="modal" style="display:none;">
    <div class="modal-content card register-modal" data-draggable-modal="true" style="max-width: 640px; width: min(92vw, 640px); margin: 8vh auto; padding: 22px 22px 18px; border-radius: 18px; border: 1px solid rgba(15, 23, 42, 0.08); box-shadow: 0 22px 60px rgba(15, 23, 42, 0.18); background: #fff; position: relative; cursor: default;">
        <h3 id="courseModalTitle" data-drag-handle="true" style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; cursor: move; user-select: none; touch-action: none;">Add Course</h3>
        <form id="courseForm" method="post" enctype="multipart/form-data" style="margin-top: 16px;">
            <?= csrf_field() ?>
            <div style="margin-top:12px;">
                <label>Course Name</label>
                <input id="course_name" name="course_name" class="form-control" placeholder="Enter course name" required>
            </div>
            <div style="margin-top:12px;">
                <label>Department</label>
                <select id="course_department" name="department_id" class="form-control" required>
                    <option value="">Select Department</option>
                </select>
            </div>
            <div style="margin-top:12px;">
                <label>Status</label>
                <select id="course_status" name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div style="margin-top:16px; text-align:right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" id="courseCancel" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Course</button>
            </div>
        </form>
    </div>
</div>

<script>
const deptApi = '<?= base_url('admin/departments/all') ?>';
const deptListApi = '<?= base_url('admin/departments/list') ?>';
const courseApi = '<?= base_url('admin/courses/all') ?>';
let departmentList = [];
let courseList = [];

async function fetchJson(url) {
    const res = await fetch(url);
    return res.ok ? res.json() : [];
}

function departmentStatusChip(status) {
    const isActive = String(status).toLowerCase() === 'active';
    return `<span class="badge ${isActive ? 'badge-success' : 'badge-warning'}">${status || 'Inactive'}</span>`;
}

function courseStatusChip(status) {
    const isActive = String(status).toLowerCase() === 'active';
    return `<span class="badge ${isActive ? 'badge-success' : 'badge-warning'}">${status || 'Inactive'}</span>`;
}

async function loadDepartmentManagement() {
    departmentList = await fetchJson(deptApi);
    const filter = document.getElementById('deptSearch').value.trim().toLowerCase();
    const tbody = document.getElementById('departmentsTableBody');
    const rows = departmentList.filter(dept => {
        const text = `${dept.department_code || ''} ${dept.department_name || ''}`.toLowerCase();
        return text.includes(filter);
    });

    tbody.innerHTML = rows.length ? rows.map(dept => `
        <tr>
            <td>${dept.department_code || '—'}</td>
            <td>${dept.department_name || '—'}</td>
            <td>${Number(dept.course_count || 0)} ${Number(dept.course_count || 0) === 1 ? 'Course' : 'Courses'}</td>
            <td>${departmentStatusChip(dept.status)}</td>
            <td>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="button" class="btn btn-sm btn-secondary" data-action="dept-edit" data-id="${dept.id}">Edit</button>
                    <button type="button" class="btn btn-sm btn-warning" data-action="dept-toggle" data-id="${dept.id}">${dept.status === 'Active' ? 'Deactivate' : 'Activate'}</button>
                    <button type="button" class="btn btn-sm btn-danger" data-action="dept-delete" data-id="${dept.id}">Delete</button>
                </div>
            </td>
        </tr>
    `).join('') : '<tr><td colspan="5">No departments found.</td></tr>';

    const deptSelect = document.getElementById('course_department');
    if (deptSelect) {
        const activeDepartments = departmentList.filter(dept => dept.status === 'Active');
        deptSelect.innerHTML = '<option value="">Select Department</option>' + activeDepartments.map(dept => `
            <option value="${dept.id}">${dept.department_code} - ${dept.department_name}</option>
        `).join('');
    }

    const filterSelect = document.getElementById('courseDepartmentFilter');
    if (filterSelect) {
        filterSelect.innerHTML = '<option value="">All Departments</option>' + departmentList.map(dept => `
            <option value="${dept.id}">${dept.department_code} - ${dept.department_name}</option>
        `).join('');
    }
}

async function loadCourseManagement() {
    courseList = await fetchJson(courseApi);
    const search = document.getElementById('courseSearch').value.trim().toLowerCase();
    const selectedDepartment = document.getElementById('courseDepartmentFilter').value;
    const tbody = document.getElementById('coursesTableBody');

    const rows = courseList.filter(course => {
        const matchesDepartment = !selectedDepartment || String(course.department_id) === String(selectedDepartment);
        const matchesSearch = `${course.course_code || ''} ${course.course_name || ''} ${course.department_name || ''}`.toLowerCase().includes(search);
        return matchesDepartment && matchesSearch;
    });

    tbody.innerHTML = rows.length ? rows.map(course => `
        <tr>
            <td>${course.course_code || '—'}</td>
            <td>${course.course_name || '—'}</td>
            <td>${course.department_code ? `${course.department_code} - ${course.department_name}` : 'Unassigned'}</td>
            <td>${courseStatusChip(course.status)}</td>
            <td>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="button" class="btn btn-sm btn-secondary" data-action="course-edit" data-id="${course.id}">Edit</button>
                    <button type="button" class="btn btn-sm btn-warning" data-action="course-toggle" data-id="${course.id}">${course.status === 'Active' ? 'Deactivate' : 'Activate'}</button>
                    <button type="button" class="btn btn-sm btn-danger" data-action="course-delete" data-id="${course.id}">Delete</button>
                </div>
            </td>
        </tr>
    `).join('') : '<tr><td colspan="5">No courses found.</td></tr>';
}

async function submitFormWithJson(form, apiUrl) {
    const formData = new FormData(form);
    const response = await fetch(apiUrl, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    return response.ok ? response.json() : { success: false, message: 'Request failed.' };
}

async function handleDepartmentSubmit(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const apiUrl = form.dataset.action || '<?= base_url('admin/departments/store') ?>';
    const result = await submitFormWithJson(form, apiUrl);

    if (!result.success) {
        const text = result.message || (result.errors ? JSON.stringify(result.errors) : 'Department save failed.');
        alert(text);
        return;
    }

    form.reset();
    const deptModal = document.getElementById('departmentModal');
    deptModal.classList.remove('active');
    deptModal.style.setProperty('display', 'none', 'important');
    deptModal.style.visibility = 'hidden';
    deptModal.style.opacity = '0';
    await loadDepartmentManagement();
    await loadCourseManagement();
}

async function handleCourseSubmit(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const apiUrl = form.dataset.action || '<?= base_url('admin/courses/store') ?>';
    const result = await submitFormWithJson(form, apiUrl);

    if (!result.success) {
        const text = result.message || (result.errors ? JSON.stringify(result.errors) : 'Course save failed.');
        alert(text);
        return;
    }

    form.reset();
    const courseModal = document.getElementById('courseModal');
    courseModal.classList.remove('active');
    courseModal.style.setProperty('display', 'none', 'important');
    courseModal.style.visibility = 'hidden';
    courseModal.style.opacity = '0';
    await loadDepartmentManagement();
    await loadCourseManagement();
}

async function openDepartmentModal(mode, departmentId = null) {
    const modal = document.getElementById('departmentModal');
    const form = document.getElementById('departmentForm');
    const title = document.getElementById('departmentModalTitle');
    const inputs = {
        name: document.getElementById('department_name'),
        status: document.getElementById('department_status')
    };

    if (!modal || !form || !title || !inputs.name || !inputs.status) {
        return;
    }

    if (mode === 'edit' && departmentId) {
        const dept = departmentList.find(item => String(item.id) === String(departmentId));
        if (!dept) return;
        title.textContent = 'Edit Department';
        form.dataset.action = `<?= base_url('admin/departments/update') ?>/${dept.id}`;
        inputs.name.value = dept.department_name || '';
        inputs.status.value = dept.status || 'Active';
    } else {
        title.textContent = 'Add Department';
        form.dataset.action = '<?= base_url('admin/departments/store') ?>';
        form.reset();
    }

    modal.classList.add('active');
    modal.style.setProperty('display', 'flex', 'important');
    modal.style.visibility = 'visible';
    modal.style.opacity = '1';
}

async function openCourseModal(mode, courseId = null) {
    const modal = document.getElementById('courseModal');
    const form = document.getElementById('courseForm');
    const title = document.getElementById('courseModalTitle');
    const inputs = {
        code: document.getElementById('course_code'),
        name: document.getElementById('course_name'),
        department: document.getElementById('course_department'),
        status: document.getElementById('course_status')
    };

    if (!modal || !form || !title || !inputs.code || !inputs.name || !inputs.department || !inputs.status) {
        return;
    }

    await loadDepartmentManagement();

    if (mode === 'edit' && courseId) {
        const course = courseList.find(item => String(item.id) === String(courseId));
        if (!course) return;
        title.textContent = 'Edit Course';
        form.dataset.action = `<?= base_url('admin/courses/update') ?>/${course.id}`;
        inputs.code.value = course.course_code || '';
        inputs.name.value = course.course_name || '';
        inputs.department.value = course.department_id || '';
        inputs.status.value = course.status || 'Active';
    } else {
        title.textContent = 'Add Course';
        form.dataset.action = '<?= base_url('admin/courses/store') ?>';
        form.reset();
        inputs.department.value = '';
    }

    modal.classList.add('active');
    modal.style.setProperty('display', 'flex', 'important');
    modal.style.visibility = 'visible';
    modal.style.opacity = '1';
}

async function deleteDepartment(departmentId) {
    const department = departmentList.find(item => String(item.id) === String(departmentId));
    if (!department) return;
    if (Number(department.course_count || 0) > 0) {
        alert('This department contains courses. Please remove or reassign the courses before deleting this department.');
        return;
    }

    if (!confirm(`Delete department ${department.department_code}?`)) return;

    const response = await fetch(`<?= base_url('admin/departments/delete') ?>/${departmentId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const result = await response.json();

    if (!result.success) {
        alert(result.message || 'Unable to delete department.');
        return;
    }

    await loadDepartmentManagement();
    await loadCourseManagement();
}

async function toggleDepartment(departmentId) {
    const response = await fetch(`<?= base_url('admin/departments/toggle') ?>/${departmentId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const result = await response.json();
    if (!result.success) {
        alert('Unable to update department status.');
        return;
    }
    await loadDepartmentManagement();
    await loadCourseManagement();
}

async function deleteCourse(courseId) {
    if (!confirm('Delete this course?')) return;

    const response = await fetch(`<?= base_url('admin/courses/delete') ?>/${courseId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const result = await response.json();

    if (!result.success) {
        alert(result.message || 'Unable to delete course.');
        return;
    }

    await loadDepartmentManagement();
    await loadCourseManagement();
}

async function toggleCourse(courseId) {
    const response = await fetch(`<?= base_url('admin/courses/toggle') ?>/${courseId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const result = await response.json();
    if (!result.success) {
        alert('Unable to update course status.');
        return;
    }
    await loadDepartmentManagement();
    await loadCourseManagement();
}

window.openDepartmentModal = openDepartmentModal;
window.openCourseModal = openCourseModal;

function enableDraggableModal(modalElement) {
    const handle = modalElement.querySelector('[data-drag-handle="true"]');
    if (!handle) return;

    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let startLeft = 0;
    let startTop = 0;

    const stopDragging = () => {
        isDragging = false;
        handle.style.cursor = 'move';
        modalElement.style.cursor = 'default';
    };

    handle.addEventListener('pointerdown', function (event) {
        if (event.target.closest('button, input, select, textarea, label')) {
            return;
        }

        isDragging = true;
        const rect = modalElement.getBoundingClientRect();
        startX = event.clientX;
        startY = event.clientY;
        startLeft = rect.left;
        startTop = rect.top;

        modalElement.style.position = 'absolute';
        modalElement.style.margin = '0';
        modalElement.style.left = `${startLeft}px`;
        modalElement.style.top = `${startTop}px`;
        modalElement.style.transform = 'none';
        modalElement.style.cursor = 'grabbing';
        handle.style.cursor = 'grabbing';
        handle.setPointerCapture(event.pointerId);
    });

    handle.addEventListener('pointermove', function (event) {
        if (!isDragging) return;

        const deltaX = event.clientX - startX;
        const deltaY = event.clientY - startY;
        const maxLeft = Math.max(0, window.innerWidth - modalElement.offsetWidth);
        const maxTop = Math.max(0, window.innerHeight - modalElement.offsetHeight);

        const nextLeft = Math.min(Math.max(0, startLeft + deltaX), maxLeft);
        const nextTop = Math.min(Math.max(0, startTop + deltaY), maxTop);

        modalElement.style.left = `${nextLeft}px`;
        modalElement.style.top = `${nextTop}px`;
    });

    handle.addEventListener('pointerup', stopDragging);
    handle.addEventListener('pointercancel', stopDragging);
    handle.addEventListener('lostpointercapture', stopDragging);
}

document.addEventListener('DOMContentLoaded', async function () {
    document.querySelectorAll('[data-draggable-modal="true"]').forEach(enableDraggableModal);
    document.getElementById('departmentForm').addEventListener('submit', handleDepartmentSubmit);
    document.getElementById('courseForm').addEventListener('submit', handleCourseSubmit);

    document.getElementById('addDeptBtn').addEventListener('click', () => openDepartmentModal('add'));
    document.getElementById('departmentCancel').addEventListener('click', () => {
        const modal = document.getElementById('departmentModal');
        modal.classList.remove('active');
        modal.style.setProperty('display', 'none', 'important');
        modal.style.visibility = 'hidden';
        modal.style.opacity = '0';
    });
    document.getElementById('addCourseBtn').addEventListener('click', () => openCourseModal('add'));
    document.getElementById('courseCancel').addEventListener('click', () => {
        const modal = document.getElementById('courseModal');
        modal.classList.remove('active');
        modal.style.setProperty('display', 'none', 'important');
        modal.style.visibility = 'hidden';
        modal.style.opacity = '0';
    });

    document.getElementById('deptSearch').addEventListener('input', loadDepartmentManagement);
    document.getElementById('courseSearch').addEventListener('input', loadCourseManagement);
    document.getElementById('courseDepartmentFilter').addEventListener('change', loadCourseManagement);

    document.getElementById('departmentsTableBody').addEventListener('click', async function (event) {
        const button = event.target.closest('button');
        if (!button) return;
        const departmentId = button.dataset.id;
        if (!departmentId) return;

        const action = button.dataset.action;
        if (action === 'dept-edit') openDepartmentModal('edit', departmentId);
        if (action === 'dept-toggle') toggleDepartment(departmentId);
        if (action === 'dept-delete') deleteDepartment(departmentId);
    });

    document.getElementById('coursesTableBody').addEventListener('click', async function (event) {
        const button = event.target.closest('button');
        if (!button) return;
        const courseId = button.dataset.id;
        if (!courseId) return;

        const action = button.dataset.action;
        if (action === 'course-edit') openCourseModal('edit', courseId);
        if (action === 'course-toggle') toggleCourse(courseId);
        if (action === 'course-delete') deleteCourse(courseId);
    });

    await loadDepartmentManagement();
    await loadCourseManagement();
});
</script>

<style>
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .badge-success {
        background: rgba(34, 197, 94, 0.14);
        color: #7ef0ac;
        border: 1px solid rgba(34, 197, 94, 0.35);
    }
    .badge-warning {
        background: rgba(250, 204, 21, 0.12);
        color: #ffd66b;
        border: 1px solid rgba(250, 204, 21, 0.28);
    }
    .modal {
        display: none !important;
    }
    .modal.active {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
</style>

<?= $this->endSection() ?>
