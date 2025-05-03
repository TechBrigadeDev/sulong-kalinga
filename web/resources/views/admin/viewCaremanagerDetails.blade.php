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
    @include('components.modals.statusChangeCaremanager')
    @include('components.modals.deleteCaremanager')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Original Back Button -->
                <a href="{{ route('admin.caremanagers.index') }}" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW CARE MANAGER PROFILE DETAILS</div>
                <div>
                    <!-- Hidden Back Button -->
                    <a href="viewCareworkerProfile" class="btn btn-secondary hidden-back-btn">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                    <!-- Edit Button with Routing - Only visible to administrators -->
                    @if(Auth::user()->role_id == 1)
                        <!-- Edit Button -->
                        <a href="{{ route('admin.caremanagers.edit', $caremanager->id) }}" class="btn btn-primary">
                            <i class="bx bxs-edit"></i> Edit
                        </a>

                        <!-- Delete Button -->
                        <button type="button" class="btn btn-danger" onclick="openDeleteCaremanagerModal('{{ $caremanager->id }}', '{{ $caremanager->first_name }} {{ $caremanager->last_name }}')">
                            <i class="bx bxs-trash"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
            <div class="row justify-content-center" id="profileDetails">
                <div class="row justify-content-center mb-3">
                    <div class="col-lg-8 col-md-12 col-sm-12 mt-3 mb-3" id="profilePic">
                        <div class="row justify-content-center align-items-center text-center text-md-start">
                            <!-- Profile Picture Column -->
                            <div class="col-lg-3 col-md-4 col-sm-12 mb-3 mb-md-0">
                                <img src="{{ $caremanager->photo ? asset('storage/' . $caremanager->photo) : asset('images/defaultProfile.png') }}" 
                                    alt="Profile Picture" 
                                    class="img-fluid rounded-circle mx-auto d-block d-md-inline" 
                                    style="width: 150px; height: 150px; border: 1px solid #ced4da;">
                            </div>
                            <!-- Name and Details Column -->
                            <div class="col-lg-9 col-md-8 col-sm-12">
                                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                                    <!-- Complete Name -->
                                    <h4 class="me-md-3 mb-2 mb-md-0 mt-2">{{ $caremanager->first_name }} {{ $caremanager->last_name }}</h4>
                                    <!-- Dropdown for Status -->
                                    <div class="form-group mb-0 ms-md-auto">
                                        <select class="form-select" name="status" id="statusSelect{{ $caremanager->id }}" onchange="openStatusChangeCaremanagerModal(this, 'Care Manager', {{ $caremanager->id }}, '{{ $caremanager->status }}')">
                                                <option value="Active" {{ $caremanager->status == 'Active' ? 'selected' : '' }}>Active Care Manager</option>
                                                <option value="Inactive" {{ $caremanager->status == 'Inactive' ? 'selected' : '' }}>Inactive Care Manager</option>
                                            </select>
                                    </div>
                                </div>
                                <p class="text-muted mt-2 text-center text-md-start">A Care Manager since {{ $caremanager->status_start_date->format('F j, Y') }}</p>
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
                                    <td>{{$caremanager->educational_background ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Birthday:</strong></td>
                                    <td>{{$caremanager->birthday->format('F j, Y')}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Gender:</strong></td>
                                    <td>{{$caremanager->gender ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Civil Status:</strong></td>
                                    <td>{{$caremanager->civil_status ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Religion:</strong></td>
                                    <td>{{$caremanager->religion ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Nationality:</strong></td>
                                    <td>{{$caremanager->nationality ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Assigned Municipality:</strong></td>
                                    <td>{{$caremanager->municipality->municipality_name}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Email Address:</strong></td>
                                    <td>{{$caremanager->email}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Mobile Number:</strong></td>
                                    <td>{{$caremanager->mobile}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Landline Number:</strong></td>
                                    <td>{{$caremanager->landline ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Current Address:</strong></td>
                                    <td><p>{{$caremanager->address}}</p></td>
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
                                                @if($caremanager->government_issued_id)
                                                    <a href="{{ asset('storage/' . $caremanager->government_issued_id) }}" download>Download</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>                                    
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>Resume / CV:</strong></td>
                                            <td style="width: 60%;">
                                                @if($caremanager->cv_resume)
                                                    <a href="{{ asset('storage/' . $caremanager->cv_resume) }}" download>Download</a>
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
                                            <td style="width: 60%;">{{$caremanager->sss_id_number ?? 'N/A'}}</td>                                
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>PhilHealth ID Number:</strong></td>
                                            <td style="width: 60%;">{{$caremanager->philhealth_id_number ?? 'N/A'}}</td>                                
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>Pag-Ibig ID Number:</strong></td>
                                            <td style="width: 60%;">{{$caremanager->pagibig_id_number ?? 'N/A'}}</td>                             
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

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
   
</body>
</html>