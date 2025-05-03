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
    <link rel="stylesheet" href="{{ asset('css/viewFamilyDetails.css') }}">
</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    @include('components.modals.deleteFamilyMember')

    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Original Back Button -->
                <a href="{{ route('care-manager.families.index') }}" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                </a>

                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW FAMILY PROFILE DETAILS</div>

                <!-- Edit and Delete Buttons -->
                <div>
                    <!-- Hidden Back Button 
                    <a href="familyProfile" class="btn btn-secondary hidden-back-btn">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>-->
                    <!-- Edit Button with Routing -->
                    <a href="{{ route('care-manager.families.edit', $family_member->family_member_id) }}" class="btn btn-primary">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                    <!-- Delete button to call the delete function -->
                    <button type="button" class="btn btn-danger" onclick="openDeleteFamilyMemberModal('{{ $family_member->family_member_id }}', '{{ $family_member->first_name }} {{ $family_member->last_name }}')">
                        <i class="bx bxs-trash"></i> Delete
                    </button>
                </div>
            </div>
            <div class="row justify-content-center" id="profileDetails">
                <div class="row mb-3 mt-3 justify-content-center">
                    <div class="col-lg-8 col-md-12 col-sm-12" id="profilePic">
                        <div class="row justify-content-center align-items-center text-center text-md-start">
                            <!-- Profile Picture Column -->
                            <div class="col-lg-3 col-md-4 col-sm-12 mb-3 mb-md-0">
                                <img src="{{ $family_member->photo ? asset('storage/' . $family_member->photo) : asset('images/defaultProfile.png') }}" 
                                    alt="Profile Picture" 
                                    class="img-fluid rounded-circle mx-auto d-block d-md-inline" 
                                    style="width: 150px; height: 150px; border: 1px solid #ced4da;">
                            </div>
                            <!-- Name and Details Column -->
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <div class="d-flex flex-column align-items-start">
                                    <!-- Complete Name -->
                                    <h4 class="mb-2 mt-1">{{ $family_member->first_name }} {{ $family_member->last_name }}</h4>
                                    <!-- Dropdown for Status 
                                    <div class="form-group">
                                        <select class="form-select text-center" name="status" id="statusSelect{{ $family_member->family_member_id }}" data-id="{{ $family_member->family_member_id }}" onchange="openFamilyStatusChangeModal(this, 'Family')">
                                            <option value="Approved" {{ $family_member->status == 'Approved' ? 'selected' : '' }} >Access Approved</option>
                                            <option value="Denied" {{ $family_member->status == 'Denied' ? 'selected' : '' }} >Access Denied</option>
                                        </select>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-1">
                    <!-- Personal Details Column -->
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h5 class="text-center">Personal Details</h5>
                        <table class="table table-striped personal-details">                            
                            <tbody>
                                <tr>
                                    <td style="width:30%;"><strong>Gender:</strong></td>
                                    <td>{{ $family_member->gender }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Birthday:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($family_member->birthday)->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Mobile Number:</strong></td>
                                    <td>{{ $family_member->mobile ?? 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Landline Number:</strong></td>
                                    <td>{{ $family_member->landline ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Email:</strong></td>
                                    <td>{{ $family_member->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Current Address:</strong></td>
                                    <td>{{ $family_member->street_address }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Relation to Beneficiary:</strong></td>
                                    <td>{{ $family_member->relation_to_beneficiary }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>    
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center">
                        <h5 class="text-center">Related Beneficiary</h5>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center flex-wrap">
                        <div class="card text-center p-1 m-1" style="max-width: 160px;">
                            <div class="d-flex justify-content-center align-items-center" style="height: 100px;">
                                <img src=" {{ $family_member->beneficiary->photo ? asset('storage/' . $family_member->beneficiary->photo) : asset('images/defaultProfile.png') }}" class="img-fluid" alt="..." style="max-width: 100px; max-height: 100px;">
                            </div>
                            <div class="card-body p-1">
                                <p class="card-text" style="font-size:14px;">{{ $family_member->beneficiary->first_name }} {{ $family_member->beneficiary->last_name }}</p>
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