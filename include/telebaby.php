<script>
document.addEventListener('DOMContentLoaded', () => {
    // ---------- Due Date Calculator ----------
    class DueDateCalculator {
        constructor() {
            this.calcInput = document.getElementById('last-period');
            this.calcButton = document.querySelector('.btn-calculate');
            this.resultBox = document.querySelector('.result-box');
            this.init();
        }

        init() {
            this.calcButton.addEventListener('click', () => this.calculate());
            this.setMaxDate();
        }

        setMaxDate() {
            const today = new Date().toISOString().split('T')[0];
            this.calcInput.max = today;
        }

        calculate() {
            const lastPeriod = new Date(this.calcInput.value);
            if (!lastPeriod.getTime()) return this.showError('Please select a valid date');
            
            const dueDate = new Date(lastPeriod);
            dueDate.setDate(dueDate.getDate() + 280);
            
            this.showResults({
                dueDate: dueDate.toLocaleDateString(),
                weeks: this.calculateWeeks(lastPeriod)
            });
        }

        calculateWeeks(startDate) {
            const today = new Date();
            const diffTime = today - startDate;
            return Math.floor(diffTime / (1000 * 60 * 60 * 24 * 7));
        }

        showResults({ dueDate, weeks }) {
            this.resultBox.innerHTML = `
                <div class="result-item">
                    <h4>Estimated Due Date</h4>
                    <p class="highlight">${dueDate}</p>
                </div>
                <div class="result-item">
                    <h4>Current Pregnancy Week</h4>
                    <p class="highlight">Week ${weeks}</p>
                </div>
            `;
        }

        showError(message) {
            this.resultBox.innerHTML = `<p class="error">${message}</p>`;
        }
    }

    // ---------- Timeline Animation ----------
    const timelineObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    // ---------- Service Filtering ----------
    class ServiceFilter {
        constructor() {
            this.filterButtons = document.querySelectorAll('.filter-btn');
            this.serviceCards = document.querySelectorAll('.service-card');
            this.init();
        }

        init() {
            this.filterButtons.forEach(btn => {
                btn.addEventListener('click', () => this.filter(btn));
            });
        }

        filter(btn) {
            const filter = btn.dataset.filter;
            this.updateActiveButton(btn);
            this.filterCards(filter);
        }

        updateActiveButton(activeBtn) {
            this.filterButtons.forEach(btn => btn.classList.remove('active'));
            activeBtn.classList.add('active');
        }

        filterCards(filter) {
            this.serviceCards.forEach(card => {
                card.style.display = filter === 'all' || card.classList.contains(filter) 
                    ? 'block' 
                    : 'none';
            });
        }
    }

    
    // ---------- Emergency Button ----------
    const setupEmergencyButton = () => {
        const emergencyBtn = document.querySelector('.emergency-btn');
        emergencyBtn.addEventListener('click', () => {
            window.location.href = 'tel:+1234567890';
        });
    };

    // ---------- Smooth Scroll ----------
    const setupSmoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
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
    };

    // ---------- Specialist Loader ----------
    const loadSpecialists = () => {
        fetch('/api/specialists')
            .then(response => response.json())
            .then(data => this.populateSpecialists(data))
            .catch(error => console.error('Error loading specialists:', error));
    };

    const populateSpecialists = (specialists) => {
        const carousel = document.querySelector('.specialist-carousel');
        carousel.innerHTML = specialists.map(spec => `
            <div class="specialist-card">
                <img src="${spec.photo}" alt="${spec.name}">
                <h3>${spec.name}</h3>
                <p>${spec.specialty}</p>
                <p>${spec.experience} years experience</p>
            </div>
        `).join('');
    };

    // ---------- Initialize All Components ----------
    new DueDateCalculator();
    document.querySelectorAll('.timeline-step').forEach(step => timelineObserver.observe(step));
    new ServiceFilter();
    new TrimesterNavigation();
    setupEmergencyButton();
    setupSmoothScroll();
    loadSpecialists();
});
</script>