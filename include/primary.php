<script>
document.addEventListener('DOMContentLoaded', () => {
    // Clinic Filter Functionality
    const clinicFilters = {
        init() {
            this.bindEvents();
            this.loadClinics();
        },
        
        bindEvents() {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', () => this.filterClinics(btn));
            });
        },
        
        loadClinics() {
            // Sample clinic data - replace with real data
            const clinics = [
                { type: 'pediatrics', name: 'Childrens Health Center' },
                { type: 'emergency', name: 'City Emergency Clinic' },
                { type: 'chronic', name: 'Chronic Care Specialists' }
            ];
            
            const clinicGrid = document.querySelector('.clinic-grid');
            clinicGrid.innerHTML = clinics.map(clinic => `
                <div class="clinic-card" data-type="${clinic.type}">
                    <h4>${clinic.name}</h4>
                    <p>24/7 Telemedicine Support</p>
                </div>
            `).join('');
        },
        
        filterClinics(btn) {
            const filter = btn.dataset.filter;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            document.querySelectorAll('.clinic-card').forEach(card => {
                if (filter === 'all' || card.dataset.type === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    };
    clinicFilters.init();

    // Health Monitoring Chart
    const initHealthChart = () => {
        const ctx = document.getElementById('vitalChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [{
                    label: 'Heart Rate',
                    data: [72, 75, 73, 70, 68],
                    borderColor: '#e63946',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    };
    initHealthChart();

    // Floating Navigation Active State
    window.addEventListener('scroll', () => {
        const sections = document.querySelectorAll('.content-section');
        const navLinks = document.querySelectorAll('.floating-nav a');
        
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            if (rect.top <= 150 && rect.bottom >= 150) {
                const id = section.getAttribute('id');
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href').includes(id)) {
                        link.classList.add('active');
                    }
                });
            }
        });
    });

    // Emergency Modal Interaction
    const emergencyModal = document.querySelector('.emergency-modal');
    let modalVisible = true;
    
    const toggleModal = () => {
        modalVisible = !modalVisible;
        emergencyModal.style.display = modalVisible ? 'block' : 'none';
    };
    
    document.querySelectorAll('.btn-emergency').forEach(btn => {
        btn.addEventListener('click', toggleModal);
    });
});
</script>