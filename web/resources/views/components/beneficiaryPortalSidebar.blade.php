<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

<div class="sidebar">
  <div class="logo-details" id="logoToggle">
    <i class="bi bi-people-fill"></i>
    <span class="logo_name">Beneficiary Portal</span>
  </div>
  
  <ul class="nav-links">
    <li class="{{ Request::is('beneficiary.dashboard') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.dashboard') }}">
        <i class="bi bi-house-door"></i>
        <span class="link_name">Home</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.dashboard') }}">Home</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.visitation.schedule.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.visitation.schedule.index') }}">
        <i class="bi bi-calendar-check"></i>
        <span class="link_name">Visitation Schedule</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.visitation.schedule.index') }}">Visitation Schedule</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.medication.schedule.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.medication.schedule.index') }}">
        <i class="bi bi-capsule"></i>
        <span class="link_name">Medication Schedule</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.medication.schedule.index') }}">Medication Schedule</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.emergency.service.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.emergency.service.index') }}">
        <i class="bi bi-clipboard2-pulse"></i>
        <span class="link_name">Emergency & Service Requests</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.emergency.service.index') }}">Emergency & Service Requests</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.care.plan.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.care.plan.index') }}">
        <i class="bi bi-clipboard2-check"></i>
        <span class="link_name">Care Plan</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.care.plan.index') }}">Care Plan</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.member.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.member.index') }}">
        <i class="bi bi-people-fill"></i>
        <span class="link_name">Family Members</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.member.index') }}">Family Members</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.faQuestions.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.faQuestions.index') }}">
        <i class="bi bi-question-square"></i>
        <span class="link_name">Frequently Asked Question</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.faQuestions.index') }}">Frequently Asked Question</a></li>
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