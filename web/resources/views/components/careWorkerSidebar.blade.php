@php
    // Check if we're on a messaging page
    $isMessagingView = Request::is('*/messaging') || 
                      Request::is('*/messaging/*') || 
                      Request::routeIs('care-worker.messaging.*');
@endphp

{{-- Load the appropriate CSS based on the current page --}}
@if($isMessagingView)
    <link rel="stylesheet" href="{{ asset('css/staffMessagingSidebar.css') }}">
@else
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
@endif

<div class="sidebar {{ $isMessagingView ? 'messaging-sidebar' : '' }}">
  <div class="logo-details" id="logoToggle">
    <i class="bi bi-list"></i>
    <span class="logo_name">Menu</span>
  </div>
  <ul class="nav-links">
    <li class="{{ Request::routeIs('care-worker.dashboard') ? 'active' : '' }}">
      <a href="{{ route('care-worker.dashboard') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.dashboard') }}'; return false;" @endif>
        <i class="bi bi-grid"></i>
        <span class="link_name">Dashboard</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('care-worker.dashboard') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.dashboard') }}'; return false;" @endif>Dashboard</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::is('care-worker/beneficiaries*') || Request::is('care-worker/family-members*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-people"></i>
          <span class="link_name">User Management</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">User Management</a></li>
        <li><a href="{{ route('care-worker.beneficiaries.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.beneficiaries.index') }}'; return false;" @endif class="{{ Request::routeIs('care-worker.showBeneficiary') || Request::routeIs('care-worker.addBeneficiary') ? 'active' : '' }}">Beneficiary Profiles</a></li>
        <li><a href="{{ route('care-worker.families.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.families.index') }}'; return false;" @endif class="{{ Request::routeIs('care-worker.showFamilyMember') || Request::routeIs('care-worker.addFamilyMember') ? 'active' : '' }}">Family or Relative Profiles</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('care-worker.reports') || Request::routeIs('care-worker.weeklycareplans.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-clipboard-data"></i>
          <span class="link_name">Care Records</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Care Records</a></li>
        <li><a href="{{ route('care-worker.reports') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.reports') }}'; return false;" @endif class="{{ Request::routeIs('care-worker.reports') ? 'active' : '' }}">Records Management</a></li>
        <li><a href="{{ route('care-worker.weeklycareplans.create') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.weeklycareplans.create') }}'; return false;" @endif class="{{ Request::routeIs('care-worker.weeklycareplans.*') ? 'active' : '' }}">Weekly Care Plan</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('care-worker.careworker.appointments.*') || Request::routeIs('care-worker.internal-appointments.*') || Request::routeIs('care-worker.medication.schedule.*')? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-calendar-week"></i>
          <span class="link_name">Schedules & Appointments</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Schedules & Appointments</a></li>
        <li><a href="{{ route('care-worker.careworker.appointments.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.careworker.appointments.index') }}'; return false;" @endif class="{{ Request::routeIs('care-worker.careworker.appointments.*') ? 'active' : '' }}">Care Worker Appointment</a></li>
        <li><a href="{{ route('care-worker.internal-appointments.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.internal-appointments.index') }}'; return false;" @endif class="{{ Request::routeIs('care-worker.internal-appointments.*') ? 'active' : '' }}">Internal Appointment</a></li>
        <li><a href="{{ route('care-worker.medication.schedule.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.medication.schedule.index') }}'; return false;" @endif class="{{ Request::routeIs('care-worker.medication.schedule.*') ? 'active' : '' }}">Medication Schedule</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('care-worker.emergency.request.*') ? 'active' : '' }}">
      <a href="{{ route('care-worker.emergency.request.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.emergency.request.index') }}'; return false;" @endif>
        <i class="bi bi-exclamation-triangle"></i>
        <span class="link_name">Emergency & Request</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('care-worker.emergency.request.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('care-worker.emergency.request.index') }}'; return false;" @endif>Emergency & Request</a></li>
      </ul>
    </li>
  </ul>
</div>

<!-- Custom CSS for flyout menu behavior -->
<style>
  /* Base styles for the sidebar */
  .sidebar .nav-links li .sub-menu {
    display: none; /* Hide sub-menus by default */
  }
  
  /* Show sub-menu when parent li has showMenu class (used in expanded mode) */
  .sidebar:not(.close) .nav-links li.showMenu .sub-menu {
    display: block;
  }
  
  /* COLLAPSED MODE - Flyout styles */
  .sidebar.close .nav-links li .sub-menu {
    position: fixed !important; /* Using fixed positioning */
    margin-top: 0;
    padding: 10px 20px;
    border-radius: 0 6px 6px 0;
    opacity: 0;
    display: none;
    pointer-events: none;
    background-color: #fff;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
    z-index: 9999999 !important; /* Extreme z-index */
    transition: none;
  }

  /* Position flyouts correctly using fixed positioning */
  .sidebar.close .nav-links li .sub-menu {
    top: auto !important; /* Will be set by JavaScript */
    left: 80px !important; /* Position to the right of collapsed sidebar */
  }

  /* Show sub-menu in collapsed mode when clicked */
  .sidebar.close .nav-links li.showFlyout .sub-menu {
    display: block !important;
    opacity: 1 !important;
    pointer-events: auto !important;
  }
  
  /* Fix arrow display */
  .sidebar.close .nav-links li .icon-link .arrow {
    display: none !important;
  }
  
  .sidebar:not(.close) .nav-links li .icon-link .arrow {
    display: block;
  }

  /* Menu styling */
  .sidebar .nav-links li .sub-menu a {
    padding: 8px 0;
    display: block;
  }
  
  .sidebar .nav-links li .sub-menu li {
    margin-bottom: 5px;
  }

  /* IMPORTANT: Force conversation-list behind flyouts */
  .conversation-list, 
  .conversation-list *,
  .conversation-list-items,
  .conversation-search,
  .message-area {
    z-index: 1 !important;
  }
  
  /* Extra specificity for the flyouts */
  html body .sidebar.close .nav-links li.showFlyout .sub-menu {
    z-index: 9999999 !important;
  }
  
  /* Active menu item styling */
  .sidebar .nav-links li.active > .icon-link,
  .sidebar .nav-links li.active > a {
    background-color: #5a595d;
    color: #fff;
    border-radius: 8px;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const logoToggle = document.getElementById('logoToggle');
    
    // Toggle sidebar open/close when menu icon is clicked
    if (logoToggle) {
      logoToggle.addEventListener('click', function(e) {
        e.preventDefault();
        sidebar.classList.toggle('close');
      });
    }

    // Reset any active flyouts when toggling the sidebar state
    sidebar.addEventListener('transitionend', function() {
      document.querySelectorAll('.showFlyout').forEach(item => {
        item.classList.remove('showFlyout');
      });
    });
    
    // Dropdown toggle in expanded mode
    document.querySelectorAll('.nav-links li.dropdown .arrow').forEach(arrow => {
      arrow.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Only handle clicks in expanded mode
        if (!sidebar.classList.contains('close')) {
          const parent = this.closest('li');
          parent.classList.toggle('showMenu');
        }
      });
    });

    // Handle clicks on parent items in collapsed mode to show flyouts
    document.querySelectorAll('.nav-links li.dropdown .parent-link').forEach(link => {
      link.addEventListener('click', function(e) {
        // Only toggle flyouts if sidebar is collapsed
        if (sidebar.classList.contains('close')) {
          e.preventDefault();
          e.stopPropagation();
          
          const parentLi = this.closest('li');
          
          // Close all other open flyouts first
          document.querySelectorAll('.nav-links li.showFlyout').forEach(item => {
            if (item !== parentLi) {
              item.classList.remove('showFlyout');
            }
          });
          
          // Toggle this flyout
          parentLi.classList.toggle('showFlyout');
          
          // Position the flyout at the correct vertical position
          if (parentLi.classList.contains('showFlyout')) {
            const subMenu = parentLi.querySelector('.sub-menu');
            if (subMenu) {
              const rect = parentLi.getBoundingClientRect();
              subMenu.style.top = rect.top + 'px';
            }
          }
        }
      });
    });
    
    // Close flyouts when clicking outside
    document.addEventListener('click', function(e) {
      // If click is outside any dropdown item
      if (!e.target.closest('.sidebar .nav-links li.dropdown')) {
        document.querySelectorAll('.nav-links li.showFlyout').forEach(item => {
          item.classList.remove('showFlyout');
        });
      }
    });

    // For messaging view, ensure links navigate properly
    if (sidebar.classList.contains('messaging-sidebar')) {
      document.querySelectorAll('.sidebar a[href]').forEach(link => {
        const href = link.getAttribute('href');
        if (href && !href.startsWith('#')) {
          link.addEventListener('click', function(e) {
            e.preventDefault();
            window.top.location.href = href;
            return false;
          });
        }
      });
      
      // Additional touch support for mobile in messaging view
      document.querySelectorAll('.sidebar.messaging-sidebar .dropdown').forEach(item => {
        item.addEventListener('touchstart', function(e) {
          const isCollapsed = sidebar.classList.contains('close');
          const target = e.target;
          
          // Only handle if it's a parent link and sidebar is collapsed
          if (isCollapsed && 
              (target.classList.contains('parent-link') || 
               target.parentElement.classList.contains('parent-link'))) {
            
            e.preventDefault();
            e.stopPropagation();
            
            // Close other flyouts
            document.querySelectorAll('.nav-links li.showFlyout').forEach(el => {
              if (el !== this) {
                el.classList.remove('showFlyout');
              }
            });
            
            // Toggle this flyout
            this.classList.toggle('showFlyout');
            
            // Position the flyout at the correct vertical position
            if (this.classList.contains('showFlyout')) {
              const subMenu = this.querySelector('.sub-menu');
              if (subMenu) {
                const rect = this.getBoundingClientRect();
                subMenu.style.top = rect.top + 'px';
              }
            }
          }
        });
      });
    }
    
    // Handle responsive behavior
    function handleSidebarState() {
      if (window.innerWidth <= 768) {
        sidebar.classList.add('close');
      }
    }
    
    // Force sidebar to collapsed state in messaging view
    if (sidebar.classList.contains('messaging-sidebar')) {
      sidebar.classList.add('close');
    }
    
    handleSidebarState();
    window.addEventListener('resize', handleSidebarState);
  });
</script>