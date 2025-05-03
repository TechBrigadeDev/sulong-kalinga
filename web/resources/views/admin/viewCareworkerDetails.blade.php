<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/viewProfileDetails.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeCareworker')
    @include('components.modals.deleteCareworker')

    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Original Back Button -->
                <a href="{{ route('admin.careworkers.index') }}" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW CARE WORKER PROFILE DETAILS</div>
                <div>
                    <!-- Hidden Back Button -->
                    <a href="beneficiaryProfile" class="btn btn-secondary hidden-back-btn">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                    <!-- Edit Button with Routing -->
                    <a href="{{ route('admin.careworkers.edit', $careworker->id) }}" class="btn btn-primary">
                        <i class="bx bxs-edit"></i> Edit
                    </a>
                    <button class="btn btn-danger" onclick="openDeleteCareworkerModal('{{ $careworker->id }}', '{{ $careworker->first_name }} {{ $careworker->last_name }}')">
                        <i class="bx bxs-trash"></i> Delete
                    </button>
                </div>
            </div>
            <div class="row justify-content-center" id="profileDetails">
                <div class="row justify-content-center mb-3">
                    <div class="col-lg-8 col-md-12 col-sm-12 mt-3 mb-3" id="profilePic">
                        <div class="row justify-content-center align-items-center text-center text-md-start">
                            <!-- Profile Picture Column -->
                            <div class="col-lg-3 col-md-4 col-sm-12 mb-3 mb-md-0">
                            <img src="{{ $careworker->photo ? asset('storage/' . $careworker->photo) : asset('images/defaultProfile.png') }}"
                                    alt="Profile Picture" 
                                    class="img-fluid rounded-circle mx-auto d-block d-md-inline" 
                                    style="width: 150px; height: 150px; border: 1px solid #ced4da;">
                                    
                            </div>
                            <!-- Name and Details Column -->
                            <div class="col-lg-9 col-md-8 col-sm-12">
                                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                                    <!-- Complete Name -->
                                    <h4 class="me-md-3 mb-2 mb-md-0 mt-2">{{ $careworker->first_name }} {{ $careworker->last_name }}</h4>
                                    <!-- Dropdown for Status -->
                                    <div class="form-group mb-0 ms-md-auto">
                                    <select class="form-select" name="status" id="statusSelect{{ $careworker->id }}" onchange="openStatusChangeCareworkerModal(this, 'Care Worker', {{ $careworker->id }}, '{{ $careworker->status }}')">
                                                <option value="Active" {{ $careworker->status == 'Active' ? 'selected' : '' }}>Active Care Worker</option>
                                                <option value="Inactive" {{ $careworker->status == 'Inactive' ? 'selected' : '' }}>Inactive Care Worker</option>
                                            </select>
                                    </div>
                                </div>
                                <p class="text-muted mt-2 text-center text-md-start">A Care Worker since {{ $careworker->status_start_date->format('F j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Personal Details Column -->
                    <div class="col-lg-8 col-md-12 col-sm-12">
                        <h5 class="text-center">Personal Details</h5>
                        <table class="table table-striped personal-details">                            
                            <tbody>
                                <tr>
                                    <td style="width:30%;"><strong>Educational Background:</strong></td>
                                    <td>{{$careworker->educational_background ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Birthday:</strong></td>
                                    <td>{{$careworker->birthday->format('F j, Y')}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Gender:</strong></td>
                                    <td>{{$careworker->gender ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Civil Status:</strong></td>
                                    <td>{{$careworker->civil_status ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Religion:</strong></td>
                                    <td>{{$careworker->religion ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Nationality:</strong></td>
                                    <td>{{$careworker->nationality ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Assigned Municipality:</strong></td>
                                    <td>{{$careworker->municipality->municipality_name}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Assigned Care Manager:</strong></td>
                                    <td>
                                        @if($careworker->assignedCareManager)
                                            {{ $careworker->assignedCareManager->first_name }} {{ $careworker->assignedCareManager->last_name }}
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Email Address:</strong></td>
                                    <td>{{$careworker->email}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Mobile Number:</strong></td>
                                    <td>{{$careworker->mobile}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Landline Number:</strong></td>
                                    <td>{{$careworker->landline ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Current Address:</strong></td>
                                    <td><p>{{$careworker->address}}</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5 class="text-center">Documents</h5>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%;"><strong>Government Issued ID:</strong></td>
                                            <td style="width: 60%;">
                                                @if($careworker->government_issued_id)
                                                    <a href="{{ asset('storage/' . $careworker->government_issued_id) }}" download>Download</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>                                     
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>Resume / CV:</strong></td>
                                            <td style="width: 60%;">
                                                @if($careworker->cv_resume)
                                                    <a href="{{ asset('storage/' . $careworker->cv_resume) }}" download>Download</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>                                                
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5 class="text-center">Government ID Numbers</h5>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%;"><strong>SSS ID Number:</strong></td>
                                            <td style="width: 60%;">{{$careworker->sss_id_number ?? 'N/A'}}</td>                                 
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>PhilHealth ID Number:</strong></td>
                                            <td style="width: 60%;">{{$careworker->philhealth_id_number ?? 'N/A'}}</td>                                   
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>Pag-Ibig ID Number:</strong></td>
                                            <td style="width: 60%;">{{$careworker->pagibig_id_number ?? 'N/A'}}</td>                                 
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center">
                            <h5 class="text-center">Managed Beneficiary</h5>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center flex-wrap">
                            @forelse ($beneficiaries as $beneficiary)
                                <div class="card text-center p-1 m-1" style="max-width: 160px;">
                                    <div class="d-flex justify-content-center align-items-center" style="height: 100px;">
                                        <img src="{{ $beneficiary->photo ? asset('storage/' . $beneficiary->photo) : asset('images/defaultProfile.png') }}" class="img-fluid" alt="..." style="max-width: 100px; max-height: 100px;">
                                    </div>
                                    <div class="card-body p-1">
                                        <p class="card-text" style="font-size:14px;">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center">No beneficiaries being handled currently.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
   
</body>
</html>