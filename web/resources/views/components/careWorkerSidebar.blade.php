<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/boxicons.min.css') }}">

<div class="sidebar">
  <div class="logo-details" id="logoToggle">
    <i class='bx bx-menu'></i>
    <span class="logo_name">Menu</span>
  </div>
  <ul class="nav-links">
    <li>
      <a href="{{ route('care-worker.dashboard') }}" class="{{ Request::routeIs('care-worker.dashboard') ? 'active' : '' }}">
        <i class='bx bx-grid-alt'></i>
        <span class="link_name">Dashboard</span>
      </a>
      <ul class="sub-menu blank">
      <li><a class="link_name" href="{{ route('care-worker.dashboard') }}" >Dashboard</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('care-worker.reports') }}" class="{{ Request::routeIs('admin.reports') ? 'active' : '' }}">
        <i class='bx bx-file'></i>
        <span class="link_name">Reports Management</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('care-worker.reports') }}">Reports Management</a></li>
      </ul>
    </li>
    <li>
      <div class="icon-link">
        <a class="{{ Request::is('care-worker/beneficiaries*') || Request::is('care-worker/family-members*') ? 'active' : '' }}">
          <i class='bx bxs-user-account'></i>
          <span class="link_name" onclick="toggleDropdown(this)">User Management</span>
          <i class='bx bxs-chevron-down arrow' onclick="toggleDropdown(this)"></i>
        </a>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">User Management</a></li>
        <li><a href="{{ route('care-worker.beneficiaries.index') }}" class="{{ Request::routeIs('care-worker.showBeneficiary') || Request::routeIs('care-worker.addBeneficiary') ? 'active' : '' }}">Beneficiary Profiles</a></li>
        <li><a href="{{ route('care-worker.families.index') }}" class="{{ Request::routeIs('care-worker.showFamilyMember') || Request::routeIs('care-worker.addFamilyMember') ? 'active' : '' }}">Family or Relative Profiles</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('care-worker.weeklycareplans.create') }}" class="{{ Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">
        <i class='bx bx-task'></i>
        <span class="link_name">Weekly Care Plan</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('care-worker.weeklycareplans.create') }}">Weekly Care Plan</a></li>
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