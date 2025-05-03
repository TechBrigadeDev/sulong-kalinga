<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfile.css') }}">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    @include('components.userNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h4 class="text-center">USER PROFILE</h4>
                </div>
            </div>
            
            <div class="row" id="viewProfile">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="breadcrumb-container">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" data-section="personal"><a href="javascript:void(0)">Personal</a></li>
                                    <li class="breadcrumb-item" data-section="contact"><a href="javascript:void(0)">Contact</a></li>
                                    <li class="breadcrumb-item" data-section="settings"><a href="javascript:void(0)">Settings</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    
                    <!-- Profile Sections -->
                    <div class="profile-sections">
                        <!-- Personal Information Section -->
                        <div class="profile-section active" id="personal-section">
                            <div class="profile-header">
                                <h5>Personal Information</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="{{ asset('images/defaultProfile.png') }}" alt="Profile Photo" class="profile-photo">
                                    <h5>John Doe</h5>
                                    <p class="text-muted">Member since: Jan 2020</p>
                                </div>
                                <div class="col-md-8">
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Full Name:</div>
                                        <div class="col-md-9">John Michael Doe</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Date of Birth:</div>
                                        <div class="col-md-9">January 15, 1985</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Gender:</div>
                                        <div class="col-md-9">Male</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Nationality:</div>
                                        <div class="col-md-9">American</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Marital Status:</div>
                                        <div class="col-md-9">Married</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information Section -->
                        <div class="profile-section" id="contact-section">
                            <div class="profile-header">
                                <h5>Contact Information</h5>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Email:</div>
                                <div class="col-md-9">john.doe@example.com</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Mobile Phone:</div>
                                <div class="col-md-9">(555) 123-4567</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Landline:</div>
                                <div class="col-md-9">(555) 987-6543</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Address:</div>
                                <div class="col-md-9">
                                    123 Main Street<br>
                                    Apt 4B<br>
                                    Springfield, IL 62704<br>
                                    United States
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Settings Section -->
                        <div class="profile-section" id="settings-section">
                            <div class="profile-header">
                                <h5>Account Settings</h5>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Email:</div>
                                <div class="col-md-9">example@gmail.com</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Password:</div>
                                <div class="col-md-9">
                                    <span class="text-muted">********</span>
                                </div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Account Status:</div>
                                <div class="col-md-9"><span class="badge bg-success">Active</span></div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Last Login:</div>
                                <div class="col-md-9">Today at 09:42 AM</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Language:</div>
                                <div class="col-md-9">English (US)</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-12 text-end">
                                    <button class="btn btn-primary" id="updateEmailBtn">Update Email</button>
                                    <button class="btn btn-primary" id="updatePasswordBtn">Update Password</button>
                                </div>
                            </div>
                            <!-- Hidden Update Email Form -->
                            <div class="row mt-3" id="updateEmailForm" style="display: none;">
                                <div class="col-md-12">
                                    <form action="" method="POST">
                                        <!-- @csrf
                                        @method('PUT') -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">New Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter new email" required>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success">Save Email</button>
                                            <button type="button" class="btn btn-secondary" id="cancelEmailUpdateBtn">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Hidden Update Password Form -->
                            <div class="row mt-3" id="updatePasswordForm" style="display: none;">
                                <div class="col-md-12">
                                    <form action="" method="POST">
                                        <!-- @csrf
                                        @method('PUT') -->
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success">Save Password</button>
                                            <button type="button" class="btn btn-secondary" id="cancelPasswordUpdateBtn">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script>
        // Show the Update Email Form
        document.getElementById('updateEmailBtn').addEventListener('click', function () {
            document.getElementById('updateEmailForm').style.display = 'block';
            this.style.display = 'none'; // Hide the button
            document.getElementById('updatePasswordBtn').style.display = 'none'; // Hide the other button
        });

        // Cancel the Update Email Form
        document.getElementById('cancelEmailUpdateBtn').addEventListener('click', function () {
            document.getElementById('updateEmailForm').style.display = 'none';
            document.getElementById('updateEmailBtn').style.display = 'inline-block'; // Show the button
            document.getElementById('updatePasswordBtn').style.display = 'inline-block'; // Show the other button
        });

        // Show the Update Password Form
        document.getElementById('updatePasswordBtn').addEventListener('click', function () {
            document.getElementById('updatePasswordForm').style.display = 'block';
            this.style.display = 'none'; // Hide the button
            document.getElementById('updateEmailBtn').style.display = 'none'; // Hide the other button
        });

        // Cancel the Update Password Form
        document.getElementById('cancelPasswordUpdateBtn').addEventListener('click', function () {
            document.getElementById('updatePasswordForm').style.display = 'none';
            document.getElementById('updatePasswordBtn').style.display = 'inline-block'; // Show the button
            document.getElementById('updateEmailBtn').style.display = 'inline-block'; // Show the other button
        });
    </script>
    <script>
        // Profile section navigation
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.profile-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show the selected section
            document.getElementById(sectionId + '-section').classList.add('active');
            
            // Update breadcrumb active state
            document.querySelectorAll('.breadcrumb-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.section === sectionId) {
                    item.classList.add('active');
                    
                    // Scroll the active breadcrumb item into view
                    setTimeout(() => {
                        const breadcrumbContainer = document.querySelector('.breadcrumb-container');
                        const activeItem = item;
                        
                        // Calculate scroll position to center the active item
                        const containerWidth = breadcrumbContainer.offsetWidth;
                        const itemLeft = activeItem.offsetLeft;
                        const itemWidth = activeItem.offsetWidth;
                        const scrollPosition = itemLeft - (containerWidth / 2) + (itemWidth / 2);
                        
                        // Smooth scroll to the position
                        breadcrumbContainer.scrollTo({
                            left: Math.max(0, scrollPosition),
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            });
            
            // Scroll to top of content
            window.scrollTo(0, 0);
        }
        
        // Breadcrumb navigation
        document.querySelectorAll('.breadcrumb-item').forEach(item => {
            item.addEventListener('click', function() {
                showSection(this.dataset.section);
            });
        });
    </script>

</body>
</html>