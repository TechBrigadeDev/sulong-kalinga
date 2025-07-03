<link rel="stylesheet" href="{{ asset('css/footer.css') }}">

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-5 mb-lg-0">
                <img src="{{ asset('images/cose-logo.png')}}" alt="COSE Logo" style="max-height: 60px; filter: brightness(0) invert(1);">
                <p class="mb-4">The Coalition of Services for the Elderly, Inc.</p>
                <p>Empowering seniors since 1989 through comprehensive mobile healthcare services.</p>
            </div>
            <div class="col-lg-3 mb-5 mb-lg-0">
                <div class="footer-links">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#">About MHCS</a></li>
                        <li><a href="#">Our Services</a></li>
                        <li><a href="#">Upcoming Events</a></li>
                        <li><a href="#">Volunteer Opportunities</a></li>
                        <li><a href="#">Contact Our Team</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 mb-5 mb-lg-0">
                <div class="footer-links">
                    <h5>Connect With Us</h5>
                    <p><i class="bi bi-envelope me-2"></i> mhcs@cose.org.ph</p>
                    <p><i class="bi bi-telephone me-2"></i> (02) 1234-5678</p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="footer-links">
                    <h5>Subscribe to Newsletter</h5>
                    <form action="#" method="POST" class="newsletter-form">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="privacyCheck" required>
                            <label class="form-check-label small" for="privacyCheck">
                                I agree to the privacy policy
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" style="background-color:#4c9d6f; border-color: #4c9d6f;">
                            Subscribe <i class="bi bi-envelope-arrow-up ms-2"></i>
                        </button>
                    </form>
                    <p class="small mt-2">Get updates on our latest services and events</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="mb-0">&copy; 2025 COSE Mobile Healthcare Service. All rights reserved.</p>
        </div>
    </div>
</footer>