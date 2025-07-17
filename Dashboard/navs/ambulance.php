<?php
// ambulance_booking.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save booking data to session
    $_SESSION['ambulance_booking'] = [
        'emergency_type' => $_POST['emergency_type'] ?? '',
        'location_details' => $_POST['location_details'] ?? '',
        'medical_notes' => $_POST['medical_notes'] ?? '',
        'latitude' => $_POST['latitude'] ?? '',
        'longitude' => $_POST['longitude'] ?? '',
        'status' => 'Requested',
        'tracking_enabled' => true
    ];
    
    // Success message
    $success_message = "Ambulance request submitted! Help is on the way.";
}

// Retrieve saved data if available
$saved_data = $_SESSION['ambulance_booking'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambulance Booking - HealthConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8;
            color: #2c3e50;
            line-height: 1.6;
        }

        .app-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #dfe6e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #2a6bc9;
            box-shadow: 0 0 0 3px rgba(42, 107, 201, 0.1);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .btn-primary {
            background: #2a6bc9;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1e5aaf;
            transform: translateY(-2px);
        }

        .btn-emergency {
            background: #e74c3c;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-emergency:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #2a6bc9;
            color: #2a6bc9;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: #e3edfc;
        }

        .notification {
            animation: slideIn 0.5s, fadeOut 1s 4.5s forwards;
        }

        @keyframes slideIn {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        #map {
            height: 400px;
            border-radius: 8px;
            z-index: 1;
        }

        .emergency-card {
            border-left: 4px solid #e74c3c;
        }

        .emergency-type {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .type-cardiac {
            background: #fce8e6;
            color: #e74c3c;
        }

        .type-accident {
            background: #fff3e0;
            color: #ef6c00;
        }

        .type-stroke {
            background: #e3f2fd;
            color: #1976d2;
        }

        .type-breathing {
            background: #e8f5e9;
            color: #388e3c;
        }

        .type-other {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-requested {
            background-color: #e3edfc;
            color: #2a6bc9;
        }

        .status-dispatched {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .status-arrived {
            background-color: #e8f5e9;
            color: #27ae60;
        }
        
        .confirmation-card {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 2rem 0;
        }
        
        .confirmation-icon {
            font-size: 4rem;
            color: #27ae60;
            margin-bottom: 1.5rem;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        
        .summary-table th, .summary-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dfe6e9;
        }
        
        .summary-table th {
            background-color: #f8f9fa;
            font-weight: 500;
            color: #7f8c8d;
            width: 30%;
        }
        
        .location-permission {
            background-color: #fff3e0;
            border-left: 4px solid #ef6c00;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .location-icon {
            font-size: 1.5rem;
            color: #ef6c00;
            margin-right: 1rem;
        }
        
        .tracking-indicator {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: #e8f5e9;
            border-radius: 20px;
            color: #388e3c;
            font-weight: 500;
            margin-top: 1rem;
        }
        
        .tracking-pulse {
            width: 12px;
            height: 12px;
            background: #388e3c;
            border-radius: 50%;
            margin-right: 0.5rem;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(56, 142, 60, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(56, 142, 60, 0); }
            100% { box-shadow: 0 0 0 0 rgba(56, 142, 60, 0); }
        }
        
        .location-history {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            max-height: 150px;
            overflow-y: auto;
        }
        
        .location-history-item {
            padding: 0.5rem;
            border-bottom: 1px solid #dfe6e9;
            font-size: 0.85rem;
        }
        
        .location-history-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <div class="bg-blue-600 text-white p-2 rounded-lg mr-3">
                    <i class="fas fa-hospital text-xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-blue-800">Health<span class="text-blue-600">Connect</span></h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="font-medium"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?> <?= htmlspecialchars($_SESSION['last_name'] ?? '') ?></p>
                    <p class="text-sm text-gray-600">Patient ID: <?= $_SESSION['user_id'] ?? 'N/A' ?></p>
                </div>
                <div class="bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center text-blue-800 font-bold">
                    <?= strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1)) ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-8 px-4">
        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                <span><?= $success_message ?></span>
            </div>
        <?php endif; ?>
        
        <div class="card overflow-hidden">
            <div class="bg-red-600 text-white py-4 px-6">
                <h1 class="text-2xl font-bold">Emergency Ambulance Booking</h1>
                <p class="text-red-100 mt-1">
                    Request immediate medical assistance. Our team will be dispatched to your location.
                </p>
            </div>
            
            <form id="ambulanceForm" method="POST" class="p-6">
                <!-- Emergency Information -->
                <div class="mb-10">
                    <div class="flex items-center mb-6">
                        <div class="bg-red-100 text-red-800 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-ambulance"></i>
                        </div>
                        <h2 class="text-xl font-bold text-red-800">Emergency Details</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="form-group">
                            <label for="patientName">Patient Name</label>
                            <input type="text" id="patientName" class="form-control" readonly
                                   value="<?= htmlspecialchars($_SESSION['first_name'] ?? '') ?> <?= htmlspecialchars($_SESSION['last_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="patientPhone">Contact Phone</label>
                            <input type="tel" id="patientPhone" name="phone" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['phone'] ?? $_SESSION['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="emergencyType">Type of Emergency</label>
                        <select id="emergencyType" name="emergency_type" class="form-control" required>
                            <option value="">Select Emergency Type</option>
                            <option value="cardiac" <?= ($saved_data['emergency_type'] ?? '') === 'cardiac' ? 'selected' : '' ?>>Cardiac Emergency (Heart Attack)</option>
                            <option value="accident" <?= ($saved_data['emergency_type'] ?? '') === 'accident' ? 'selected' : '' ?>>Accident/Trauma</option>
                            <option value="stroke" <?= ($saved_data['emergency_type'] ?? '') === 'stroke' ? 'selected' : '' ?>>Stroke</option>
                            <option value="breathing" <?= ($saved_data['emergency_type'] ?? '') === 'breathing' ? 'selected' : '' ?>>Breathing Difficulties</option>
                            <option value="other" <?= ($saved_data['emergency_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other Medical Emergency</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="medicalNotes">Medical Notes (Symptoms, Conditions, etc.)</label>
                        <textarea id="medicalNotes" name="medical_notes" rows="3" class="form-control" placeholder="Describe the emergency situation and any important medical information"><?= htmlspecialchars($saved_data['medical_notes'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="locationDetails">Location Details (Landmarks, Floor, etc.)</label>
                        <textarea id="locationDetails" name="location_details" rows="2" class="form-control" placeholder="Provide additional location details to help our team find you"><?= htmlspecialchars($saved_data['location_details'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <!-- Location Section -->
                <div class="mb-10">
                    <div class="flex items-center mb-6">
                        <div class="bg-red-100 text-red-800 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h2 class="text-xl font-bold text-red-800">Real-Time Location Tracking</h2>
                    </div>
                    
                    <div class="location-permission">
                        <div class="location-icon">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Location Access Required</p>
                            <p class="text-gray-600">We need access to your precise location to dispatch the ambulance to your exact position. This will enable real-time tracking until help arrives.</p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <div id="map" class="mb-4"></div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="button" id="locateBtn" class="btn-primary">
                                <i class="fas fa-location-arrow mr-2"></i> Enable Real-Time Tracking
                            </button>
                            <button type="button" id="stopTrackingBtn" class="btn-outline hidden">
                                <i class="fas fa-stop mr-2"></i> Stop Tracking
                            </button>
                        </div>
                        
                        <div id="trackingStatus" class="tracking-indicator hidden">
                            <div class="tracking-pulse"></div>
                            <span>Live location tracking active</span>
                        </div>
                        
                        <div class="location-history hidden" id="locationHistory">
                            <p class="font-medium text-gray-700 mb-2">Location History:</p>
                            <div id="locationHistoryList"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden" id="manualLocationFields">
                        <div class="form-group">
                            <label for="latitude">Latitude</label>
                            <input type="text" id="latitude" name="latitude" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['latitude'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="longitude">Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['longitude'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg mt-6">
                        <div class="flex items-start">
                            <div class="bg-blue-100 text-blue-800 p-2 rounded-full mr-3 mt-1">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <p class="text-blue-800">
                                    <strong>Important:</strong> Our system will continuously track your location once enabled. 
                                    This allows our ambulance team to reach you faster, especially if you need to move to a safer location.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Confirmation -->
                <div class="flex justify-center pt-6">
                    <button type="submit" class="btn-emergency px-8 py-3 text-lg">
                        <i class="fas fa-ambulance mr-2"></i> Request Ambulance Now
                    </button>
                </div>
                
                <!-- Booking Confirmation -->
                <?php if (!empty($saved_data)): ?>
                <div class="confirmation-card mt-8">
                    <div class="confirmation-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Ambulance Request Confirmed</h3>
                    
                    <div class="flex justify-center mb-6">
                        <span class="status-badge status-requested">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Status: <?= $saved_data['status'] ?>
                        </span>
                    </div>
                    
                    <table class="summary-table">
                        <tr>
                            <th>Emergency Type</th>
                            <td>
                                <?php 
                                $type_classes = [
                                    'cardiac' => 'type-cardiac',
                                    'accident' => 'type-accident',
                                    'stroke' => 'type-stroke',
                                    'breathing' => 'type-breathing',
                                    'other' => 'type-other'
                                ];
                                $type_labels = [
                                    'cardiac' => 'Cardiac Emergency',
                                    'accident' => 'Accident/Trauma',
                                    'stroke' => 'Stroke',
                                    'breathing' => 'Breathing Difficulties',
                                    'other' => 'Other Medical Emergency'
                                ];
                                $type = $saved_data['emergency_type'] ?? '';
                                ?>
                                <span class="emergency-type <?= $type_classes[$type] ?? '' ?>">
                                    <i class="fas fa-<?= $type === 'cardiac' ? 'heart' : ($type === 'accident' ? 'car-crash' : ($type === 'stroke' ? 'brain' : ($type === 'breathing' ? 'lungs' : 'first-aid'))) ?> mr-2"></i>
                                    <?= $type_labels[$type] ?? 'Not specified' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Request Time</th>
                            <td><?= date('Y-m-d H:i:s') ?></td>
                        </tr>
                        <tr>
                            <th>Estimated Arrival</th>
                            <td>8-12 minutes</td>
                        </tr>
                        <tr>
                            <th>Ambulance ID</th>
                            <td>AMB-<?= rand(1000, 9999) ?></td>
                        </tr>
                        <?php if ($saved_data['tracking_enabled'] ?? false): ?>
                        <tr>
                            <th>Location Tracking</th>
                            <td class="text-green-600 font-medium">Active</td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-yellow-500 text-xl mt-1 mr-3"></i>
                            </div>
                            <div>
                                <p class="text-yellow-800">
                                    <strong>Important:</strong> Please stay at your location. Keep your phone accessible. 
                                    Our emergency team will contact you shortly for confirmation and updates.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-center space-x-4 mt-6">
                        <button type="button" onclick="location.href='dashboard.php'" class="btn-outline">
                            <i class="fas fa-home mr-2"></i> Back to Dashboard
                        </button>
                        <button type="button" onclick="location.href='emergency_contacts.php'" class="btn-primary">
                            <i class="fas fa-phone-alt mr-2"></i> Emergency Contacts
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="bg-blue-50 rounded-lg p-6 mt-8 flex flex-col md:flex-row items-center">
            <div class="bg-blue-100 text-blue-800 p-3 rounded-lg mb-4 md:mb-0 md:mr-6">
                <i class="fas fa-info-circle text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-blue-800 mb-2">Emergency Instructions</h3>
                <ul class="list-disc text-blue-700 pl-5 space-y-2">
                    <li>Stay calm and ensure your safety first</li>
                    <li>If possible, have someone wait at the entrance to guide the ambulance</li>
                    <li>Prepare any important medical information (allergies, medications)</li>
                    <li>Clear pathways for emergency personnel</li>
                    <li>Do not move the patient unless absolutely necessary</li>
                </ul>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">HealthConnect</h3>
                    <p class="text-gray-400">
                        Providing compassionate care and innovative medical solutions for healthier communities.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Emergency Services</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Ambulance Services</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Emergency Departments</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Urgent Care</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Emergency Contacts</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                            <span>123 Medical Center Drive<br>Healthville, HV 12345</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3"></i>
                            <span>Emergency: (555) 911-HELP</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>emergency@healthconnect.example</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Emergency Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">First Aid Guide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Emergency Procedures</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Hospital Locations</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Disaster Preparedness</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> HealthConnect Medical Center. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the map
            const map = L.map('map').setView([0, 0], 13);
            let marker = null;
            let watchId = null;
            const locationHistory = [];
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Elements
            const locateBtn = document.getElementById('locateBtn');
            const stopTrackingBtn = document.getElementById('stopTrackingBtn');
            const trackingStatus = document.getElementById('trackingStatus');
            const locationHistoryDiv = document.getElementById('locationHistory');
            const locationHistoryList = document.getElementById('locationHistoryList');
            const manualLocationFields = document.getElementById('manualLocationFields');
            
            // Set initial view to user's location if available
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    
                    map.setView([userLat, userLng], 15);
                    
                    // Add a marker for the user's location
                    marker = L.marker([userLat, userLng]).addTo(map)
                        .bindPopup('Your current location')
                        .openPopup();
                    
                    // Update form fields
                    document.getElementById('latitude').value = userLat;
                    document.getElementById('longitude').value = userLng;
                    
                    // Add to location history
                    addLocationToHistory(userLat, userLng);
                }, function(error) {
                    console.error('Error getting location:', error);
                    // Default to a central location if geolocation fails
                    map.setView([51.505, -0.09], 13);
                });
            } else {
                console.error('Geolocation is not supported by this browser.');
                map.setView([51.505, -0.09], 13);
            }
            
            // Locate button click handler
            locateBtn.addEventListener('click', function() {
                if (navigator.geolocation) {
                    // Start watching position for real-time updates
                    watchId = navigator.geolocation.watchPosition(
                        function(position) {
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;
                            
                            // Update map view
                            map.setView([userLat, userLng], 15);
                            
                            // Remove existing marker if any
                            if (marker) {
                                map.removeLayer(marker);
                            }
                            
                            // Add a new marker with a pulsing icon
                            marker = L.marker([userLat, userLng], {
                                icon: L.divIcon({
                                    className: 'pulse-icon',
                                    html: '<div class="pulse-dot"></div>',
                                    iconSize: [20, 20],
                                    iconAnchor: [10, 10]
                                })
                            }).addTo(map)
                            .bindPopup('Your current location')
                            .openPopup();
                            
                            // Update form fields
                            document.getElementById('latitude').value = userLat;
                            document.getElementById('longitude').value = userLng;
                            
                            // Add to location history
                            addLocationToHistory(userLat, userLng);
                            
                            // Show tracking status
                            trackingStatus.classList.remove('hidden');
                            locationHistoryDiv.classList.remove('hidden');
                            stopTrackingBtn.classList.remove('hidden');
                            locateBtn.disabled = true;
                            locateBtn.textContent = 'Tracking Active';
                            locateBtn.classList.remove('btn-primary');
                            locateBtn.classList.add('bg-green-600', 'text-white');
                        },
                        function(error) {
                            console.error('Error getting location:', error);
                            showNotification('Could not retrieve your location. Please try again or enter manually.', 'error');
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                    );
                    
                    showNotification('Real-time location tracking activated!', 'success');
                } else {
                    showNotification('Geolocation is not supported by your browser. Please enter location manually.', 'error');
                    manualLocationFields.classList.remove('hidden');
                }
            });
            
            // Stop tracking button
            stopTrackingBtn.addEventListener('click', function() {
                if (watchId && navigator.geolocation) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                    
                    // Reset UI
                    trackingStatus.classList.add('hidden');
                    stopTrackingBtn.classList.add('hidden');
                    locateBtn.disabled = false;
                    locateBtn.textContent = 'Enable Real-Time Tracking';
                    locateBtn.classList.remove('bg-green-600', 'text-white');
                    locateBtn.classList.add('btn-primary');
                    
                    showNotification('Location tracking stopped', 'info');
                }
            });
            
            // Add location to history
            function addLocationToHistory(lat, lng) {
                const now = new Date();
                const timeString = now.toLocaleTimeString();
                
                // Add to history array
                locationHistory.push({
                    lat: lat,
                    lng: lng,
                    time: timeString
                });
                
                // Update UI
                locationHistoryList.innerHTML = '';
                locationHistory.slice().reverse().forEach(loc => {
                    const item = document.createElement('div');
                    item.className = 'location-history-item';
                    item.innerHTML = `
                        <div class="font-medium">${loc.time}</div>
                        <div>Lat: ${loc.lat.toFixed(6)}, Lng: ${loc.lng.toFixed(6)}</div>
                    `;
                    locationHistoryList.appendChild(item);
                });
            }
            
            // Form validation
            const ambulanceForm = document.getElementById('ambulanceForm');
            ambulanceForm.addEventListener('submit', function(e) {
                // Check if location is set
                const lat = document.getElementById('latitude').value;
                const lng = document.getElementById('longitude').value;
                
                if (!lat || !lng) {
                    e.preventDefault();
                    showNotification('Please set your location using the GPS button or enter coordinates manually.', 'error');
                    return;
                }
                
                // Check emergency type
                const emergencyType = document.getElementById('emergencyType').value;
                if (!emergencyType) {
                    e.preventDefault();
                    showNotification('Please select the type of emergency.', 'error');
                    return;
                }
                
                // If tracking is active, stop it when form is submitted
                if (watchId && navigator.geolocation) {
                    navigator.geolocation.clearWatch(watchId);
                }
            });
            
            // Show notification function
            function showNotification(message, type) {
                // Remove existing notifications
                const existing = document.querySelector('.custom-notification');
                if (existing) existing.remove();
                
                const notification = document.createElement('div');
                notification.className = `custom-notification fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center ${
                    type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 
                    type === 'info' ? 'bg-blue-100 border border-blue-400 text-blue-700' : 
                    'bg-green-100 border border-green-400 text-green-700'
                }`;
                notification.innerHTML = `
                    <i class="fas ${
                        type === 'error' ? 'fa-exclamation-circle' : 
                        type === 'info' ? 'fa-info-circle' : 
                        'fa-check-circle'
                    } mr-2"></i>
                    <span>${message}</span>
                `;
                
                document.body.appendChild(notification);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>