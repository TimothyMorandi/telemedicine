// script_app.js - Contains all JavaScript logic for the Appointment Manager application

document.addEventListener('DOMContentLoaded', function() {
    // Custom Alert and Confirm functions
    function showAlert(message, title = 'Alert') {
        const alertModal = document.getElementById('customAlertModal');
        document.getElementById('customAlertTitle').textContent = title;
        document.getElementById('customAlertMessage').textContent = message;
        alertModal.style.display = 'flex';
        return new Promise(resolve => {
            document.getElementById('customAlertOkBtn').onclick = () => {
                alertModal.style.display = 'none';
                resolve(true);
            };
        });
    }

    function showConfirm(message, title = 'Confirm') {
        const confirmModal = document.getElementById('customConfirmModal');
        document.getElementById('customConfirmTitle').textContent = title;
        document.getElementById('customConfirmMessage').textContent = message;
        confirmModal.style.display = 'flex';
        return new Promise(resolve => {
            document.getElementById('customConfirmYesBtn').onclick = () => {
                confirmModal.style.display = 'none';
                resolve(true);
            };
            document.getElementById('customConfirmNoBtn').onclick = () => {
                confirmModal.style.display = 'none';
                resolve(false);
            };
        });
    }

    // Modal functionality
    const addAppointmentBtn = document.getElementById('addAppointmentBtn');
    const closeModal = document.getElementById('closeModal');
    const appointmentModal = document.getElementById('appointmentModal');
    const cancelModalBtn = document.querySelector('.btn-cancel-modal');

    // Open/close main appointment modal
    if (addAppointmentBtn) addAppointmentBtn.addEventListener('click', function() { appointmentModal.style.display = 'flex'; });
    if (closeModal) closeModal.addEventListener('click', function() { appointmentModal.style.display = 'none'; });
    if (cancelModalBtn) cancelModalBtn.addEventListener('click', function() { appointmentModal.style.display = 'none'; });
    window.addEventListener('click', function(event) {
        // Close modal if clicked outside of it
        if (event.target === appointmentModal) {
            appointmentModal.style.display = 'none';
        }
    });

    // Doctor selection
    const doctorCards = document.querySelectorAll('.doctor-card');
    doctorCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove 'selected' class from all other doctor cards
            doctorCards.forEach(c => c.classList.remove('doctor-card--selected'));
            // Add 'selected' class to the clicked card
            this.classList.add('doctor-card--selected');
            // Auto-fill form fields
            document.getElementById('doctor').value = this.getAttribute('data-doctor');
            document.getElementById('hospital').value = this.getAttribute('data-hospital');
            document.getElementById('department').value = this.getAttribute('data-department');
        });
    });

    // Form submission
    const appointmentForm = document.getElementById('appointmentForm');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevent default form submission

            // Gather form data
            const formData = {
                date: this.date.value,
                time: this.time.value,
                doctor: this.doctor.value,
                hospital: this.hospital.value,
                department: this.department.value
            };

            // Validate doctor selection before proceeding
            if (!formData.doctor || !formData.hospital || !formData.department) {
                await showAlert('Please select a doctor from the list to proceed.');
                return; // Stop function execution
            }

            // Create a new table row for the appointment
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${formData.date}</td>
                <td>${formData.time}</td>
                <td>${formData.doctor}</td>
                <td>${formData.hospital}</td>
                <td>${formData.department}</td>
                <td><span class="status upcoming">Upcoming</span></td>
                <td>
                    <button class="btn-join"><i class="fas fa-video"></i> Join</button>
                    <button class="btn-delete"><i class="fas fa-trash"></i></button>
                </td>
            `;

            // Add the new row to the top of the appointments list
            document.getElementById('appointmentsList').prepend(newRow);

            // Hide the modal and reset the form
            appointmentModal.style.display = 'none';
            this.reset();
            // Deselect any selected doctor card
            doctorCards.forEach(card => card.classList.remove('doctor-card--selected'));

            // Attach event listeners for the new row's buttons
            newRow.querySelector('.btn-delete').addEventListener('click', async function() {
                const confirmed = await showConfirm('Are you sure you want to delete this appointment?');
                if (confirmed) {
                    newRow.remove();
                }
            });
            newRow.querySelector('.btn-join').addEventListener('click', async function() {
                const row = this.closest('tr');
                const doctor = row.cells[2].textContent;
                const time = row.cells[1].textContent;
                await showAlert(`Joining appointment with ${doctor} at ${time}.`);
            });

            // Show success message
            await showAlert('Appointment added successfully!');
        });
    }

    // Attach event listeners to initial static appointments (join/delete)
    // This handles the appointments that are hardcoded in the HTML
    document.querySelectorAll('#appointmentsList .btn-join').forEach(button => {
        // Ensure button is not disabled before attaching join event
        if (!button.disabled) {
            button.addEventListener('click', async function() {
                const row = this.closest('tr');
                const doctor = row.cells[2].textContent;
                const time = row.cells[1].textContent;
                await showAlert(`Joining appointment with ${doctor} at ${time}.`);
            });
        }
    });
    document.querySelectorAll('#appointmentsList .btn-delete').forEach(button => {
        button.addEventListener('click', async function() {
            const row = this.closest('tr');
            const confirmed = await showConfirm('Are you sure you want to delete this appointment?');
            if (confirmed) {
                row.remove();
            }
        });
    });

    // Set minimum date for appointment input to today's date
    const appointmentDate = document.getElementById('appointmentDate');
    if (appointmentDate) {
        const today = new Date().toISOString().split('T')[0];
        appointmentDate.min = today;
    }

    // Set default time for appointment input to the next full hour
    const appointmentTime = document.getElementById('appointmentTime');
    if (appointmentTime) {
        const now = new Date();
        const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
        // Format hours and minutes with leading zeros if necessary
        const hours = String(nextHour.getHours()).padStart(2, '0');
        const minutes = String(nextHour.getMinutes()).padStart(2, '0');
        appointmentTime.value = `${hours}:${minutes}`;
    }

    // Doctor search functionality
    const searchInput = document.getElementById('doctorSearch');
    const doctorsList = document.getElementById('doctorsList');
    const allDoctors = Array.from(doctorsList.querySelectorAll('.doctor-card')); // Convert NodeList to Array for easier filtering

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase(); // Get search term and convert to lowercase for case-insensitive search
        allDoctors.forEach(doctor => {
            const doctorName = doctor.getAttribute('data-doctor').toLowerCase();
            const hospital = doctor.getAttribute('data-hospital').toLowerCase();
            const specialty = doctor.getAttribute('data-specialty').toLowerCase();

            // Check if search term is included in doctor name, hospital, or specialty
            if (
                doctorName.includes(searchTerm) ||
                hospital.includes(searchTerm) ||
                specialty.includes(searchTerm)
            ) {
                doctor.style.display = 'flex'; // Show the card if it matches
            } else {
                doctor.style.display = 'none'; // Hide the card if it doesn't match
            }
        });
    });
});
