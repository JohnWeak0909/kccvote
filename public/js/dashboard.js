document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const body = document.body;
    const sidebar = document.querySelector('.sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('sidebar-collapsed');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                sidebar.style.width = '80px';
                body.querySelector('.main-panel').style.marginLeft = '80px';
            } else {
                sidebar.style.width = '280px';
                body.querySelector('.main-panel').style.marginLeft = '280px';
            }
        });
    }
});
