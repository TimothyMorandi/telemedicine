<?php
// index.php - Main entry point for the Appointment Manager application

// Start session if not already started (important for potential future features like user authentication)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Initialize records array in session if not set
if (!isset($_SESSION['records'])) {
    $_SESSION['records'] = [
        [
            'id' => 1,
            'date' => '2025-06-10',
            'type' => 'Lab Result',
            'description' => 'Blood Test - Normal',
            'doctor' => 'Dr. Alice Smith',
            'hospital' => 'City Medical Center',
            'notes' => 'Patient\'s blood work shows normal levels across all tested parameters. No abnormalities detected.'
        ],
        [
            'id' => 2,
            'date' => '2025-05-28',
            'type' => 'Prescription',
            'description' => 'Amoxicillin 500mg - 7 days',
            'doctor' => 'Dr. John Doe',
            'hospital' => 'Metro General',
            'notes' => 'Prescribed for bacterial infection. Take twice daily with food.'
        ],
        [
            'id' => 3,
            'date' => '2025-05-15',
            'type' => 'Imaging',
            'description' => 'X-Ray Chest - No issues',
            'doctor' => 'Dr. Jane Lee',
            'hospital' => 'Children\'s Hospital',
            'notes' => 'Chest X-ray shows clear lungs with no signs of pneumonia or other abnormalities.'
        ]
    ];
}

// Handle form submission to add a new record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record'])) {
    $newRecord = [
        'id' => count($_SESSION['records']) + 1,
        'date' => $_POST['date'],
        'type' => $_POST['type'],
        'description' => $_POST['description'],
        'doctor' => $_POST['doctor'],
        'hospital' => $_POST['hospital'],
        'notes' => $_POST['notes']
    ];
    
    array_unshift($_SESSION['records'], $newRecord);
    $notification = "Record added successfully!";
}

// Handle record deletion
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    foreach ($_SESSION['records'] as $key => $record) {
        if ($record['id'] === $deleteId) {
            unset($_SESSION['records'][$key]);
            $notification = "Record deleted successfully!";
            break;
        }
    }
}

