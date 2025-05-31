<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

<div class="sidebar">
  <div class="logo-details" id="logoToggle">
    <i class="bi bi-people-fill"></i>
    <span class="logo_name">Family Portal</span>
  </div>
  
  <ul class="nav-links">
    <li class="{{ Request::is('family/homePage') ? 'active' : '' }}">
      <a href="{{ route('familyPortalHomePage') }}">
        <i class="bi bi-house-door"></i>
        <span class="link_name">Home</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('familyPortalHomePage') }}">Home</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('family.visitation.schedule.*') ? 'active' : '' }}">
      <a href="{{ route('family.visitation.schedule.index') }}">
        <i class="bi bi-calendar-check"></i>
        <span class="link_name">Visitation Schedule</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('family.visitation.schedule.index') }}">Visitation Schedule</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('family.medication.schedule.*') ? 'active' : '' }}">
      <a href="{{ route('family.medication.schedule.index') }}">
        <i class="bi bi-capsule"></i>
        <span class="link_name">Medication Schedule</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('family.medication.schedule.index') }}">Medication Schedule</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('family.emergency.service.*') ? 'active' : '' }}">
      <a href="{{ route('family.emergency.service.index') }}">
        <i class="bi bi-clipboard2-pulse"></i>
        <span class="link_name">Emergency & Service Requests</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('family.emergency.service.index') }}">Emergency & Service Requests</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('family.care.plan.*') ? 'active' : '' }}">
      <a href="{{ route('family.care.plan.index') }}">
        <i class="bi bi-clipboard2-check"></i>
        <span class="link_name">Care Plan</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('family.care.plan.index') }}">Care Plan</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('family.family.member.*') ? 'active' : '' }}">
      <a href="{{ route('family.family.member.index') }}">
        <i class="bi bi-people-fill"></i>
        <span class="link_name">Family Members</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('family.family.member.index') }}">Family Members</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('family.faQuestions.*') ? 'active' : '' }}">
      <a href="{{ route('family.faQuestions.index') }}">
        <i class="bi bi-question-square"></i>
        <span class="link_name">Frequently Asked Question</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('family.faQuestions.index') }}">Frequently Asked Question</a></li>
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