@php
    // Check if we're on a messaging page
    $isMessagingView = Request::is('*/messaging') || 
                      Request::is('*/messaging/*') || 
                      Request::routeIs('beneficiary.messaging.*');
@endphp

{{-- Load the appropriate CSS based on the current page --}}
@if($isMessagingView)
    <link rel="stylesheet" href="{{ asset('css/portalMessagingSidebar.css') }}">
@else
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
@endif
@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="sidebar {{ $isMessagingView ? 'messaging-sidebar' : '' }}">
  <div class="logo-details" id="logoToggle">
    <i class="bi bi-people-fill"></i>
    <span class="logo_name">Beneficiary Portal</span>
  </div>
  
  <ul class="nav-links">
    <li class="{{ Request::routeIs('beneficiary.dashboard') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.dashboard') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.dashboard') }}'; return false;" @endif>
        <i class="bi bi-house-door"></i>
        <span class="link_name">Home</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.dashboard') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.dashboard') }}'; return false;" @endif>Home</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.visitation.schedule.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.visitation.schedule.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.visitation.schedule.index') }}'; return false;" @endif>
        <i class="bi bi-calendar-check"></i>
        <span class="link_name">{{ T::translate('Visitation Schedule', 'Iskedyul ng Pagbisita')}}</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.visitation.schedule.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.visitation.schedule.index') }}'; return false;" @endif>Visitation Schedule</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.medication.schedule.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.medication.schedule.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.medication.schedule.index') }}'; return false;" @endif>
        <i class="bi bi-capsule"></i>
        <span class="link_name">{{ T::translate('Medication Schedule', 'Iskedyul ng Gamot')}}</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.medication.schedule.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.medication.schedule.index') }}'; return false;" @endif>Medication Schedule</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.emergency.service.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.emergency.service.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.emergency.service.index') }}'; return false;" @endif>
        <i class="bi bi-clipboard2-pulse"></i>
        <span class="link_name">{{ T::translate('Emergency & Service Requests', 'Emergency at Pakiusap na Serbisyo')}}</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.emergency.service.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.emergency.service.index') }}'; return false;" @endif>Emergency & Service Requests</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.care.plan.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.care.plan.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.care.plan.index') }}'; return false;" @endif>
        <i class="bi bi-clipboard2-check"></i>
        <span class="link_name">Care Plan</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.care.plan.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.care.plan.index') }}'; return false;" @endif>Care Plan</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.member.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.member.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.member.index') }}'; return false;" @endif>
        <i class="bi bi-people-fill"></i>
        <span class="link_name">{{ T::translate('Family Members', 'Mga Miyembro ng Pamilya')}}</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.member.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.member.index') }}'; return false;" @endif>Family Members</a></li>
      </ul>
    </li>

    <li class="{{ Request::routeIs('beneficiary.faQuestions.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.faQuestions.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.faQuestions.index') }}'; return false;" @endif>
        <i class="bi bi-question-square"></i>
        <span class="link_name">Frequently Asked Question</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.faQuestions.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.faQuestions.index') }}'; return false;" @endif>Frequently Asked Question</a></li>
      </ul>
    </li>
    
    <!-- <li class="{{ Request::routeIs('beneficiary.messaging.*') ? 'active' : '' }}">
      <a href="{{ route('beneficiary.messaging.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.messaging.index') }}'; return false;" @endif>
        <i class="bi bi-chat-dots"></i>
        <span class="link_name">Messages</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('beneficiary.messaging.index') }}" @if($isMessagingView) target="_top" onclick="window.top.location.href='{{ route('beneficiary.messaging.index') }}'; return false;" @endif>Messages</a></li>
      </ul>
    </li> -->
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
  
  /* Fix arrow display if any */
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

    // Handle dropdowns if we have any
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
    }
    
    // Handle responsive behavior
    function handleSidebarState() {
      if (window.innerWidth <= 768) {
        sidebar.classList.add("close");
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