// Function to get CSS class for record type
function getTypeClass($type) {
    $classes = [
        'Lab Result' => 'lab',
        'Prescription' => 'prescription',
        'Imaging' => 'imaging',
        'Consultation' => 'consultation',
        'Surgery' => 'surgery'
    ];
    return $classes[$type] ?? 'default';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>HealthConnect - Medical Records</title> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* --primary: #2a6bc9;
            --primary-light: #e3edfc;
            --secondary: #2ac9bf;
            --dark: #2c3e50;
            --light: #f8f9fa;
            --danger: #e74c3c;
            --success: #27ae60;
            --warning: #f39c12;
            --gray: #7f8c8d;
            --light-gray: #ecf0f1;
            --border: #dfe6e9;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease; */
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8;
            color: var(--dark);
            line-height: 1.6;
        }

        .app-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        .app-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .app-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--primary);
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
            font-size: 18px;
        }

        .user-name {
            font-weight: 500;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header {
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--primary-light);
            border-bottom: 1px solid var(--border);
        }

        .card-header h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--primary);
            font-size: 20px;
        }

        /* Button Styles */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #1e5aaf;
            transform: translateY(-2px);
        }

        .btn-view {
            background: var(--primary-light);
            color: var(--primary);
            padding: 6px 12px;
        }

        .btn-view:hover {
            background: #d3e2fb;
        }

        .btn-delete {
            background: #fce8e6;
            color: var(--danger);
            padding: 6px 12px;
        }

        .btn-delete:hover {
            background: #fadbd8;
        }

        /* Table Styles */
        .records-table-wrapper {
            overflow-x: auto;
            padding: 15px;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .records-table th {
            background-color: var(--light-gray);
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid var(--border);
        }

        .records-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border);
        }

        .records-table tr:hover {
            background-color: var(--primary-light);
        }

        .record-type {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .type-lab {
            background: #e3f2fd;
            color: #1976d2;
        }

        .type-prescription {
            background: #e8f5e9;
            color: #388e3c;
        }

        .type-imaging {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .type-consultation {
            background: #fff3e0;
            color: #ef6c00;
        }

        .type-surgery {
            background: #ffebee;
            color: #d32f2f;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }

        .modal-header h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--primary);
        }

        .close {
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
            transition: var(--transition);
        }

        .close:hover {
            color: var(--dark);
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            padding: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid var(--border);
        }

        .btn-modal {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-cancel-record {
            background: var(--light-gray);
            color: var(--dark);
        }

        .btn-cancel-record:hover {
            background: #e0e0e0;
        }

        .btn-confirm {
            background: var(--primary);
            color: white;
        }

        .btn-confirm:hover {
            background: #1e5aaf;
        }

        /* Form Styles */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(42, 107, 201, 0.1);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        /* View Record Modal */
        .record-detail {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .record-detail:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: var(--gray);
            margin-bottom: 5px;
            font-size: 14px;
        }

        .detail-value {
            font-size: 16px;
            color: var(--dark);
        }

        .record-notes {
            background: var(--light);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.records {
            background: #e3f2fd;
            color: #1976d2;
        }

        .stat-icon.upcoming {
            background: #fff3e0;
            color: #ef6c00;
        }

        .stat-icon.medication {
            background: #e8f5e9;
            color: #388e3c;
        }

        .stat-icon.reminder {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: var(--gray);
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .app-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-info {
                align-self: flex-end;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            background: var(--success);
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1100;
            animation: slideIn 0.3s, fadeOut 0.5s 2.5s;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .notification.error {
            background: var(--danger);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- App Header -->
        <!-- <header class="app-header">
            <h1 class="app-title"><i class="fas fa-heartbeat"></i> HealthConnect</h1>
            <div class="user-info">
                <div class="user-name">Dr. Sarah Johnson</div>
                <div class="user-avatar">SJ</div>
            </div>
        </header> -->

        <!-- Stats Overview -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon records">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo count($_SESSION['records']); ?></h3>
                    <p>Health Records</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon upcoming">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>3</h3>
                    <p>Upcoming Appointments</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon medication">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="stat-info">
                    <h3>7</h3>
                    <p>Active Medications</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon reminder">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-info">
                    <h3>2</h3>
                    <p>Pending Reminders</p>
                </div>
            </div>
        </div>

        <!-- Notification -->
        <?php if (isset($notification)): ?>
            <div class="notification">
                <i class="fas fa-check-circle"></i>
                <?php echo $notification; ?>
            </div>
        <?php endif; ?>

        <!-- Health Records Section -->
        <section class="card health-records-section">
            <div class="card-header">
                <h2><i class="fas fa-file-medical"></i> My Health Records</h2>
                <button class="btn btn-primary" id="addRecordBtn">
                    <i class="fas fa-plus"></i> Add Record
                </button>
            </div>
            <div class="records-table-wrapper">
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Doctor</th>
                            <th>Hospital</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recordsList">
                        <?php foreach ($_SESSION['records'] as $record): ?>
                        <tr>
                            <td><?php echo $record['date']; ?></td>
                            <td>
                                <span class="record-type type-<?php echo getTypeClass($record['type']); ?>">
                                    <?php echo $record['type']; ?>
                                </span>
                            </td>
                            <td><?php echo $record['description']; ?></td>
                            <td><?php echo $record['doctor']; ?></td>
                            <td><?php echo $record['hospital']; ?></td>
                            <td class="actions-cell">
                                <button class="btn btn-view" 
                                        data-date="<?php echo $record['date']; ?>"
                                        data-type="<?php echo $record['type']; ?>"
                                        data-description="<?php echo htmlspecialchars($record['description']); ?>"
                                        data-doctor="<?php echo $record['doctor']; ?>"
                                        data-hospital="<?php echo $record['hospital']; ?>"
                                        data-notes="<?php echo htmlspecialchars($record['notes']); ?>">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <a href="?delete_id=<?php echo $record['id']; ?>" class="btn btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this record?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Health Record Modal -->
    <div class="modal" id="recordModal">
        <div class="modal-content">
            <span class="close" id="closeRecordModal">&times;</span>
            <div class="modal-header">
                <h3>Add New Health Record</h3>
            </div>
            <div class="modal-body">
                <form id="recordForm" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="recordDate">Date</label>
                            <input type="date" id="recordDate" class="form-control" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="recordType">Type</label>
                            <select id="recordType" class="form-control" name="type" required>
                                <option value="">Select Type</option>
                                <option value="Lab Result">Lab Result</option>
                                <option value="Prescription">Prescription</option>
                                <option value="Imaging">Imaging</option>
                                <option value="Consultation">Consultation</option>
                                <option value="Surgery">Surgery</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recordDescription">Description</label>
                        <input type="text" id="recordDescription" class="form-control" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="recordDoctor">Doctor</label>
                        <input type="text" id="recordDoctor" class="form-control" name="doctor" required>
                    </div>
                    <div class="form-group">
                        <label for="recordHospital">Hospital</label>
                        <input type="text" id="recordHospital" class="form-control" name="hospital" required>
                    </div>
                    <div class="form-group">
                        <label for="recordNotes">Notes</label>
                        <textarea id="recordNotes" class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal btn-cancel-record">Cancel</button>
                        <button type="submit" class="btn-modal btn-confirm" name="add_record">Add Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Health Record View Modal -->
    <div class="modal" id="viewRecordModal">
        <div class="modal-content">
            <span class="close" id="closeViewRecordModal">&times;</span>
            <div class="modal-header">
                <h3>Record Details</h3>
            </div>
            <div class="modal-body" id="viewRecordBody">
                <div class="record-detail">
                    <div class="detail-label">Date</div>
                    <div class="detail-value" id="viewDate"></div>
                </div>
                <div class="record-detail">
                    <div class="detail-label">Record Type</div>
                    <div class="detail-value" id="viewType"></div>
                </div>
                <div class="record-detail">
                    <div class="detail-label">Description</div>
                    <div class="detail-value" id="viewDescription"></div>
                </div>
                <div class="record-detail">
                    <div class="detail-label">Doctor</div>
                    <div class="detail-value" id="viewDoctor"></div>
                </div>
                <div class="record-detail">
                    <div class="detail-label">Hospital</div>
                    <div class="detail-value" id="viewHospital"></div>
                </div>
                <div class="record-detail">
                    <div class="detail-label">Notes</div>
                    <div class="detail-value" id="viewNotes"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal elements
            const addRecordBtn = document.getElementById('addRecordBtn');
            const recordModal = document.getElementById('recordModal');
            const closeRecordModal = document.getElementById('closeRecordModal');
            const cancelRecordBtn = document.querySelector('.btn-cancel-record');
            const recordsList = document.getElementById('recordsList');
            const viewRecordModal = document.getElementById('viewRecordModal');
            const closeViewRecordModal = document.getElementById('closeViewRecordModal');
            
            // Modal open/close functionality
            function openModal(modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            
            function closeModal(modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            
            // Event listeners for modals
            addRecordBtn.addEventListener('click', () => openModal(recordModal));
            closeRecordModal.addEventListener('click', () => closeModal(recordModal));
            closeViewRecordModal.addEventListener('click', () => closeModal(viewRecordModal));
            cancelRecordBtn.addEventListener('click', () => closeModal(recordModal));
            
            // Close modals when clicking outside
            window.addEventListener('click', (e) => {
                if (e.target === recordModal) closeModal(recordModal);
                if (e.target === viewRecordModal) closeModal(viewRecordModal);
            });
            
            // View record functionality
            recordsList.addEventListener('click', function(e) {
                if (e.target.closest('.btn-view')) {
                    const btn = e.target.closest('.btn-view');
                    
                    // Populate view modal
                    document.getElementById('viewDate').textContent = btn.dataset.date;
                    document.getElementById('viewType').textContent = btn.dataset.type;
                    document.getElementById('viewDescription').textContent = btn.dataset.description;
                    document.getElementById('viewDoctor').textContent = btn.dataset.doctor;
                    document.getElementById('viewHospital').textContent = btn.dataset.hospital;
                    document.getElementById('viewNotes').textContent = btn.dataset.notes;
                    
                    openModal(viewRecordModal);
                }
            });
            
            // Set today's date as default in form
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('recordDate').value = today;
            
            // Auto-hide notification after 3 seconds
            const notification = document.querySelector('.notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.animation = 'fadeOut 0.5s forwards';
                    setTimeout(() => notification.remove(), 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>