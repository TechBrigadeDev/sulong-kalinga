<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">HEALTH MONITORING</div>
            <div class="container-fluid">
                <div class="row" id="home-content">
                    <div class="col-12 mb-2">
                        <div class="row mb-4 mt-2 justify-content-center">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group d-flex align-items-center justify-content-center">
                                    <label for="beneficiarySelect" class="form-label me-3">Select Beneficiary:</label>
                                    <select class="form-select" id="beneficiarySelect" name="beneficiary_id" onchange="fetchBeneficiaryData(this.value)" style="flex: 1;">
                                        <option value="">-- Select Beneficiary --</option>
                                        <option value="1">Doe, John</option>
                                        <option value="2">Smith, Jane</option>
                                        <option value="3">Brown, Alice</option>
                                        <option value="4">Johnson, Bob</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                
                            </div>
                        </div>
                        <div class="row align-items-center justify-content-center">
                            <!-- Row 1 -->
                            <div class="row mb-2">
                                <!-- Blood Pressure Chart -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header text-center">
                                            <strong>Blood Pressure</strong>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="bloodPressureChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <!-- Heart Rate Chart -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header text-center">
                                            <strong>Heart Rate</strong>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="heartRateChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="row">
                                <!-- Respiratory Rate Chart -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header text-center">
                                            <strong>Respiratory Rate</strong>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="respiratoryRateChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <!-- Temperature Chart -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header text-center">
                                            <strong>Temperature</strong>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="temperatureChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Blood Pressure Chart
        const bloodPressureCtx = document.getElementById('bloodPressureChart').getContext('2d');
        new Chart(bloodPressureCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Blood Pressure (mmHg)',
                    data: [120, 125, 130, 128, 122, 118],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Heart Rate Chart
        const heartRateCtx = document.getElementById('heartRateChart').getContext('2d');
        new Chart(heartRateCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Heart Rate (bpm)',
                    data: [72, 75, 78, 76, 74, 73],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Respiratory Rate Chart
        const respiratoryRateCtx = document.getElementById('respiratoryRateChart').getContext('2d');
        new Chart(respiratoryRateCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Respiratory Rate (breaths/min)',
                    data: [16, 18, 17, 16, 15, 16],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Temperature Chart
        const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
        new Chart(temperatureCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Temperature (°C)',
                    data: [36.5, 36.7, 36.8, 36.6, 36.5, 36.4],
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    </script>

</body>
</html>