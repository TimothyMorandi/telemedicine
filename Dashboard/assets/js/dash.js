// Global variables
let healthChartInstance = null;

// Page initialization logic
window.initDashboardPage = function() {
    // ===== COMMON FUNCTIONALITY =====
    // Sign out
    document.getElementById('signOutBtn')?.addEventListener('click', function() {
        document.getElementById('confirm-sign-out').style.display = 'flex';
    });
    
    document.getElementById('confirm-sign-out-yes')?.addEventListener('click', function() {
        window.location.href = '../../login.php';
    });
    
    document.getElementById('confirm-sign-out-no')?.addEventListener('click', function() {
        document.getElementById('confirm-sign-out').style.display = 'none';
    });

    // Toggle sidebar
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.body.classList.toggle('sidebar-active');
        });
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('confirm-sign-out');
        if (event.target === modal) modal.style.display = 'none';
        
        const appointmentModal = document.getElementById('appointmentModal');
        if (appointmentModal && event.target === appointmentModal) {
            appointmentModal.style.display = 'none';
        }
    });

    // ===== DASHBOARD PAGE =====
    if (document.querySelector('.dashboard-page')) {
        // Calendar date selection
        document.querySelectorAll('.day-num').forEach(day => {
            day.addEventListener('click', function() {
                document.querySelectorAll('.day-num').forEach(d => d.classList.remove('active-date'));
                this.classList.add('active-date');
            });
        });

        // "Add Appointment" button
        const btnAdd = document.querySelector('.btn-add-appointment');
        if (btnAdd) {
            btnAdd.addEventListener('click', function() {
                const appointmentLink = document.querySelector('.nav-item a[data-content="appointment.php"]');
                if (appointmentLink) appointmentLink.click();
            });
        }

        // Health chart
        const healthChartEl = document.getElementById('healthChart');
        if (healthChartEl) {
            if (healthChartInstance) healthChartInstance.destroy();
            const ctx = healthChartEl.getContext('2d');
            healthChartInstance = new Chart(ctx, {
                // ... chart config ...
            });
        }
    }

    // ===== APPOINTMENT PAGE =====
    if (document.querySelector('.appointments-section')) {
        // Modal handling
        const addAppointmentBtn = document.getElementById('addAppointmentBtn');
        const closeModal = document.getElementById('closeModal');
        const appointmentModal = document.getElementById('appointmentModal');
        
        if (addAppointmentBtn) addAppointmentBtn.addEventListener('click', () => appointmentModal.style.display = 'block');
        if (closeModal) closeModal.addEventListener('click', () => appointmentModal.style.display = 'none');
        
        // Form submission
        const appointmentForm = document.getElementById('appointmentForm');
        if (appointmentForm) {
            appointmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // ... form handling ...
            });
        }
        
        // Handle join buttons
        document.querySelectorAll('.btn-join').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const doctor = row.cells[2].textContent;
                const time = row.cells[1].textContent;
                alert(`Joining appointment with ${doctor} at ${time}`);
            });
        });
    }
};

// Initialize when page loads
document.addEventListener("DOMContentLoaded", function() {
    if (window.initDashboardPage) window.initDashboardPage();
});