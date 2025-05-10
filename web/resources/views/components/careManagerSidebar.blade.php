<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

<div class="sidebar">
  <div class="logo-details" id="logoToggle">
    <i class="bi bi-list"></i>
    <span class="logo_name">Menu</span>
  </div>
  <ul class="nav-links">
    <li class="{{ Request::routeIs('care-manager.dashboard') ? 'active' : '' }}">
      <a href="{{ route('care-manager.dashboard') }}">
        <i class="bi bi-grid"></i>
        <span class="link_name">Dashboard</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('care-manager.dashboard') }}">Dashboard</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('care-manager.beneficiaries.*') || Request::routeIs('care-manager.families.*') || Request::routeIs('care-manager.careworkers.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a>
          <i class="bi bi-people"></i>
          <span class="link_name">User Management</span>
        </a>
        <i class="bi bi-chevron-down arrow dropdown-arrow"></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">User Management</a></li>
        <li><a href="{{ route('care-manager.beneficiaries.index') }}" class="{{ Request::routeIs('care-manager.beneficiaries.*') ? 'active' : '' }}">Beneficiary Profile</a></li>
        <li><a href="{{ route('care-manager.families.index') }}" class="{{ Request::routeIs('care-manager.families.*') ? 'active' : '' }}">Family Profile</a></li>
        <li><a href="{{ route('care-manager.careworkers.index') }}" class="{{ Request::routeIs('care-manager.careworkers.*') ? 'active' : '' }}">Care Worker Profile</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('care-manager.careworker.performance.*') || Request::routeIs('care-manager.health.monitoring.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a>
          <i class="bi bi-file-earmark-text"></i>
          <span class="link_name">Report Management</span>
        </a>
        <i class="bi bi-chevron-down arrow dropdown-arrow"></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Report Management</a></li>
        <li><a href="{{ route('care-manager.careworker.performance.index') }}" class="{{ Request::routeIs('care-manager.careworker.performance.*') ? 'active' : '' }}">Care Worker Performance</a></li>
        <li><a href="{{ route('care-manager.health.monitoring.index') }}" class="{{ Request::routeIs('care-manager.health.monitoring.*') ? 'active' : '' }}">Health Monitoring</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('care-manager.reports') || Request::routeIs('care-manager.weeklycareplans.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a>
          <i class="bi bi-clipboard-data"></i>
          <span class="link_name">Care Records</span>
        </a>
        <i class="bi bi-chevron-down arrow dropdown-arrow"></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Care Records</a></li>
        <li><a href="{{ route('care-manager.reports') }}" class="{{ Request::routeIs('care-manager.reports') ? 'active' : '' }}">Records Management</a></li>
        <li><a href="{{ route('care-manager.weeklycareplans.create') }}" class="{{ Request::routeIs('care-manager.weeklycareplans.*') ? 'active' : '' }}">Weekly Care Plan</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('care-manager.careworker.appointments.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a>
          <i class="bi bi-calendar-week"></i>
          <span class="link_name">Schedules & Appointments</span>
        </a>
        <i class="bi bi-chevron-down arrow dropdown-arrow"></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Schedules & Appointments</a></li>
        <li><a href="{{ route('care-manager.careworker.appointments.index') }}" class="{{ Request::routeIs('care-manager.careworker.appointments.*') ? 'active' : '' }}">Care Worker Appointment</a></li>
        <li><a href="#" class="">Internal Appointment</a></li>
        <li><a href="#" class="">Medical Schedule</a></li>
      </ul>
    </li>
    
    <li>
      <div class="icon-link">
        <a>
          <i class="bi bi-geo-alt"></i>
          <span class="link_name">Location Tracking</span>
        </a>
        <i class="bi bi-chevron-down arrow dropdown-arrow"></i>
      </div>
      <ul class="sub-menu m-auto">
        <li><a class="link_name">Location Tracking</a></li>
        <li><a href="#" class="">Beneficiary Map</a></li>
        <li><a href="#" class="">Care Worker Tracking</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('care-manager.municipalities.*') ? 'active' : '' }}">
      <a href="{{ route('care-manager.municipalities.index') }}">
        <i class="bi bi-map"></i>
        <span class="link_name">Municipality Management</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('care-manager.municipalities.index') }}">Municipality Management</a></li>
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