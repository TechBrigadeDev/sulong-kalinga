<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">

    <style>
        tbody .btn-link{
            color: black;
            text-decoration: none;
            font-size: 20px;
        }
        tbody td{
            vertical-align: middle;
        }
    </style>
</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="text-left">HEALTH MONITORING</div>
            <div class="container-fluid">
                <div class="row" id="home-content">
                    <div class="col-12 mb-2">
                        <!-- Combined Beneficiary Select and Time Range Filter -->
                        <div class="row mb-3 mt-1 justify-content-center">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <strong class="me-3">Filters</strong>
                                        </div>
                                        <button class="btn btn-primary btn-sm" id="applyFilterBtn">Apply Filters</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Beneficiary Select -->
                                            <div class="col-md-5 mb-1">
                                                <label for="beneficiarySelect" class="form-label">Select Beneficiary:</label>
                                                <select class="form-select" id="beneficiarySelect" name="beneficiary_id">
                                                    <option value="">All Beneficiaries</option>
                                                    <option value="1">Doe, John</option>
                                                    <option value="2">Smith, Jane</option>
                                                    <option value="3">Brown, Alice</option>
                                                    <option value="4">Johnson, Bob</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Time Range Select -->
                                            <div class="col-md-2 mb-1">
                                                <label for="timeRange" class="form-label">Time Range:</label>
                                                <select class="form-select" id="timeRange">
                                                    <option value="weeks">Weeks</option>
                                                    <option value="months">Months</option>
                                                    <option value="year">Year</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Week Filter (visible by default) -->
                                            <div class="col-md-5 mb-1" id="weekFilterContainer">
                                                <label for="monthSelect" class="form-label">Select Month:</label>
                                                <select class="form-select" id="monthSelect">
                                                    <option value="1">January</option>
                                                    <option value="2">February</option>
                                                    <option value="3">March</option>
                                                    <option value="4">April</option>
                                                    <option value="5">May</option>
                                                    <option value="6">June</option>
                                                    <option value="7">July</option>
                                                    <option value="8">August</option>
                                                    <option value="9">September</option>
                                                    <option value="10">October</option>
                                                    <option value="11">November</option>
                                                    <option value="12">December</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Month Range Filter (hidden by default) -->
                                            <div class="col-md-5 mb-1 d-none" id="monthRangeFilterContainer">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="startMonth" class="form-label">Start Month:</label>
                                                        <select class="form-select" id="startMonth">
                                                            <option value="1">January</option>
                                                            <option value="2">February</option>
                                                            <option value="3">March</option>
                                                            <option value="4">April</option>
                                                            <option value="5">May</option>
                                                            <option value="6">June</option>
                                                            <option value="7">July</option>
                                                            <option value="8">August</option>
                                                            <option value="9">September</option>
                                                            <option value="10">October</option>
                                                            <option value="11">November</option>
                                                            <option value="12">December</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="endMonth" class="form-label">End Month:</label>
                                                        <select class="form-select" id="endMonth">
                                                            <option value="1">January</option>
                                                            <option value="2">February</option>
                                                            <option value="3">March</option>
                                                            <option value="4">April</option>
                                                            <option value="5">May</option>
                                                            <option value="6">June</option>
                                                            <option value="7">July</option>
                                                            <option value="8">August</option>
                                                            <option value="9">September</option>
                                                            <option value="10">October</option>
                                                            <option value="11">November</option>
                                                            <option value="12">December</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Year Filter (hidden by default) -->
                                            <div class="col-md-5 mb-1 d-none" id="yearFilterContainer">
                                                <label for="yearSelect" class="form-label">Select Year:</label>
                                                <select class="form-select" id="yearSelect">
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Beneficiary Details Row -->
                        <div class="row mb-3" id="beneficiaryDetailsRow">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong class="text-center d-block">Beneficiary Details</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="row text-md-start">
                                            <div class="col-md-2 col-sm-6 mb-1">
                                                <p><strong>Age:</strong> <span id="beneficiaryAge">65</span></p>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-1">
                                                <p><strong>Address:</strong> <span id="beneficiaryAddress">123 Main St, City</span></p>
                                            </div>
                                            <div class="col-md-2 col-sm-6 mb-1">
                                                <p><strong>Gender:</strong> <span id="beneficiaryGender">Male</span></p>
                                            </div>
                                            <div class="col-md-2 col-sm-6 mb-1">
                                                <p><strong>Civil Status:</strong> <span id="beneficiaryCivilStatus">Married</span></p>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-1">
                                                <p><strong>Category:</strong> <span id="beneficiaryCategory">Frail</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reports Table Row (hidden by default) -->
                        <div class="row mb-3 d-none" id="reportsTableRow">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong class="text-center d-block">Health Reports</strong>
                                    </div>
                                    <div class="card-body p-0 pb-1">
                                    <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Author</th>
                <th>Report Type</th>
                <th class="d-none d-md-table-cell">Date Uploaded</th> <!-- Hide on small screens -->
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody id="reportsTableBody">
            <tr>
                <td>Dr. Smith</td>
                <td>Initial Assessment</td>
                <td class="d-none d-md-table-cell">2023-05-15</td> <!-- Hide on small screens -->
                <td class="text-center">
                    <button class="btn btn-link" title="View Report">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>Nurse Johnson</td>
                <td>Monthly Checkup</td>
                <td class="d-none d-md-table-cell">2023-06-20</td> <!-- Hide on small screens -->
                <td class="text-center">
                    <button class="btn btn-link" title="View Report">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>Dr. Williams</td>
                <td>Follow-up</td>
                <td class="d-none d-md-table-cell">2023-07-10</td> <!-- Hide on small screens -->
                <td class="text-center">
                    <button class="btn btn-link" title="View Report">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row align-items-center justify-content-center">
                            <!-- Row 1 -->
                            <div class="row mb-3">
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
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Chart variables
        let bloodPressureChart, heartRateChart, respiratoryRateChart, temperatureChart;
        
        // Initialize charts
        function initCharts() {
            // Sample data for weeks in a month
            const weekLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            
            // Blood Pressure Chart
            const bloodPressureCtx = document.getElementById('bloodPressureChart').getContext('2d');
            bloodPressureChart = new Chart(bloodPressureCtx, {
                type: 'line',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Blood Pressure (mmHg)',
                        data: [120, 125, 130, 128],
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
            heartRateChart = new Chart(heartRateCtx, {
                type: 'line',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Heart Rate (bpm)',
                        data: [72, 75, 78, 76],
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
            respiratoryRateChart = new Chart(respiratoryRateCtx, {
                type: 'line',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Respiratory Rate (breaths/min)',
                        data: [16, 18, 17, 16],
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
            temperatureChart = new Chart(temperatureCtx, {
                type: 'line',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Temperature (°C)',
                        data: [36.5, 36.7, 36.8, 36.6],
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
        }
        
        // Update time range filter visibility
        function updateTimeFilters() {
            const timeRange = document.getElementById('timeRange').value;
            
            // Hide all filters first
            document.getElementById('weekFilterContainer').classList.add('d-none');
            document.getElementById('monthRangeFilterContainer').classList.add('d-none');
            document.getElementById('yearFilterContainer').classList.add('d-none');
            
            // Show the appropriate filter
            if (timeRange === 'weeks') {
                document.getElementById('weekFilterContainer').classList.remove('d-none');
            } else if (timeRange === 'months') {
                document.getElementById('monthRangeFilterContainer').classList.remove('d-none');
            } else if (timeRange === 'year') {
                document.getElementById('yearFilterContainer').classList.remove('d-none');
            }
        }
        
        // Toggle beneficiary details and reports based on selection
        function toggleBeneficiaryDetails() {
            const beneficiaryId = document.getElementById('beneficiarySelect').value;
            const detailsRow = document.getElementById('beneficiaryDetailsRow');
            const reportsRow = document.getElementById('reportsTableRow');
            
            if (beneficiaryId) {
                detailsRow.classList.remove('d-none');
                reportsRow.classList.remove('d-none');
                
                // In a real app, you would fetch beneficiary details here
                // For demo purposes, we'll just update with sample data
                document.getElementById('beneficiaryAge').textContent = Math.floor(Math.random() * 30) + 50;
                document.getElementById('beneficiaryGender').textContent = 
                    beneficiaryId % 2 === 0 ? 'Female' : 'Male';
                document.getElementById('beneficiaryCivilStatus').textContent = 
                    ['Single', 'Married', 'Widowed', 'Separated'][Math.floor(Math.random() * 4)];
                document.getElementById('beneficiaryCategory').textContent = 
                    ['Frail', 'Bedridden', 'Disabled', 'Chronic Illness'][Math.floor(Math.random() * 4)];
                
                // Update reports table with sample data
                const reports = [
                    { author: 'Dr. Smith', type: 'Initial Assessment', date: '2023-05-15' },
                    { author: 'Nurse Johnson', type: 'Monthly Checkup', date: '2023-06-20' },
                    { author: 'Dr. Williams', type: 'Follow-up', date: '2023-07-10' }
                ];
                
                const tableBody = document.getElementById('reportsTableBody');
                tableBody.innerHTML = '';
                reports.forEach(report => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${report.author}</td>
                        <td>${report.type}</td>
                        <td class="d-none d-md-table-cell">${report.date}</td>
                        <td class="text-center">
                            <button class="btn btn-link" title="View Report">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                detailsRow.classList.add('d-none');
                reportsRow.classList.add('d-none');
            }
        }
        
        // Update charts based on selected filters
        function updateCharts() {
            const timeRange = document.getElementById('timeRange').value;
            const beneficiaryId = document.getElementById('beneficiarySelect').value;
            const beneficiaryText = beneficiaryId ? 
                document.getElementById('beneficiarySelect').options[document.getElementById('beneficiarySelect').selectedIndex].text : 
                'All Beneficiaries';
            
            // Toggle beneficiary details visibility
            toggleBeneficiaryDetails();
            
            // In a real application, you would fetch data from the server here
            // For this example, we'll simulate different data based on the time range
            
            let labels, bpData, hrData, rrData, tempData;
            
            if (timeRange === 'weeks') {
                const month = document.getElementById('monthSelect').value;
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                                    'July', 'August', 'September', 'October', 'November', 'December'];
                
                labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                bpData = [120 + month*2, 125 + month*2, 130 + month*2, 128 + month*2];
                hrData = [72 + month, 75 + month, 78 + month, 76 + month];
                rrData = [16 + month/3, 18 + month/3, 17 + month/3, 16 + month/3];
                tempData = [36.5 + month/10, 36.7 + month/10, 36.8 + month/10, 36.6 + month/10];
                
                // Update chart titles to show month and beneficiary
                document.querySelector('#bloodPressureChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Blood Pressure - ${monthNames[month-1]} (${beneficiaryText})`;
                document.querySelector('#heartRateChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Heart Rate - ${monthNames[month-1]} (${beneficiaryText})`;
                document.querySelector('#respiratoryRateChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Respiratory Rate - ${monthNames[month-1]} (${beneficiaryText})`;
                document.querySelector('#temperatureChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Temperature - ${monthNames[month-1]} (${beneficiaryText})`;
                
            } else if (timeRange === 'months') {
                const startMonth = parseInt(document.getElementById('startMonth').value);
                const endMonth = parseInt(document.getElementById('endMonth').value);
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                                    'July', 'August', 'September', 'October', 'November', 'December'];
                
                // Generate labels for the selected months
                labels = [];
                for (let i = startMonth; i <= endMonth; i++) {
                    labels.push(monthNames[i-1]);
                }
                
                // Generate sample data
                bpData = labels.map((_, index) => 120 + (index * 5));
                hrData = labels.map((_, index) => 70 + (index * 2));
                rrData = labels.map((_, index) => 15 + (index * 0.5));
                tempData = labels.map((_, index) => 36.0 + (index * 0.2));
                
                // Update chart titles to show month range and beneficiary
                const rangeText = `${monthNames[startMonth-1]} to ${monthNames[endMonth-1]}`;
                document.querySelector('#bloodPressureChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Blood Pressure - ${rangeText} (${beneficiaryText})`;
                document.querySelector('#heartRateChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Heart Rate - ${rangeText} (${beneficiaryText})`;
                document.querySelector('#respiratoryRateChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Respiratory Rate - ${rangeText} (${beneficiaryText})`;
                document.querySelector('#temperatureChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Temperature - ${rangeText} (${beneficiaryText})`;
                
            } else if (timeRange === 'year') {
                const year = document.getElementById('yearSelect').value;
                labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                // Generate sample data with some variation
                bpData = labels.map((_, index) => 120 + Math.sin(index) * 10);
                hrData = labels.map((_, index) => 70 + Math.cos(index) * 8);
                rrData = labels.map((_, index) => 15 + Math.sin(index/2) * 3);
                tempData = labels.map((_, index) => 36.5 + Math.cos(index/3) * 0.5);
                
                // Update chart titles to show year and beneficiary
                document.querySelector('#bloodPressureChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Blood Pressure - ${year} (${beneficiaryText})`;
                document.querySelector('#heartRateChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Heart Rate - ${year} (${beneficiaryText})`;
                document.querySelector('#respiratoryRateChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Respiratory Rate - ${year} (${beneficiaryText})`;
                document.querySelector('#temperatureChart').closest('.card').querySelector('.card-header strong').textContent = 
                    `Temperature - ${year} (${beneficiaryText})`;
            }
            
            // Update all charts with new data
            bloodPressureChart.data.labels = labels;
            bloodPressureChart.data.datasets[0].data = bpData;
            bloodPressureChart.update();
            
            heartRateChart.data.labels = labels;
            heartRateChart.data.datasets[0].data = hrData;
            heartRateChart.update();
            
            respiratoryRateChart.data.labels = labels;
            respiratoryRateChart.data.datasets[0].data = rrData;
            respiratoryRateChart.update();
            
            temperatureChart.data.labels = labels;
            temperatureChart.data.datasets[0].data = tempData;
            temperatureChart.update();
        }
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            // Set current month as default
            const currentMonth = new Date().getMonth() + 1;
            document.getElementById('monthSelect').value = currentMonth;
            document.getElementById('startMonth').value = currentMonth;
            document.getElementById('endMonth').value = currentMonth;
            // Set current year as default
            document.getElementById('yearSelect').value = new Date().getFullYear();
            
            // Event listeners
            document.getElementById('timeRange').addEventListener('change', updateTimeFilters);
            document.getElementById('applyFilterBtn').addEventListener('click', updateCharts);
            document.getElementById('beneficiarySelect').addEventListener('change', toggleBeneficiaryDetails);
            
            // Initial filter setup
            updateTimeFilters();
            toggleBeneficiaryDetails();
        });
    </script>

</body>
</html>