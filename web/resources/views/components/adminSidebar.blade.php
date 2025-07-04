@php
    // Check if we're on a messaging page
    $isMessagingView = Request::is('*/messaging') || 
                      Request::is('*/messaging/*') || 
                      Request::routeIs('admin.messaging.*');
@endphp

{{-- Load the appropriate CSS based on the current page --}}
@if($isMessagingView)
    <link rel="stylesheet" href="{{ asset('css/staffMessagingSidebar.css') }}">
@else
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
@endif

@php
use App\Helpers\TranslationHelper as T;
@endphp

<div class="sidebar {{ $isMessagingView ? 'messaging-sidebar' : '' }}">
  <div class="logo-details" id="logoToggle">
    <i class="bi bi-list"></i>
    <span class="logo_name">Menu</span>
  </div>
  <ul class="nav-links">
    <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
      <a href="{{ route('admin.dashboard') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.dashboard') }}'; return false;" @endif>
        <i class="bi bi-grid"></i>
        <span class="link_name">Dashboard</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.dashboard') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.dashboard') }}'; return false;" @endif>Dashboard</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::is('admin/beneficiaries*') || Request::is('admin/families*') || Request::is('admin/care-workers*') || Request::is('admin/care-managers*') || Request::is('admin/administrators*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-people"></i>
          <span class="link_name">User Management</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">User Management</a></li>
        <li><a href="{{ route('admin.beneficiaries.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.beneficiaries.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.beneficiaries.*') ? 'active' : '' }}">{{ T::translate('Beneficiary Profile', 'Profile ng Benepisyaryo')}}</a></li>
        <li><a href="{{ route('admin.families.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.families.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.families.*') ? 'active' : '' }}">{{ T::translate('Family Profile', 'Profile ng Pamilya')}}</a></li>
        <li><a href="{{ route('admin.careworkers.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.careworkers.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.careworkers.*') ? 'active' : '' }}">{{ T::translate('Care Worker Profile', 'Profile ng Tagapag-alaga')}}</a></li>
        <li><a href="{{ route('admin.caremanagers.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.caremanagers.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.caremanagers.*') ? 'active' : '' }}">{{ T::translate('Care Manager Profile', 'Profile ng Care Manager')}}</a></li>
        <li><a href="{{ route('admin.administrators.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.administrators.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.administrators.*') ? 'active' : '' }}">{{ T::translate('Admin Profile', 'Profile ng Admin')}}</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.careworker.performance.*') || Request::routeIs('admin.health.monitoring.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-file-earmark-text"></i>
          <span class="link_name">Report Management</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Report Management</a></li>
        <li><a href="{{ route('admin.careworker.performance.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.careworker.performance.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.careworker.performance.*') ? 'active' : '' }}">Care Worker Performance</a></li>
        <li><a href="{{ route('admin.health.monitoring.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.health.monitoring.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.health.monitoring.*') ? 'active' : '' }}">{{ T::translate('Health Monitoring', 'Pagsubaybay sa Kalusugan')}}</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.reports') || Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-clipboard-data"></i>
          <span class="link_name">{{ T::translate('Care Records', 'Tala ng mga Pangagalaga')}}</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">{{ T::translate('Care Records', 'Tala ng mga Pangangalaga')}}</a></li>
        <li><a href="{{ route('admin.reports') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.reports') }}'; return false;" @endif class="{{ Request::routeIs('admin.reports') ? 'active' : '' }}">{{ T::translate('Records Management', 'Pamamahala sa mga Tala')}}</a></li>
        <li><a href="{{ route('admin.weeklycareplans.create') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.weeklycareplans.create') }}'; return false;" @endif class="{{ Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">Weekly Care Plan</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.careworker.appointments.*') || Request::routeIs('admin.internal-appointments.*') || Request::routeIs('admin.medication.schedule.*')? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-calendar-week"></i>
          <span class="link_name">Schedules & Appointments</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Schedules & Appointments</a></li>
        <li><a href="{{ route('admin.careworker.appointments.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.careworker.appointments.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.careworker.appointments.*') ? 'active' : '' }}">Care Worker Appointment</a></li>
        <li><a href="{{ route('admin.internal-appointments.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.internal-appointments.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.internal-appointments.*') ? 'active' : '' }}">Internal Apppointment</a></li>
        <li><a href="{{ route('admin.medication.schedule.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.medication.schedule.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.medication.schedule.*') ? 'active' : '' }}">{{ T::translate('Medication Schedule', 'Iskedyul ng Gamot')}}</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.beneficiary.map.*') || Request::routeIs('admin.careworker.tracking.*')  ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-geo-alt"></i>
          <span class="link_name">{{ T::translate('Location Tracking', 'Pagsubaybay sa Lokasyon')}}</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">{{ T::translate('Location Tracking', 'Pagsubaybay sa Lokasyon')}}</a></li>
        <li><a href="{{ route('admin.beneficiary.map.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.beneficiary.map.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.beneficiary.map.*') ? 'active' : '' }}">{{ T::translate('Beneficiary Map', 'Mapa ng Benepisyaryo')}}</a></li>
        <li><a href="{{ route('admin.shift.histories.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.shift.histories.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.shift.histories.*') ? 'active' : '' }}">Shift Histories</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.locations.*') ? 'active' : '' }}">
      <a href="{{ route('admin.locations.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.locations.index') }}'; return false;" @endif>
        <i class="bi bi-map"></i>
        <span class="link_name">{{ T::translate('Municipality Management', 'Pamamahala sa Munisipalidad')}}</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.locations.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.locations.index') }}'; return false;" @endif>{{ T::translate('Municipality Management', 'Pamamahala sa Municipalidad')}}</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('admin.expense.*') ? 'active' : '' }}">
      <a href="{{ route('admin.expense.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.expense.index') }}'; return false;" @endif>
        <i class="bi bi-cash-stack"></i>
        <span class="link_name">{{ T::translate('Expenses Tracker', 'Pagsubaybay sa mga Gastos')}}</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.expense.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.expense.index') }}'; return false;" @endif>{{ T::translate('Expenses Tracker', 'Pagsubaybay sa mga Gastos')}}</a></li>
      </ul>
    </li>
    
    <li class="{{ Request::routeIs('admin.emergency.request.*') ? 'active' : '' }}">
      <a href="{{ route('admin.emergency.request.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.emergency.request.index') }}'; return false;" @endif class="">
        <i class="bi bi-exclamation-triangle"></i>
        <span class="link_name">{{ T::translate('Emergency & Request', 'Emergency at Pakiusap')}}</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.emergency.request.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.emergency.request.index') }}'; return false;" @endif>{{ T::translate('Emergency & Requests', 'Emergency at Pakiusap')}}</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('admin.ai-summary.*') ? 'active' : '' }}">
      <a href="{{ route('admin.ai-summary.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.ai-summary.index') }}'; return false;" @endif>
        <i class="bi bi-stars"></i>
        <span class="link_name">AI Summary</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.ai-summary.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.ai-summary.index') }}'; return false;" @endif>AI Summary</a></li>
      </ul>
    </li>
    
    <li class="dropdown {{ Request::routeIs('admin.donor.acknowledgement.*') || Request::routeIs('admin.highlights.events.*') ? 'active' : '' }}">
      <div class="icon-link">
        <a class="parent-link">
          <i class="bi bi-globe"></i>
          <span class="link_name">Site Management</span>
        </a>
        <i class="bi bi-chevron-down arrow"></i>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">Site Management</a></li>
        <li><a href="{{ route('admin.donor.acknowledgement.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.donor.acknowledgement.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.donor.acknowledgement.*') ? 'active' : '' }}">{{ T::translate('Donor Acknowledgement', 'Pagkilala sa Donor')}}</a></li>
        <li><a href="{{ route('admin.highlights.events.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('admin.highlights.events.index') }}'; return false;" @endif class="{{ Request::routeIs('admin.highlights.events.*') ? 'active' : '' }}">{{ T::translate('Events & Highlights', 'Mga Kaganapan at Highlights')}}</a></li>
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
    background-color: #f1f0fe;
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