<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/boxicons.min.css') }}">

<div class="sidebar">
  <div class="logo-details" id="logoToggle">
    <i class="bi bi-list"></i>
    <span class="logo_name">Menu</span>
  </div>
  <ul class="nav-links">
    <li>
      <a href="/admin/dashboard" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid"></i>
        <span class="link_name">Dashboard</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="/admin/dashboard" >Dashboard</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.reports') }}" class="{{ Request::routeIs('admin.reports') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i>
        <span class="link_name">Reports Management</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.reports') }}">Reports Management</a></li>
      </ul>
    </li>
    <li>
      <div class="icon-link">
        <a class="{{ Request::is('admin/beneficiaries*') || Request::is('admin/families*') || Request::is('admin/care-workers*') || Request::is('admin/care-managers*') || Request::is('admin/administrators*') ? 'active' : '' }}">
          <i class="bi bi-people"></i>
          <span class="link_name" onclick="toggleDropdown(this)">User Management</span>
          <i class='bi bi-chevron-down arrow' onclick="toggleDropdown(this)"></i>
        </a>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">User Management</a></li>
        <li><a href="{{ route('admin.beneficiaries.index') }}" class="{{ Request::routeIs('admin.beneficiaries.*') ? 'active' : '' }}">Beneficiary Profiles</a></li>
        <li><a href="{{ route('admin.families.index') }}" class="{{ Request::routeIs('admin.families.*') ? 'active' : '' }}">Family or Relative Profiles</a></li>
        <li><a href="{{ route('admin.careworkers.index') }}" class="{{ Request::routeIs('admin.careworkers.*') ? 'active' : '' }}">Care Worker Profiles</a></li>
        <li><a href="{{ route('admin.caremanagers.index') }}" class="{{ Request::routeIs('admin.caremanagers.*') ? 'active' : '' }}">Care Manager Profiles</a></li>
        <li><a href="{{ route('admin.administrators.index') }}" class="{{ Request::routeIs('admin.administrators.*') ? 'active' : '' }}">Administrator Profiles</a></li>
      </ul>
    </li>
    <li>
    <a href="{{ route('admin.careworker.performance.index') }}" class="{{ Request::routeIs('admin.careworker.performance.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart"></i>
        <span class="link_name">Care Worker Performance</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.careworker.performance.index') }}">Care Worker Performance</a></li>
      </ul>
    </li>
    <li>
        <a href="{{ route('admin.health.monitoring.index') }}" class="{{ Request::routeIs('admin.health.monitoring.*') ? 'active' : '' }}">
        <i class="bi bi-heart-pulse"></i>
        <span class="link_name">Health Monitoring</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.health.monitoring.index') }}">Health Monitoring</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.schedules.appointments.index') }}" class="{{ Request::routeIs('admin.schedules.appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar-week"></i>
        <span class="link_name">Schedules / Appointments</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.schedules.appointments.index') }}">Schedules / Appointments</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.locations.index') }}" class="{{ Request::routeIs('admin.locations.*') ? 'active' : '' }}">
        <i class="bi bi-map"></i>
        <span class="link_name">Municipality</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.locations.index') }}">Municipality</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.beneficiary.map.index') }}" class="{{ Request::routeIs('admin.beneficiary.map.*') ? 'active' : '' }}">
        <i class="bi bi-pin-map"></i>
        <span class="link_name">Beneficiary Map</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.beneficiary.map.index') }}">Beneficiary Map</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.weeklycareplans.create') }}" class="{{ Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard-check"></i>
        <span class="link_name">Weekly Care Plan</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.weeklycareplans.create') }}">Weekly CarePlan</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.donor.acknowledgement.index') }}" class="{{ Request::routeIs('admin.donor.acknowledgement.*') ? 'active' : '' }}">
        <i class="bi bi-award"></i>
        <span class="link_name">Donor Acknowledgement</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.donor.acknowledgement.index') }}">Donor Acknowledgement </a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.highlights.events.index') }}" class="{{ Request::routeIs('admin.highlights.events.*') ? 'active' : '' }}">
        <i class="bi bi-megaphone"></i>
        <span class="link_name">Highlights & Events</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.highlights.events.index') }}">Highlights & Events</a></li>
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
</script>