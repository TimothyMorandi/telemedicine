<?php
// Start session and connect to DB
if (session_status() === PHP_SESSION_NONE) session_start();


$doctors = [];
try {
    $stmt = $pdo->prepare("
        SELECT id, CONCAT(first_name, ' ', last_name) AS name, specialty, hospital
        FROM users
        WHERE user_type = 'doctor'
        ORDER BY name ASC
    ");
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>
<!-- Doctor selection for appointment booking -->
<form id="appointmentForm" method="POST" action="backend/book_appointment.php">
    <div class="form-group">
        <label for="doctorSelect">Choose a Doctor *</label>
        <select id="doctorSelect" name="doctor_id" required>
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?= $doctor['id'] ?>"
                    data-hospital="<?= htmlspecialchars($doctor['hospital']) ?>"
                    data-specialty="<?= htmlspecialchars($doctor['specialty']) ?>">
                    Dr. <?= htmlspecialchars($doctor['name']) ?> - <?= htmlspecialchars($doctor['specialty']) ?> (<?= htmlspecialchars($doctor['hospital']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="hospital">Hospital</label>
        <input type="text" id="hospital" name="hospital" readonly>
    </div>
    <div class="form-group">
        <label for="department">Specialty</label>
        <input type="text" id="department" name="department" readonly>
    </div>
    <!-- Add other fields for date, time, etc. -->
    <button type="submit">Book Appointment</button>
</form>

<!-- Optional: Quick doctor cards for click-to-select -->
<div id="doctorsList" style="margin-top:2rem;">
    <?php foreach ($doctors as $doctor): ?>
        <div class="doctor-card"
             data-doctor-id="<?= $doctor['id'] ?>"
             data-hospital="<?= htmlspecialchars($doctor['hospital']) ?>"
             data-specialty="<?= htmlspecialchars($doctor['specialty']) ?>">
            <strong>Dr. <?= htmlspecialchars($doctor['name']) ?></strong><br>
            <?= htmlspecialchars($doctor['specialty']) ?> (<?= htmlspecialchars($doctor['hospital']) ?>)
        </div>
    <?php endforeach; ?>
</div>

<script>
// Autofill hospital and specialty when doctor is selected
document.getElementById('doctorSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    document.getElementById('hospital').value = selected.dataset.hospital || '';
    document.getElementById('department').value = selected.dataset.specialty || '';
});

// If clicking a doctor card, set dropdown and autofill
document.getElementById('doctorsList').addEventListener('click', function(e) {
    const card = e.target.closest('.doctor-card');
    if (card) {
        document.getElementById('doctorSelect').value = card.dataset.doctorId;
        document.getElementById('hospital').value = card.dataset.hospital;
        document.getElementById('department').value = card.dataset.specialty;
    }
});
</script>

<style>
.doctor-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 6px;
    cursor: pointer;
    transition: background .2s;
}
.doctor-card:hover {
    background: #e0f2fe;
}
</style>