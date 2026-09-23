const themeStorageKey = 'voting_system_theme';

function getPreferredTheme() {
    const stored = localStorage.getItem(themeStorageKey);
    if (stored === 'light' || stored === 'dark') {
        return stored;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function setTheme(mode) {
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');

    if (mode === 'dark') {
        body.classList.add('theme-dark');
    } else {
        body.classList.remove('theme-dark');
    }

    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        const label = themeToggle.querySelector('span');
        if (icon) {
            icon.classList.toggle('bi-moon-fill', mode !== 'dark');
            icon.classList.toggle('bi-sun-fill', mode === 'dark');
        }
        if (label) {
            label.textContent = mode === 'dark' ? 'Light mode' : 'Dark mode';
        }
    }

    localStorage.setItem(themeStorageKey, mode);
}

function toggleTheme() {
    const current = document.body.classList.contains('theme-dark') ? 'dark' : 'light';
    setTheme(current === 'dark' ? 'light' : 'dark');
}

function initializeThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) {
        return;
    }

    themeToggle.addEventListener('click', function () {
        toggleTheme();
    });
}

function initTheme() {
    const preferredTheme = getPreferredTheme();
    setTheme(preferredTheme);
    initializeThemeToggle();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTheme);
} else {
    initTheme();
}
