@php
    // Check if we're on a messaging page
    $isMessagingView = Request::is('*/messaging') || 
                      Request::is('*/messaging/*') || 
                      Request::routeIs('admin.messaging.*');
    
    // Hard-coded role prefix for admin sidebar
    $rolePrefix = 'admin';

    // Function to get correct URL based on route definition
    function getCorrectUrl($routeName, $isMessaging = false) {
        $routeMap = [
            'dashboard' => '/admin/dashboard',
            'beneficiaries' => '/admin/beneficiaries',
            'families' => '/admin/families',
            'care-workers' => '/admin/care-workers',
            'care-managers' => '/admin/care-managers',
            'administrators' => '/admin/administrators',
            'careworker-performance' => '/admin/care-worker-performance',
            'health-monitoring' => '/admin/health-monitoring',
            'reports' => '/admin/records',
            'weeklycareplans' => '/admin/weekly-care-plans/create',
            'careworker-appointments' => '/admin/careworker-appointments',
            'internal-appointments' => '/admin/internal-appointments',
            'medication-schedule' => '/admin/medication-schedule',
            'beneficiary-map' => '/admin/beneficiary-map',
            'careworker-tracking' => '/admin/careworker-tracking',
            'shift-histories' => '/admin/shift-histories',
            'locations' => '/admin/locations',
            'expense' => '/admin/expense-tracker',
            'emergency-request' => '/admin/emergency-request',
            'aiSummary' => '/admin/ai-summary',
            'donor-acknowledgement' => '/admin/donor-acknowledgement',
            'highlights-events' => '/admin/highlights-events',
        ];
        
        $url = isset($routeMap[$routeName]) ? $routeMap[$routeName] : '/admin/' . $routeName;
        // Only add direct=1 if we're in messaging view
        return $isMessaging ? $url . '?direct=1' : $url;
    }
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
    <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
      <a href="{{ getCorrectUrl('dashboard', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('dashboard', true) }}'; return false;" @endif>
        <i class="bi bi-grid"></i>
        <span class="link_name">Dashboard</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ getCorrectUrl('dashboard', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('dashboard', true) }}'; return false;" @endif>Dashboard</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::is('admin/beneficiaries*') || Request::is('admin/families*') || Request::is('admin/care-workers*') || Request::is('admin/care-managers*') || Request::is('admin/administrators*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link" @if($isMessagingView) data-href="{{ getCorrectUrl('beneficiaries', true) }}" @endif>
          <i class="bi bi-people"></i>
          <span class="link_name">User Management</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">User Management</a></li>
        <li><a href="{{ getCorrectUrl('beneficiaries', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('beneficiaries', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.beneficiaries.*') ? 'active' : '' }}">Beneficiary Profile</a></li>
        <li><a href="{{ getCorrectUrl('families', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('families', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.families.*') ? 'active' : '' }}">Family Profile</a></li>
        <li><a href="{{ getCorrectUrl('care-workers', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('care-workers', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.careworkers.*') ? 'active' : '' }}">Care Worker Profile</a></li>
        <li><a href="{{ getCorrectUrl('care-managers', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('care-managers', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.caremanagers.*') ? 'active' : '' }}">Care Manager Profile</a></li>
        <li><a href="{{ getCorrectUrl('administrators', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('administrators', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.administrators.*') ? 'active' : '' }}">Admin Profile</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.careworker.performance.*') || Request::routeIs('admin.health.monitoring.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link" @if($isMessagingView) data-href="{{ getCorrectUrl('careworker-performance', true) }}" @endif>
          <i class="bi bi-file-earmark-text"></i>
          <span class="link_name">Report Management</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Report Management</a></li>
        <li><a href="{{ getCorrectUrl('careworker-performance', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('careworker-performance', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.careworker.performance.*') ? 'active' : '' }}">Care Worker Performance</a></li>
        <li><a href="{{ getCorrectUrl('health-monitoring', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('health-monitoring', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.health.monitoring.*') ? 'active' : '' }}">Health Monitoring</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.reports') || Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link" @if($isMessagingView) data-href="{{ getCorrectUrl('reports', true) }}" @endif>
          <i class="bi bi-clipboard-data"></i>
          <span class="link_name">Care Records</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Care Records</a></li>
        <li><a href="{{ getCorrectUrl('reports', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('reports', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.reports') ? 'active' : '' }}">Records Management</a></li>
        <li><a href="{{ getCorrectUrl('weeklycareplans', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('weeklycareplans', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">Weekly Care Plan</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.careworker.appointments.*') || Request::routeIs('admin.internal.appointments.*') || Request::routeIs('admin.medication.schedule.*')? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link" @if($isMessagingView) data-href="{{ getCorrectUrl('careworker-appointments', true) }}" @endif>
          <i class="bi bi-calendar-week"></i>
          <span class="link_name">Schedules & Appointments</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Schedules & Appointments</a></li>
        <li><a href="{{ getCorrectUrl('careworker-appointments', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('careworker-appointments', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.careworker.appointments.*') ? 'active' : '' }}">Care Worker Appointment</a></li>
        <li><a href="{{ getCorrectUrl('internal-appointments', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('internal-appointments', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.internal.appointments.*') ? 'active' : '' }}">Internal Appointment</a></li>
        <li><a href="{{ getCorrectUrl('medication-schedule', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('medication-schedule', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.medication.schedule.*') ? 'active' : '' }}">Medication Schedule</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.beneficiary.map.*') || Request::routeIs('admin.careworker.tracking.*')  ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link" @if($isMessagingView) data-href="{{ getCorrectUrl('beneficiary-map', true) }}" @endif>
          <i class="bi bi-geo-alt"></i>
          <span class="link_name">Location Tracking</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Location Tracking</a></li>
        <li><a href="{{ getCorrectUrl('beneficiary-map', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('beneficiary-map', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.beneficiary.map.*') ? 'active' : '' }}">Beneficiary Map</a></li>
        <li><a href="{{ getCorrectUrl('careworker-tracking', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('careworker-tracking', true) }}'; return false;" @endif class="">Care Worker Tracking</a></li>
        <li><a href="{{ getCorrectUrl('shift-histories', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('shift-histories', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.shift.histories.*') ? 'active' : '' }}">Shift Histories</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.locations.*') ? 'active' : '' }}">
      <a href="{{ getCorrectUrl('locations', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('locations', true) }}'; return false;" @endif>
        <i class="bi bi-map"></i>
        <span class="link_name">Municipality Management</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ getCorrectUrl('locations', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('locations', true) }}'; return false;" @endif>Municipality Management</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('admin.expense.*') ? 'active' : '' }}">
      <a href="{{ getCorrectUrl('expense', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('expense', true) }}'; return false;" @endif>
        <i class="bi bi-cash-stack"></i>
        <span class="link_name">Expenses Tracker</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ getCorrectUrl('expense', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('expense', true) }}'; return false;" @endif>Expenses Tracker</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.emergency.request.*') ? 'active' : '' }}">
      <a href="{{ getCorrectUrl('emergency-request', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('emergency-request', true) }}'; return false;" @endif class="">
        <i class="bi bi-exclamation-triangle"></i>
        <span class="link_name">Emergency & Request</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ getCorrectUrl('emergency-request', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('emergency-request', true) }}'; return false;" @endif>Emergency & Request</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('admin.aiSummary.*') ? 'active' : '' }}">
      <a href="{{ getCorrectUrl('aiSummary', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('aiSummary', true) }}'; return false;" @endif>
        <i class="bi bi-stars"></i>
        <span class="link_name">AI Summary</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ getCorrectUrl('aiSummary', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('aiSummary', true) }}'; return false;" @endif>AI Summary</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.donor.acknowledgement.*') || Request::routeIs('admin.highlights.events.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link" @if($isMessagingView) data-href="{{ getCorrectUrl('donor-acknowledgement', true) }}" @endif>
          <i class="bi bi-globe"></i>
          <span class="link_name">Site Management</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Site Management</a></li>
        <li><a href="{{ getCorrectUrl('donor-acknowledgement', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('donor-acknowledgement', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.donor.acknowledgement.*') ? 'active' : '' }}">Donor Acknowledgement</a></li>
        <li><a href="{{ getCorrectUrl('highlights-events', $isMessagingView) }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ getCorrectUrl('highlights-events', true) }}'; return false;" @endif class="{{ Request::routeIs('admin.highlights.events.*') ? 'active' : '' }}">Events & Highlights</a></li>
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
    position: fixed !important; /* CHANGED: using fixed positioning */
    margin-top: 0;
    padding: 10px 20px;
    border-radius: 0 6px 6px 0;
    opacity: 0;
    display: none;
    pointer-events: none;
    background-color: #f1f0fe;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
    z-index: 99999999 !important; /* Extreme z-index */
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
    z-index: 99999999 !important;
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
          
          // ADDED: Position the flyout at the correct vertical position
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
            
            // ADDED: Position the flyout at the correct vertical position
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