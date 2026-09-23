// Admin Dashboard Enhancements
function formatTimestamp(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) {
        return `${diff} seconds ago`;
    }
    if (diff < 3600) {
        return `${Math.floor(diff / 60)} minutes ago`;
    }
    if (diff < 86400) {
        return `${Math.floor(diff / 3600)} hours ago`;
    }
    return `${Math.floor(diff / 86400)} days ago`;
}

function renderActivityItems(items) {
    const list = document.getElementById('activity-items');
    if (!list) return;

    list.innerHTML = '';
    if (!items || items.length === 0) {
        list.innerHTML = '<li style="padding: 0.75rem 0;">No recent activity available.</li>';
        return;
    }

    items.forEach(item => {
        const li = document.createElement('li');
        li.style.padding = '0.75rem 0';
        li.style.borderBottom = '1px solid rgba(229,231,235,0.6)';
        li.innerHTML = `<strong>${item.description}</strong><br><small>${formatTimestamp(item.created_at)}</small>`;
        list.appendChild(li);
    });
}

function renderSystemStatus(status) {
    const list = document.getElementById('status-items');
    if (!list) return;

    list.innerHTML = '';
    if (!status) {
        list.innerHTML = '<li style="padding: 0.75rem 0;">System status unavailable.</li>';
        return;
    }

    Object.values(status).forEach(item => {
        const li = document.createElement('li');
        li.style.padding = '0.75rem 0';
        li.style.borderBottom = '1px solid rgba(229,231,235,0.6)';
        li.innerHTML = `<strong>${item.label}:</strong> ${item.value} <br><small>${item.detail}</small>`;
        list.appendChild(li);
    });
}

function updateDashboard(data) {
    const studentCount = document.getElementById('student-count');
    const voteCount = document.getElementById('vote-count');
    const candidateCount = document.getElementById('candidate-count');
    const electionStatus = document.getElementById('election-status');
    const progressBar = document.getElementById('vote-progress-bar');
    const progressText = document.getElementById('vote-progress-text');

    if (studentCount) studentCount.textContent = data.students;
    if (voteCount) voteCount.textContent = data.votes;
    if (candidateCount) candidateCount.textContent = data.candidates;
    if (electionStatus) electionStatus.textContent = data.election_status;
    if (progressBar) {
        const width = data.vote_progress || 0;
        progressBar.style.width = `${width}%`;
        progressBar.textContent = `${width}%`;
    }
    if (progressText) progressText.textContent = `${data.votes}/${data.students || 0} votes cast`;
    renderActivityItems(data.activities);
    renderSystemStatus(data.system_status);
}

function fetchDashboardStats(isManual = false) {
    const btn = document.getElementById('dashboard-refresh');
    if (btn) {
        btn.disabled = true;
        if (isManual) {
            btn.textContent = 'Refreshing...';
        }
    }

    const path = window.dashboardStatsUrl || 'api/stats';

    fetch(path, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server returned ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        updateDashboard(data);
    })
    .catch(error => {
        console.warn('Dashboard stats error:', error);
        renderActivityItems([]);
        renderSystemStatus(null);
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            if (isManual) {
                btn.textContent = 'Refresh';
            }
        }
    });
}

// Real-time clock
function updateTime() {
    const now = new Date();
    const formatted = now.toLocaleString(undefined, {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = formatted;
    }
}

// Initialize admin features
document.addEventListener('DOMContentLoaded', function() {
    // Highlight selected candidate card
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            const name = this.getAttribute('name');
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                const card = r.closest('.candidate-card');
                if (card) {
                    card.classList.remove('selected');
                }
            });
            if (this.checked) {
                const card = this.closest('.candidate-card');
                if (card) {
                    card.classList.add('selected');
                }
            }
        });
    });

    // Start real-time clock for admin dashboard
    if (document.getElementById('current-time')) {
        updateTime();
        setInterval(updateTime, 1000);
    }

    const refreshButton = document.getElementById('dashboard-refresh');
    if (refreshButton) {
        refreshButton.addEventListener('click', () => fetchDashboardStats(true));
    }

    if (document.getElementById('student-count')) {
        fetchDashboardStats();
        setInterval(fetchDashboardStats, 30000);
    }

    // Add page enter animation
    document.body.classList.add('page-enter');

    // Enhanced form interactions
    const formInputs = document.querySelectorAll('.form-group input, .form-group select, .form-group textarea');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            if (this.parentElement) {
                this.parentElement.classList.add('focused');
            }
        });

        input.addEventListener('blur', function() {
            if (!this.value && this.parentElement) {
                this.parentElement.classList.remove('focused');
            }
        });
    });

    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
