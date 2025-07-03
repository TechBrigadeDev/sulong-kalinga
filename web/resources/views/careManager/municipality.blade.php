<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\careManager\municipality.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipality & Barangay Information</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/municipality.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Only for this page: make the table scrollable */
        .municipality-table-scroll {
            max-height: 60vh; /* Adjust as needed */
            overflow-y: auto;
        }
    </style>
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="text-left">{{ T::translate('MUNICIPALITY & BARANGAY INFORMATION', 'MUNISIPALIDAD AT IMPORASYONG NG BARANGAY')}}</div>

        <!-- Display success and error messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="container-fluid text-center">
            <!-- Search and Filter Row -->
            <div class="row mb-3 align-items-center">
                <!-- Search Bar with Button -->
                <div class="col-12 col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="{{ T::translate('Search barangay or municipality', 'Maghanap ng barangay o munisipalidad')}}..." id="searchBar">
                        <button class="btn btn-primary" type="button" id="searchButton">
                            {{ T::translate('Search', 'Maghanap')}}
                        </button>
                    </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-funnel"></i>
                        </span>
                        <select class="form-select" id="filterDropdown">
                            <option value="">{{ T::translate('All Municipalities', 'Lahat ng Munisipalidad')}}</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->municipality_id }}">{{ $municipality->municipality_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Read-only notice -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ T::translate('This is a read-only view. For changes to municipalities or barangays, please contact an administrator.', 'Ang page na ito ay maaring basahin lamang. Para sa mga pagbabago sa mga Munisipalidad o mga Barangay, Mangyaring i-contact ang administartor.')}}
                    </div>
                </div>
            </div>

            <div class="row" id="municipality">
                <div class="col-12">
                    <div class="table-responsive municipality-table-scroll">
                        <table class="table table-striped w-100 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">{{ T::translate('Municipality', 'Munisipalidad')}}</th>
                                    <th scope="col">{{ T::translate('Barangay', 'Baranagy')}}</th>
                                    <th scope="col">{{ T::translate('Beneficiaries', 'Benepisyaryo')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangays as $barangay)
                                    <tr class="municipality-row" data-municipality="{{ $barangay->municipality->municipality_id }}">
                                        <td>{{ $barangay->municipality->municipality_name }}</td>
                                        <td>{{ $barangay->barangay_name }}</td>
                                        <td>{{ $barangay->beneficiaries_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">{{ T::translate('No barangays found', 'Walang barangay ang nakita')}}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
    // Function to perform the search
    function performSearch() {
        const searchText = document.getElementById('searchBar').value.toLowerCase();
        const rows = document.querySelectorAll('.municipality-row');
        
        rows.forEach(row => {
            const barangayName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const municipalityName = row.querySelector('td:first-child').textContent.toLowerCase();
            
            if (barangayName.includes(searchText) || municipalityName.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Search button click event
    document.getElementById('searchButton').addEventListener('click', function() {
        performSearch();
    });
    
    // Allow search on Enter key
    document.getElementById('searchBar').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            performSearch();
        }
    });
    
    // Filter functionality (unchanged)
    document.getElementById('filterDropdown').addEventListener('change', function() {
        const municipalityId = this.value;
        const rows = document.querySelectorAll('.municipality-row');
        
        if (!municipalityId) {
            // Show all rows if "All Municipalities" is selected
            rows.forEach(row => {
                row.style.display = '';
            });
            return;
        }
        
        rows.forEach(row => {
            if (row.dataset.municipality === municipalityId) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>