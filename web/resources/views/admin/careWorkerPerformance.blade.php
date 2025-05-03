<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        .compact-chart {
            height: 220px !important;
            width: 100% !important;
        }
        .card-body {
            padding: 1rem;
        }
        .card-header {
            padding: 0.75rem 1rem;
        }
        .card-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        h5 {
            font-size: 1.1rem;
        }
        .table th, .table td {
            font-size: clamp(0.8rem, 1vw, 1rem);
        }

        .table th {
            font-weight: bold;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-left">
                <strong>CARE WORKER PERFORMANCE</strong>
            </div>
            <button class="btn btn-danger btn-md" id="exportPdfBtn">
                <i class="bi bi-file-earmark-pdf"></i> Export to PDF
            </button>
        </div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Summary Cards -->
                    <div class="row mb-2">
                        <div class="col-md-4 mb-2">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Total Care Hours</h5>
                                    <h2 class="text-primary" style="font-size: clamp(1.5rem, 2vw, 2rem);">248 hrs</h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">This month</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Active Care Workers</h5>
                                    <h2 class="text-success" style="font-size: clamp(1.5rem, 2vw, 2rem);">12</h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">Currently assigned</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Avg. Rating</h5>
                                    <h2 class="text-warning" style="font-size: clamp(1.5rem, 2vw, 2rem);">4.7 <small class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">/5</small></h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">Client satisfaction</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Charts Row -->
                    <div class="row mb-2">
                        <!-- Most Implemented Interventions (Now Bar Chart) -->
                        <div class="col-lg-6 mb-2">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Most Implemented Interventions</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="mostImplementedChart" height="200" width="400"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Avg. Time/Care Need Category (Now Doughnut Chart) -->
                        <div class="col-lg-6 mb-2">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Avg. Time/Care Need Category</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="timePerCategoryChart" height="200" width="400"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Second Charts Row -->
                    <div class="row mb-2">
                        <!-- Hours per Client -->
                        <div class="col-lg-6 mb-2">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Hours of Care Services by Client</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="clientHoursChart" height="200" width="400"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Intervention Time Breakdown -->
                        <div class="col-lg-6 mb-2">
                            <div class="card h-100">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Intervention Time Breakdown</h5>
                                    <select id="chartDataSelector" class="form-select dropdown-menu-end" style="font-size: clamp(0.8rem, 1vw, 1rem); width: 180px;">
                                        <option value="set1">Set 1: Mobility</option>
                                        <option value="set2">Set 2: Cognitive/Communication</option>
                                        <option value="set3">Set 3: Self-Sustainability</option>
                                        <option value="set4">Set 4: Disease/Therapy</option>
                                        <option value="set5">Set 5: Socical Contact</option>
                                        <option value="set6">Set 6: Outdoor Activities</option>
                                        <option value="set7">Set 7: Household Activities</option>
                                    </select>
                                </div>
                                <div class="card-body">
                                    <canvas id="interventionTimeChart" height="200" width="400"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performing Care Worker -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Top Performing Care Workers</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Care Worker</th>
                                                    <th>Hours Worked</th>
                                                    <th>Clients Served</th>
                                                    <th>Interventions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Sarah Johnson</td>
                                                    <td>42</td>
                                                    <td>5</td>
                                                    <td>28</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Michael Chen</td>
                                                    <td>38</td>
                                                    <td>4</td>
                                                    <td>25</td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>David Wilson</td>
                                                    <td>35</td>
                                                    <td>4</td>
                                                    <td>23</td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>Emily Rodriguez</td>
                                                    <td>32</td>
                                                    <td>3</td>
                                                    <td>20</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Base chart configuration
            const baseChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            };

            // Configuration for bar charts (with grids)
            const barChartOptions = {
                ...baseChartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true
                        },
                        ticks: {
                            font: {
                                size: 9
                            }
                        },
                        title: {
                            display: true,
                            text: 'Count',
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            };

            // Configuration for doughnut/pie charts (no grids)
            const circularChartOptions = {
                ...baseChartOptions,
                scales: {
                    y: {
                        display: false,
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        display: false,
                        grid: {
                            display: false
                        }
                    }
                }
            };

            // Most Implemented Interventions (Bar Chart)
            const mostImplementedCtx = document.getElementById('mostImplementedChart').getContext('2d');
            new Chart(mostImplementedCtx, {
                type: 'bar',
                data: {
                    labels: ['Assist in Sitting', 'Bathing', 'Light Exercise', 'Gardening'],
                    datasets: [{
                        label: 'Times Implemented',
                        data: [120, 95, 80, 65],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    ...barChartOptions,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Avg. Time/Care Need Category (Doughnut Chart)
            const timePerCategoryCtx = document.getElementById('timePerCategoryChart').getContext('2d');
            new Chart(timePerCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Mobility', 'Cognitive', 'Self-Sustainability', 'Disease Therapy', 'Social Contact', 'Outdoor', 'Housekeeping'],
                    datasets: [{
                        data: [45, 30, 35, 25, 20, 15, 40],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(199, 199, 199, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    ...circularChartOptions,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw} min`;
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

            // Hours per Client (Horizontal Bar Chart)
            const clientHoursCtx = document.getElementById('clientHoursChart').getContext('2d');
            new Chart(clientHoursCtx, {
                type: 'bar',
                data: {
                    labels: ['R. Smith', 'M. Johnson', 'T. Williams', 'J. Brown', 'R. Davis'],
                    datasets: [{
                        label: 'Hours',
                        data: [56, 48, 42, 38, 35],
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    ...barChartOptions,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Intervention Time Breakdown (Pie Chart)
            const interventionTimeCtx = document.getElementById('interventionTimeChart').getContext('2d');
            new Chart(interventionTimeCtx, {
                type: 'pie',
                data: {
                    labels: ['Personal Care', 'Medication', 'Meal Prep', 'Mobility', 'Companionship'],
                    datasets: [{
                        data: [45, 15, 30, 20, 60],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    ...circularChartOptions,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw} min`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>