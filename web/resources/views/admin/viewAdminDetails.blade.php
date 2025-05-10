<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfileDetails.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeAdmin')
    @include('components.modals.deleteAdmin')
    @php use App\Helpers\StringHelper;
    use Illuminate\Support\Facades\Auth;
    @endphp
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Original Back Button -->
                <a href="{{ route('admin.administrators.index') }}" class="btn btn-secondary original-back-btn">
                    <i class="bi bi-arrow-bar-left"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW ADMINISTRATOR PROFILE DETAILS</div>
                <div>
                    <!-- Hidden Back Button -->
                    <a href="viewCareworkerProfile" class="btn btn-secondary hidden-back-btn">
                        <i class="bi bi-arrow-bar-left"></i> Back
                    </a>

                    <!-- Only show Edit and Delete buttons to Executive Director -->
                    @if(Auth::user()->organization_role_id == 1)
                        <!-- Edit Button with Routing -->
                        <a href="{{ route('admin.administrators.edit', $administrator->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>

                        <!-- Delete Button -->
                        <button class="btn btn-danger" onclick="openDeleteAdminModal('{{ $administrator->id }}', '{{ $administrator->first_name }} {{ $administrator->last_name }}')">
                            <i class="bi bi-trash-fill"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
            <div class="row justify-content-center" id="profileDetails">
                <div class="row justify-content-center mt-3 mb-3">
                    <div class="col-lg-8 col-md-12 col-sm-12 mb-3" id="profilePic">
                        <div class="row justify-content-center align-items-center text-center text-md-start">
                            <!-- Profile Picture Column -->
                            <div class="col-lg-3 col-md-4 col-sm-12 mb-3 mb-md-0">
                                <img src="{{ $administrator->photo ? asset('storage/' . $administrator->photo) : asset('images/defaultProfile.png') }}" 
                                    alt="Profile Picture" 
                                    class="img-fluid rounded-circle mx-auto d-block d-md-inline" 
                                    style="width: 150px; height: 150px; border: 1px solid #ced4da;">
                            </div>
                            <!-- Name and Details Column -->
                            <div class="col-lg-9 col-md-8 col-sm-12">
                                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                                    <!-- Complete Name -->
                                    <h4 class="me-md-3 mb-2 mb-md-0 mt-2">{{ $administrator->first_name }} {{ $administrator->last_name }}</h4>
                                    <!-- Dropdown for Status -->
                                    <div class="form-group mb-0 ms-md-auto" {{ isset($administrator->organizationRole) && $administrator->organizationRole->role_name == 'executive_director' ? 'data-bs-toggle="tooltip" data-bs-placement="top" title="Executive Director status cannot be changed."' : '' }}>
                                        @if(isset($administrator->organizationRole) && $administrator->organizationRole->role_name == 'executive_director')
                                            <!-- For executive directors, use a static badge instead of a dropdown -->
                                            <span style="width: 200px"  class="badge bg-primary">Active Executive Director</span>
                                            <!-- Hidden select just to maintain data consistency if needed -->
                                            <select class="form-select d-none" name="status" id="statusSelect{{ $administrator->id }}" disabled>
                                                <option value="Active" selected>Active</option>
                                            </select>
                                        @else
                                            <!-- For non-executive directors, use the normal dropdown -->
                                            <select class="form-select d-inline-block w-auto" name="status" id="statusSelect{{ $administrator->id }}" 
                                                    onchange="openStatusChangeAdminModal(this, 'Administrator', {{ $administrator->id }}, '{{ $administrator->status ?? 'Active' }}')">
                                                <option value="Active" {{ ($administrator->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active Administrator</option>
                                                <option value="Inactive" {{ ($administrator->status ?? 'Active') == 'Inactive' ? 'selected' : '' }}>Inactive Administrator</option>
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-muted mt-2 text-center text-md-start">A {{ StringHelper::formatArea($administrator->organizationRole->area ?? 'N/A')}} since {{ $administrator->status_start_date->format('F j, Y')  }}</p>
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
                                    <td>{{$administrator->educational_background ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Birthday:</strong></td>
                                    <td>{{$administrator->birthday->format('F j, Y')}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Gender:</strong></td>
                                    <td>{{$administrator->gender ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Civil Status:</strong></td>
                                    <td>{{$administrator->civil_status ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Religion:</strong></td>
                                    <td>{{$administrator->religion ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Nationality:</strong></td>
                                    <td>{{$administrator->nationality ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Email Address:</strong></td>
                                    <td>{{$administrator->email}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Mobile Number:</strong></td>
                                    <td>{{$administrator->mobile}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Landline Number:</strong></td>
                                    <td>{{$administrator->landline ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Current Address:</strong></td>
                                    <td><p>{{$administrator->address}}</p></td>
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
                                                @if($administrator->government_issued_id)
                                                    <a href="{{ asset('storage/' . $administrator->government_issued_id) }}" download>Download</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>                                  
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>Resume / CV:</strong></td>
                                            <td style="width: 60%;">
                                                @if($administrator->cv_resume)
                                                    <a href="{{ asset('storage/' . $administrator->cv_resume) }}" download>Download</a>
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
                                            <td style="width: 60%;">{{$administrator->sss_id_number ?? 'N/A'}}</td>                                  
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>PhilHealth ID Number:</strong></td>
                                            <td style="width: 60%;">{{$administrator->philhealth_id_number ?? 'N/A'}}</td>                                 
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>Pag-Ibig ID Number:</strong></td>
                                            <td style="width: 60%;">{{$administrator->pagibig_id_number ?? 'N/A'}}</td>                                  
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