// Main App Logic (MPA Version)

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initGlobalInteractions();
});

// --- SIDEBAR & MENU ---
function initMobileMenu() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');

    if (btn && menu) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    }
}

// --- GLOBAL INTERACTIONS ---
function initGlobalInteractions() {
    // Dismiss alerts
    const alerts = document.querySelectorAll('.alert-dismiss');
    alerts.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const alert = e.target.closest('.alert');
            if (alert) alert.remove();
        });
    });

    // Confirmation dialogs
    window.confirmAction = function(message) {
        return confirm(message);
    };
}
