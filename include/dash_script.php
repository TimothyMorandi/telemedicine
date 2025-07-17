<script>
// Sidebar SPA loader
document.querySelectorAll('.sidebar-nav .nav-item a').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        let url = this.getAttribute('data-content');
        // Set active class
        document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => item.classList.remove('active'));
        this.parentNode.classList.add('active');
        // Load content into main-content
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Only insert the .main-content part
                let tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                let newContent = tempDiv.querySelector('.main-content');
                if (newContent) {
                    document.getElementById('main-content').innerHTML = newContent.innerHTML;
                    // Optionally, re-init dashboard JS for new content
                    if (window.initDashboardPage) window.initDashboardPage();
                }
            });
    });
});

// Toggle sidebar on mobile
document.addEventListener('DOMContentLoaded', function() {
    let menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    }
});

// Sign out
function confirmSignOut() {
    if (confirm('Are you sure you want to sign out?')) {
        // You can clear PHP session via AJAX or redirect
        window.location.href = 'logout.php';
    }
}

// Optional: re-init for loaded pages
window.initDashboardPage = function() {
    // Handle appointment calendar clicks (if present)
    document.querySelectorAll('.day-num').forEach(day => {
        day.addEventListener('click', function() {
            document.querySelectorAll('.day-num').forEach(d => d.classList.remove('active-date'));
            this.classList.add('active-date');
            console.log(`Selected date: October ${this.textContent}, 2024`);
        });
    });
    // Add new appointment (if present)
    let btnAdd = document.querySelector('.btn-add-appointment');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            alert('Redirecting to appointment scheduling...');
            // window.location.href = 'appointment.php';
        });
    }
    // Health chart (if present)
    if (document.getElementById('healthChart')) {
        const ctx = document.getElementById('healthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Heart Rate (BPM)',
                    data: [72, 74, 71, 73, 70, 68, 72],
                    borderColor: '#FF6B6B',
                    backgroundColor: 'rgba(255, 107, 107, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Blood Pressure (mmHg)',
                    data: [118, 122, 120, 121, 119, 118, 120],
                    borderColor: '#F6AD55',
                    backgroundColor: 'rgba(246, 173, 85, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }
};
// First page load
document.addEventListener('DOMContentLoaded', window.initDashboardPage);
</script>