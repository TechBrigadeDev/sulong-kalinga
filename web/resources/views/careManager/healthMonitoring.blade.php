<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Health Monitoring</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/healthMonitoring.css') }}">
</head>
<body>

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-left">
                <strong>{{ T::translate('HEALTH MONITORING', 'PAGSUBAYBAY SA KALUSUGAN')}}</strong>
            </div>
            <button class="btn btn-danger btn-md" id="exportPdfBtn">
                <i class="bi bi-file-earmark-pdf"></i> {{ T::translate('Export to PDF', 'I-Export sa PDF')}}
            </button>
        </div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12 mb-2">
                    <!-- Combined Beneficiary Select and Time Range Filter -->
                    <form id="filterForm" action="{{ route('care-manager.health.monitoring.index') }}" method="GET">
                        <div class="row mb-3 mt-1 justify-content-center">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <strong class="me-3">{{ T::translate('Filters', 'Pagsala')}}</strong>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm" id="applyFilterBtn">{{ T::translate('Apply Filters', 'I-apply ang Pagsala')}}</button>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="row g-2">
                                            <!-- Beneficiary Select -->
                                            <div class="col">
                                                <label for="beneficiarySelect" class="form-label">{{ T::translate('Select Beneficiary:', 'Pumili ng Benepisyaryo')}}</label>
                                                <select class="form-select" id="beneficiarySelect" name="beneficiary_id">
                                                    <option value="">{{ T::translate('All Beneficiaries', 'Lahat ng Benepisyaryo')}}</option>
                                                    @foreach($beneficiaries as $beneficiary)
                                                        <option value="{{ $beneficiary->beneficiary_id }}" {{ $selectedBeneficiaryId == $beneficiary->beneficiary_id ? 'selected' : '' }}>
                                                            {{ $beneficiary->last_name }}, {{ $beneficiary->first_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Municipalities Select -->
                                            <div class="col">
                                                <label for="municipalitySelect" class="form-label">{{ T::translate('Select Municipality:', 'Pumili ng Munisipalidad')}}</label>
                                                <select class="form-select" id="municipalitySelect" name="municipality_id">
                                                    <option value="">{{ T::translate('All Municipalities', 'Lahat ng Munisipalidad')}}</option>
                                                    @foreach($municipalities as $municipality)
                                                        <option value="{{ $municipality->municipality_id }}" {{ $selectedMunicipalityId == $municipality->municipality_id ? 'selected' : '' }}>
                                                            {{ $municipality->municipality_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Time Range Select -->
                                            <div class="col">
                                                <label for="timeRange" class="form-label">{{ T::translate('Time Range:', 'Saklaw ng Oras:')}}</label>
                                                <select class="form-select" id="timeRange" name="time_range">
                                                    <option value="weeks" {{ $selectedTimeRange == 'weeks' ? 'selected' : '' }}>{{ T::translate('Monthly', 'Buwanan')}}</option>
                                                    <option value="months" {{ $selectedTimeRange == 'months' ? 'selected' : '' }}>{{ T::translate('Range of Months', 'Hanay ng mga Buwan')}}</option>
                                                    <option value="year" {{ $selectedTimeRange == 'year' ? 'selected' : '' }}>{{ T::translate('Yearly', 'Taunan')}}</option>
                                                </select>
                                            </div>

                                            <!-- Week Filter (visible by default) -->
                                            <div class="col {{ $selectedTimeRange != 'weeks' ? 'd-none' : '' }}" id="weekFilterContainer">
                                                <label for="monthSelect" class="form-label">{{ T::translate('Select Month:', 'Pumili ng Buwan:')}}</label>
                                                <div class="d-flex">
                                                    <select class="form-select" id="monthSelect" name="month" style="width: 60%;">
                                                        @for($i = 1; $i <= 12; $i++)
                                                            <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                    <select class="form-select ms-2" id="yearSelect" name="monthly_year" style="width: 40%;">
                                                        @foreach($availableYears as $year)
                                                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                                {{ $year }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Month Range Filter (hidden by default) -->
                                            <div class="col {{ $selectedTimeRange != 'months' ? 'd-none' : '' }}" id="monthRangeFilterContainer">
                                                <div class="row g-2">
                                                    <div class="col-5">
                                                        <label for="startMonth" class="form-label">{{ T::translate('Start Month:', 'Simula na Buwan:')}}</label>
                                                        <select class="form-select" id="startMonth" name="start_month">
                                                            @for($i = 1; $i <= 12; $i++)
                                                                <option value="{{ $i }}" {{ $selectedStartMonth == $i ? 'selected' : '' }}>
                                                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-5">
                                                        <label for="endMonth" class="form-label">{{ T::translate('End Month:', 'Katapusan na Buwan:')}}</label>
                                                        <select class="form-select" id="endMonth" name="end_month">
                                                            @for($i = 1; $i <= 12; $i++)
                                                                <option value="{{ $i }}" {{ $selectedEndMonth == $i ? 'selected' : '' }}>
                                                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-2">
                                                        <label for="rangeYearSelect" class="form-label">{{ T::translate('Year:', 'Taon:')}}</label>
                                                        <select class="form-select" id="rangeYearSelect" name="range_year">
                                                            @foreach($availableYears as $year)
                                                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                                    {{ $year }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Year Filter (hidden by default) -->
                                            <div class="col {{ $selectedTimeRange != 'year' ? 'd-none' : '' }}" id="yearFilterContainer">
                                                <label for="yearOnlySelect" class="form-label">{{ T::translate('Select Year:', 'Pumili ng Taon:')}}</label>
                                                <select class="form-select" id="yearOnlySelect" name="year">
                                                    @foreach($availableYears as $year)
                                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-3">
                        <!-- Total Care Hours -->
                        <div class="col-md-4 mb-2">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">{{ T::translate('Total Care Hours', 'Kabuuan na Oras ng Pangangalaga')}}</h5>
                                    <h2 class="text-primary" style="font-size: clamp(1.5rem, 2vw, 2rem);">
                                        {{ isset($totalCareTime) ? $totalCareTime : '0 hrs' }}
                                    </h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">{{ $dateRangeLabel ?? 'All time' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Beneficiaries -->
                        <div class="col-md-4 mb-2">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">{{ T::translate('Total Beneficiaries', 'Kabuuang Benepisyaryo')}}</h5>
                                    <h2 class="text-success" style="font-size: clamp(1.5rem, 2vw, 2rem);">
                                        {{ $totalPopulation ?? 0 }}
                                    </h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">{{ T::translate('Currently active', 'Kasalukuyang Aktibo')}}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Care Services -->
                        <div class="col-md-4 mb-2">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">{{ T::translate('Total Care Services', 'Kabuuang Serbisyo sa Pangangalaga')}}</h5>
                                    @php
                                        $totalInterventions = 0;
                                        foreach($careServicesSummary ?? [] as $category) {
                                            if(isset($category['interventions'])) {
                                                foreach($category['interventions'] as $intervention) {
                                                    $totalInterventions += isset($intervention['implementations']) ? $intervention['implementations'] : 0;
                                                }
                                            }
                                        }
                                    @endphp
                                    <h2 class="text-warning" style="font-size: clamp(1.5rem, 2vw, 2rem);">{{ $totalInterventions }}</h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">{{ $dateRangeLabel ?? 'All time' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Beneficiary Details Row -->
                    <div class="row mb-3 {{ !$selectedBeneficiary ? 'd-none' : '' }}" id="beneficiaryDetailsRow">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <strong class="text-center d-block">{{ T::translate('Beneficiary Details', 'Mga Detalye ng Benepisyaryo')}}</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row mb-1">
                                        <div class="col-md-4 col-sm-9 position-relative">
                                            <label for="beneficiaryName" class="form-label">{{ T::translate('Beneficiary Name', 'Pangalan ng Benepisyaryo')}}</label>
                                            <input type="text" class="form-control" id="beneficiaryName" 
                                                value="{{ $selectedBeneficiary ? $selectedBeneficiary->first_name . ' ' . $selectedBeneficiary->last_name : '' }}" 
                                                readonly data-bs-toggle="tooltip" title="{{ T::translate('Edit in General Care Plan', 'I-edit sa General Care Plan')}}">    
                                        </div>
                                        <div class="col-md-2 col-sm-3">
                                            <label for="age" class="form-label">{{ T::translate('Age', 'Edad')}}</label>
                                            <input type="text" class="form-control" id="age" 
                                                value="{{ $selectedBeneficiary ? \Carbon\Carbon::parse($selectedBeneficiary->birthday)->age : '' }}" 
                                                readonly data-bs-toggle="tooltip" title="{{ T::translate('Edit in General Care Plan', 'I-edit sa General Care Plan')}}">                                          
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label for="birthDate" class="form-label">{{ T::translate('Birthdate', 'Kaarawan')}}</label>
                                            <input type="text" class="form-control" id="birthDate" 
                                                value="{{ $selectedBeneficiary ? \Carbon\Carbon::parse($selectedBeneficiary->birthday)->format('F j, Y') : '' }}" 
                                                readonly data-bs-toggle="tooltip" title="{{ T::translate('Edit in General Care Plan', 'I-edit sa General Care Plan')}}">                                          
                                        </div>
                                        <div class="col-md-3 col-sm-6 position-relative">
                                            <label for="gender" class="form-label">{{ T::translate('Gender', 'Kasarian')}}</label>
                                            <input type="text" class="form-control" id="gender" 
                                                value="{{ $selectedBeneficiary ? $selectedBeneficiary->gender : '' }}" 
                                                readonly data-bs-toggle="tooltip" title="{{ T::translate('Edit in General Care Plan', 'I-edit sa General Care Plan')}}">
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-3 col-sm-4 position-relative">
                                            <label for="civilStatus" class="form-label">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}</label>
                                            <input type="text" class="form-control" id="civilStatus" 
                                                value="{{ $selectedBeneficiary ? $selectedBeneficiary->civil_status : '' }}" 
                                                readonly data-bs-toggle="tooltip" title="{{ T::translate('Edit in General Care Plan', 'I-edit sa General Care Plan')}}">
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <label for="address" class="form-label">{{ T::translate('Address', 'Address')}}</label>
                                            <input type="text" class="form-control" id="address" 
                                                value="{{ $selectedBeneficiary ? $selectedBeneficiary->street_address . ', ' . ($selectedBeneficiary->barangay->barangay_name ?? 'N/A') . ', ' . ($selectedBeneficiary->municipality->municipality_name ?? 'N/A') : '' }}" 
                                                readonly data-bs-toggle="tooltip" title="{{ T::translate('Edit in General Care Plan', 'I-edit sa General Care Plan')}}">
                                        </div>
                                        <div class="col-md-3 col-sm-12">
                                            <label for="category" class="form-label">{{ T::translate('Category', 'Kategorya')}}</label>
                                            <input type="text" class="form-control" id="category" 
                                                value="{{ $selectedBeneficiary ? ($selectedBeneficiary->category->category_name ?? 'N/A') : '' }}"
                                                readonly data-bs-toggle="tooltip" title="{{ T::translate('Edit in General Care Plan', 'I-edit sa General Care Plan')}}">
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-12 col-sm-12">
                                            <label for="totalCareHours" class="form-label">{{ T::translate('Total Care Hours Received', 'Kabuuang Oras ng Pangangalaga na Natanggap')}}</label>
                                            <input type="text" class="form-control" id="totalCareHours" 
                                            value="{{ $totalCareTime ?? '0 hrs' }}" 
                                            readonly style="font-weight: bold; background-color: #f8f9fa; color: #0d6efd; border: 1px solid #dee2e6;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
      
                    <!-- Statistics Table Row -->
                    <div class="row mb-3" id="reportsTableRow" {{ $selectedBeneficiary ? 'style=display:none;' : '' }}>
                        <div class="col-12">
                            <div class="card shadow-sm" id="healthStatistics">
                                <div class="card-header">
                                    <strong class="text-center d-block">{{ T::translate('Health Statistics', 'Istatistika ng Kalusugan')}}</strong>
                                </div>
                                <div class="card-body p-0 pb-1">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ T::translate('Category', 'Kategorya')}}</th>
                                                    <th class="text-center">{{ T::translate('Age', 'Edad')}} 60-69</th>
                                                    <th class="text-center">{{ T::translate('Age', 'Edad')}} 70-79</th>
                                                    <th class="text-center">{{ T::translate('Age', 'Edad')}} 80-89</th>
                                                    <th class="text-center">{{ T::translate('Age', 'Edad')}} 90+</th>
                                                    <th class="text-center">{{ T::translate('Male', 'Lalaki')}}</th>
                                                    <th class="text-center">{{ T::translate('Female', 'Babae')}}</th>
                                                    <th class="text-center">{{ T::translate('Single', 'Walang Asawa')}}</th>
                                                    <th class="text-center">{{ T::translate('Married', 'May Asawa')}}</th>
                                                    <th class="text-center">{{ T::translate('Widowed', 'Balo')}}</th>
                                                    <th class="text-center">{{ T::translate('Percentage', 'Porsyento')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reportsTableBody">
                                                @if(count($healthStatistics) > 0)
                                                    @foreach($healthStatistics as $category => $stats)
                                                        <tr>
                                                            <td>{{ $category }}</td>
                                                            <td class="text-center">{{ $stats['age_60_69'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['age_70_79'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['age_80_89'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['age_90_plus'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['male'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['female'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['single'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['married'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['widowed'] ?? 0 }}</td>
                                                            <td class="text-center">{{ $stats['percentage'] }}%</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td><strong>{{ T::translate('Total', 'Kabuuan')}}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['age_60_69'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['age_70_79'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['age_80_89'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['age_90_plus'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['male'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['female'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['single'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['married'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totals['widowed'] ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>100%</strong></td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="11" class="text-center">{{ T::translate('No data available', 'Walang datos na available.')}}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Care Services Summary Section -->
                    <div class="row mt-3 mb-3" id="careServicesSummaryRow">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header text-center">
                                    <strong>{{ T::translate('Care Services Summary', 'Buod ng Serbisyong Pangangalaga')}}</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div id="careServicesCarousel" class="carousel slide" data-bs-interval="false">
                                        <div class="carousel-inner">
                                            @foreach($careCategories as $index => $category)
                                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                    <table class="table table-bordered table-sm care-services-table">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="3" class="text-center bg-light position-relative">
                                                                    <button class="btn btn-sm btn-outline-secondary position-absolute start-0" data-bs-target="#careServicesCarousel" data-bs-slide="prev">
                                                                        <i class="bi bi-chevron-left"></i>
                                                                    </button>
                                                                    {{ $category->care_category_name }}
                                                                    <button class="btn btn-sm btn-outline-secondary position-absolute end-0" data-bs-target="#careServicesCarousel" data-bs-slide="next">
                                                                        <i class="bi bi-chevron-right"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>{{ T::translate('Intervention Implemented', 'Isinagawang Interbensiyon')}}</th>
                                                                <th class="text-center" style="width: 20%;">{{ T::translate('Times Implemented', 'Beses na Isinagawa')}}</th>
                                                                <th class="text-center" style="width: 20%;">{{ T::translate('Total Hours', 'Kabuuang Oras')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($careServicesSummary[$category->care_category_id]) && 
                                                                $careServicesSummary[$category->care_category_id]['has_interventions'])
                                                                @foreach($careServicesSummary[$category->care_category_id]['interventions'] as $intervention)
                                                                    <tr>
                                                                        <td class="align-top">{{ $intervention['description'] }}</td>
                                                                        <td class="text-center align-middle">{{ $intervention['implementations'] }}</td>
                                                                        <td class="text-center align-middle">{{ $intervention['formatted_duration'] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="3" class="text-center">{{ T::translate('No interventions implemented for this category', 'Walang interbensiyon ang isinagawa sa kategoryang ito.')}}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Section - KEEPING AS IS PER REQUEST -->
                    <div class="row align-items-center justify-content-center">
                        <!-- Row 1 -->
                        <div class="row mb-3">
                            <!-- Blood Pressure Chart -->
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center">
                                        <strong>{{ T::translate('Blood Pressure', 'Presyon ng Dugo')}}</strong>
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
                                        <strong>{{ T::translate('Heart Rate', 'Bilis ng Tibok ng Puso')}}</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="heartRateChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2 -->
                        <div class="row mb-3">
                            <!-- Respiratory Rate Chart -->
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center">
                                        <strong>{{ T::translate('Respiratory Rate', 'Bilis ng Paghinga')}}</strong>
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
                                        <strong>{{ T::translate('Temperature', 'Temperatura')}}</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="temperatureChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 3 - Statistical Charts (only for group view) -->
                        <div class="row mb-3 justify-content-center" id="statisticalChartsRow" {{ $selectedBeneficiary ? 'style=display:none;' : '' }}>
                            <!-- Medical Condition Pie Chart Section -->
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center">
                                        <strong>{{ T::translate('Medical Conditions', 'Kondisyong Medikal')}}</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="medicalConditionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Illnesses Recorded Chart Section -->
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center">
                                        <strong>{{ T::translate('Illnesses Recorded', 'Naitalang mga Sakit')}}</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="illnessesChart"></canvas>
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
    let bloodPressureChart, heartRateChart, respiratoryRateChart, temperatureChart, medicalConditionChart;
    let currentBeneficiaryName = "{{ T::translate('All Beneficiaries', 'Lahat ng Benepisyaryo')}}";

    // Initialize the Medical Condition Pie Chart
    function initMedicalConditionChart() {
        const ctx = document.getElementById('medicalConditionChart').getContext('2d');
        medicalConditionChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    "{{ T::translate('Frail', 'Mahina')}}", 
                    "{{ T::translate('Bedridden', 'Nakahiga')}}", 
                    "{{ T::translate('Disabled', 'May Kapansanan')}}", 
                    "{{ T::translate('Chronic Illness', 'Malalang Sakit')}}"
                ],
                datasets: [{
                    data: [40, 30, 20, 10], // Example data
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: "{{ T::translate('Medical Conditions Distribution', 'Distribusyon ng Kondisyong Medikal')}}"
                    }
                }
            }
        });
    }

    // Update time range filters visibility
    document.addEventListener('DOMContentLoaded', function() {
        // Time range change handler
        document.getElementById('timeRange').addEventListener('change', function() {
            const selectedRange = this.value;
            
            // Hide all time range filters
            document.getElementById('weekFilterContainer').classList.add('d-none');
            document.getElementById('monthRangeFilterContainer').classList.add('d-none');
            document.getElementById('yearFilterContainer').classList.add('d-none');
            
            // Show the appropriate filter based on selection
            if (selectedRange === 'weeks') {
                document.getElementById('weekFilterContainer').classList.remove('d-none');
            } else if (selectedRange === 'months') {
                document.getElementById('monthRangeFilterContainer').classList.remove('d-none');
            } else if (selectedRange === 'year') {
                document.getElementById('yearFilterContainer').classList.remove('d-none');
            }
        });
        
        // Initialize carousel
        const careServicesCarousel = new bootstrap.Carousel(document.getElementById('careServicesCarousel'), {
            interval: false // Don't auto-rotate
        });

        // Initialize charts with data or default values
        initializeCharts();

        // Initialize statistical charts (only if not showing a specific beneficiary)
        if (!document.getElementById('beneficiaryDetailsRow') || 
            document.getElementById('beneficiaryDetailsRow').classList.contains('d-none')) {
            initStatisticalCharts();
        }

        // PDF export button
        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            // Create a form to submit the current filter values for PDF export
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/care-manager/exports/health-monitoring-pdf';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Copy all filter values from the filter form
            const filterForm = document.getElementById('filterForm');
            const filterInputs = filterForm.querySelectorAll('select, input');
            
            filterInputs.forEach(input => {
                if (input.name && input.value) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = input.name;
                    hiddenField.value = input.value;
                    form.appendChild(hiddenField);
                }
            });
            
            // Append form to body and submit
            document.body.appendChild(form);
            form.method = 'POST'; // Explicitly set method again to ensure POST is used
            form.submit();
        });
    });

        // Initialize all charts
        function initializeCharts() {
        // Chart data from controller - use empty arrays as fallbacks if undefined
        const chartLabels = @json($chartLabels ?? []);
        const bloodPressureData = @json($bloodPressureData ?? []);
        const heartRateData = @json($heartRateData ?? []);
        const respiratoryRateData = @json($respiratoryRateData ?? []);
        const temperatureData = @json($temperatureData ?? []);
        
        // Use default data if empty
        const defaultLabels = chartLabels.length > 0 ? chartLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const defaultBP = bloodPressureData.length > 0 ? bloodPressureData : [120, 118, 122, 125, 119, 121];
        const defaultHR = heartRateData.length > 0 ? heartRateData : [72, 75, 73, 70, 74, 76];
        const defaultRR = respiratoryRateData.length > 0 ? respiratoryRateData : [16, 17, 15, 16, 18, 17];
        const defaultTemp = temperatureData.length > 0 ? temperatureData : [36.5, 36.6, 36.4, 36.7, 36.5, 36.8];
        
        try {
            console.log("Initializing charts with labels:", defaultLabels);
            console.log("BP Data:", defaultBP);
            
            // Blood Pressure Chart
            const bloodPressureCtx = document.getElementById('bloodPressureChart').getContext('2d');
            new Chart(bloodPressureCtx, {
                type: 'line',
                data: {
                    labels: defaultLabels,
                    datasets: [{
                        label: "{{ T::translate('Blood Pressure (Systolic mmHg)', 'Presyon ng Dugo (Systolic mmHg)')}}",
                        data: defaultBP,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: "{{ T::translate('Blood Pressure Readings', 'Mga Pagbabasa ng Presyon ng Dugo')}}"
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            suggestedMin: 100,
                            suggestedMax: 160
                        }
                    }
                }
            });

            // Heart Rate Chart
            const heartRateCtx = document.getElementById('heartRateChart').getContext('2d');
            new Chart(heartRateCtx, {
                type: 'line',
                data: {
                    labels: defaultLabels,
                    datasets: [{
                        label: "{{ T::translate('Heart Rate (bpm)', 'Bilis ng Tibok ng Puso (bpm)')}}",
                        data: defaultHR,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: '{{ T::translate('Heart Rate Readings','Pagbabasa ng Bilis ng Tibok ng Puso')}}'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            suggestedMin: 60,
                            suggestedMax: 100
                        }
                    }
                }
            });

            // Respiratory Rate Chart
            const respiratoryRateCtx = document.getElementById('respiratoryRateChart').getContext('2d');
            new Chart(respiratoryRateCtx, {
                type: 'line',
                data: {
                    labels: defaultLabels,
                    datasets: [{
                        label: '{{ T::translate('Respiratory Rate', 'Bilis ng Paghinga')}} (breaths/min)',
                        data: defaultRR,
                        borderColor: 'rgb(255, 206, 86)',
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: '{{ T::translate('Respiratory Rate Readings', 'Pagbabasa ng Bilis ng Paghinga')}}'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            suggestedMin: 10,
                            suggestedMax: 25
                        }
                    }
                }
            });

            // Temperature Chart
            const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
            new Chart(temperatureCtx, {
                type: 'line',
                data: {
                    labels: defaultLabels,
                    datasets: [{
                        label: '{{ T::translate('Temperature', 'Temperatura')}} (°C)',
                        data: defaultTemp,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: '{{ T::translate('Body Temperature Readings', 'Pagbabasa ng Temperatura ng Katawan')}}'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            suggestedMin: 35.5,
                            suggestedMax: 38
                        }
                    }
                }
            });
            
            console.log("Charts initialized successfully");
        } catch (error) {
            console.error("Error initializing charts:", error);
        }
    }

    // Initialize both statistical charts
    function initStatisticalCharts() {
        // Medical Condition Pie Chart
        const medicalConditionCtx = document.getElementById('medicalConditionChart').getContext('2d');
        const medicalConditionData = @json($medicalConditionStats ?? []);
        
        if (Object.keys(medicalConditionData).length > 0) {
            new Chart(medicalConditionCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(medicalConditionData),
                    datasets: [{
                        data: Object.values(medicalConditionData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: '{{ T::translate('Top 10 Medical Conditions Distribution', 'Pamamahagi ng 10 Nangungunang Kondisyong Medikal')}}',
                            font: { size: 14 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('medicalConditionChart').parentElement.innerHTML = 
                '<div class="text-center text-muted pt-5 pb-5">{{ T::translate('No medical condition data available', 'Walang medikal na kondisyon ang available')}}</div>';
        }
        
        // Illnesses Chart
        const illnessesCtx = document.getElementById('illnessesChart').getContext('2d');
        const illnessesData = @json($illnessStats ?? []);
        
        if (Object.keys(illnessesData).length > 0) {
            new Chart(illnessesCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(illnessesData),
                    datasets: [{
                        data: Object.values(illnessesData),
                        backgroundColor: [
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 159, 64, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: '{{ T::translate('Top 10 Reported Illnesses', '10 Nangungunang Naitalang mga Sakit')}}',
                            font: { size: 14 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('illnessesChart').parentElement.innerHTML = 
                '<div class="text-center text-muted pt-5 pb-5">{{ T::translate('No illness data available', 'Walang mga datos ng mga sakit ang available')}}</div>';
        }
    }

</script>

</body>
</html>