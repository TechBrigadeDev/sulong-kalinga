<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

<div class="sidebar">
  <div class="logo-details" id="logoToggle">
    <i class="bi bi-list"></i>
    <span class="logo_name">Menu</span>
  </div>
  <ul class="nav-links">
    <li  class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
      <a href="/admin/dashboard">
        <i class="bi bi-grid"></i>
        <span class="link_name">Dashboard</span>
      </a>
      <ul class="sub-menu blank ">
        <li><a class="link_name" href="/admin/dashboard">Dashboard</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::is('admin/beneficiaries*') || Request::is('admin/families*') || Request::is('admin/care-workers*') || Request::is('admin/care-managers*') || Request::is('admin/administrators*') ? 'active' : '' }}">
      <div class="icon-link">
        <a >
          <i class="bi bi-people"></i>
          <span class="link_name">User Management</span>
        </a>
        <i class="bi bi-chevron-down arrow dropdown-arrow"></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">User Management</a></li>
        <li><a href="{{ route('admin.beneficiaries.index') }}" class="{{ Request::routeIs('admin.beneficiaries.*') ? 'active' : '' }}">Beneficiary Profile</a></li>
        <li><a href="{{ route('admin.families.index') }}" class="{{ Request::routeIs('admin.families.*') ? 'active' : '' }}">Family Profile</a></li>
        <li><a href="{{ route('admin.careworkers.index') }}" class="{{ Request::routeIs('admin.careworkers.*') ? 'active' : '' }}">Care Worker Profile</a></li>
        <li><a href="{{ route('admin.caremanagers.index') }}" class="{{ Request::routeIs('admin.caremanagers.*') ? 'active' : '' }}">Care Manager Profile</a></li>
        <li><a href="{{ route('admin.administrators.index') }}" class="{{ Request::routeIs('admin.administrators.*') ? 'active' : '' }}">Admin Profile</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.careworker.performance.*') || Request::routeIs('admin.health.monitoring.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a >
          <i class="bi bi-file-earmark-text"></i>
          <span class="link_name">Report Management</span>
        </a>
          <i class='bi bi-chevron-down arrow dropdown-arrow'></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Report Management</a></li>
        <li><a href="{{ route('admin.careworker.performance.index') }}" class="{{ Request::routeIs('admin.careworker.performance.*') ? 'active' : '' }}">Care Worker Performance</a></li>
        <li><a href="{{ route('admin.health.monitoring.index') }}" class="{{ Request::routeIs('admin.health.monitoring.*') ? 'active' : '' }}">Health Monitoring</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.reports') || Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a>
          <i class="bi bi-clipboard-data"></i>
          <span class="link_name">Care Records</span>
        </a>
          <i class='bi bi-chevron-down arrow dropdown-arrow'></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Care Records</a></li>
        <li><a href="{{ route('admin.reports') }}" class="{{ Request::routeIs('admin.reports') ? 'active' : '' }}">Records Management</a></li>
        <li><a href="{{ route('admin.weeklycareplans.create') }}" class="{{ Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">Weekly Care Plan</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.careworker.appointments.*') || Request::routeIs('admin.internal.appointments.*') || Request::routeIs('admin.medication.schedule.*')? 'active' : '' }}">
      <div class="icon-link">
        <a>
          <i class="bi bi-calendar-week"></i>
          <span class="link_name">Schedules & Appointments</span>
        </a>
          <i class='bi bi-chevron-down arrow dropdown-arrow'></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Schedules & Appointments</a></li>
        <li><a href="{{ route('admin.careworker.appointments.index') }}" class="{{ Request::routeIs('admin.careworker.appointments.*') ? 'active' : '' }}">Care Worker Appointment</a></li>
        <li><a href="{{ route('admin.internal.appointments.index') }}" class="{{ Request::routeIs('admin.internal.appointments.*') ? 'active' : '' }}">Internal Appointment</a></li>
        <li><a href="{{ route('admin.medication.schedule.index') }}" class="{{ Request::routeIs('admin.medication.schedule.*') ? 'active' : '' }}">Medication Schedule</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.beneficiary.map.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a>
          <i class="bi bi-geo-alt"></i>
          <span class="link_name">Location Tracking</span>
        </a>
          <i class='bi bi-chevron-down arrow dropdown-arrow'></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Location Tracking</a></li>
        <li><a href="{{ route('admin.beneficiary.map.index') }}" class="{{ Request::routeIs('admin.beneficiary.map.*') ? 'active' : '' }}">Beneficiary Map</a></li>
        <li><a href="#" class="">Care Worker Tracking</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.locations.*') ? 'active' : '' }}">
      <a href="{{ route('admin.locations.index') }}">
        <i class="bi bi-map"></i>
        <span class="link_name">Municipality Management</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.locations.index') }}">Municipality Management</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('admin.expense.*') ? 'active' : '' }}">
      <a href="{{ route('admin.expense.index') }}">
        <i class="bi bi-cash-stack"></i>
        <span class="link_name">Expenses Tracker</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="#">Expenses Tracker</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.emergency.request.*') ? 'active' : '' }}">
      <a href="{{ route('admin.emergency.request.index') }}" class="">
        <i class="bi bi-exclamation-triangle"></i>
        <span class="link_name">Emergency & Request</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.emergency.request.index') }}">Emergency & Request</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.donor.acknowledgement.*') || Request::routeIs('admin.highlights.events.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a>
          <i class="bi bi-globe"></i>
          <span class="link_name">Site Management</span>
        </a>
          <i class='bi bi-chevron-down arrow dropdown-arrow'></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Site Management</a></li>
        <li><a href="{{ route('admin.donor.acknowledgement.index') }}" class="{{ Request::routeIs('admin.donor.acknowledgement.*') ? 'active' : '' }}">Donor Acknowledgement</a></li>
        <li><a href="{{ route('admin.highlights.events.index') }}" class="{{ Request::routeIs('admin.highlights.events.*') ? 'active' : '' }}">Events & Highlights</a></li>
      </ul>
    </li>
  </ul>
</div>

<script>
  function handleSidebarState() {
    const sidebar = document.querySelector(".sidebar");
    const contentSection = document.getElementById("content-section");

    if (window.innerWidth <= 768) {
      sidebar.classList.add("close");
    } else {
      sidebar.classList.remove("close");
    }
  }
  window.addEventListener("load", handleSidebarState);
  window.addEventListener("resize", handleSidebarState);

  function toggleDropdown(element) {
    const parent = element.closest('li');
    parent.classList.toggle('showMenu');
  }
  
  // Add click event to all dropdown arrows
  document.querySelectorAll('.dropdown-arrow').forEach(arrow => {
    arrow.addEventListener('click', function(e) {
      e.stopPropagation();
      const parent = this.closest('li');
      parent.classList.toggle('showMenu');
    });
  });
  
  // Add click event to all link names in dropdown items
  document.querySelectorAll('.icon-link .link_name').forEach(link => {
    link.addEventListener('click', function(e) {
      e.stopPropagation();
      const parent = this.closest('li');
      parent.classList.toggle('showMenu');
    });
  });
</script>