// Add this code to a new file: /public/js/calendar-fix-v2.js

/**
 * Advanced FullCalendar Event Fix
 * Ensures ALL events are displayed consistently across all days
 */
(function() {
    // Wait for window to fully load everything
    window.addEventListener('load', function() {
        // Apply fix after everything is fully loaded
        setTimeout(applyFix, 1500);
        
        // Also apply when changing views or navigating
        document.addEventListener('click', function(e) {
            if (e.target.closest('.fc-button')) {
                setTimeout(applyFix, 500);
            }
        });
    });
    
    function applyFix() {
        console.log("Applying advanced calendar fix...");
        
        try {
            if (!window.calendar || typeof window.calendar.getEvents !== 'function') {
                console.error("Calendar not accessible - will try again");
                setTimeout(applyFix, 500);
                return;
            }
            
            // 1. First clear all existing cells and rebuild them
            rebuildAllDayCells();
            
            // 2. Force all day cells to expand to their maximum height
            document.querySelectorAll('.fc-daygrid-day-frame').forEach(cell => {
                cell.style.height = 'auto';
                cell.style.maxHeight = 'none';
                cell.style.overflow = 'visible';
            });
            
            console.log("All cells rebuilt and expanded");
        } catch (error) {
            console.error("Error in calendar fix:", error);
        }
    }
    
    function rebuildAllDayCells() {
        // Get all day cells
        const dayCells = document.querySelectorAll('.fc-daygrid-day');
        
        dayCells.forEach(dayCell => {
            const dateAttr = dayCell.getAttribute('data-date');
            if (!dateAttr) return;
            
            // Get all events for this date
            const eventsForDay = window.calendar.getEvents().filter(event => {
                if (!event.start) return false;
                const eventDate = new Date(event.start);
                const eventDateStr = eventDate.toISOString().split('T')[0];
                return eventDateStr === dateAttr;
            });
            
            // Skip if no events
            if (eventsForDay.length === 0) return;
            
            // Find the events container for this day
            const eventsContainer = dayCell.querySelector('.fc-daygrid-day-events');
            if (!eventsContainer) return;
            
            // Clear existing day frame and make it extremely tall to fit everything
            const dayFrame = dayCell.querySelector('.fc-daygrid-day-frame');
            if (dayFrame) {
                dayFrame.style.minHeight = 'auto';
                dayFrame.style.height = 'auto';
                dayFrame.style.maxHeight = 'none';
                dayFrame.style.overflow = 'visible';
            }
            
            // Add a counter badge showing the number of appointments
            const countsContainer = dayCell.querySelector('.fc-daygrid-day-top');
            if (countsContainer && !countsContainer.querySelector('.event-count-badge')) {
                countsContainer.style.position = 'relative';
                
                const countBadge = document.createElement('div');
                countBadge.className = 'event-count-badge';
                countBadge.style.position = 'absolute';
                countBadge.style.right = '5px';
                countBadge.style.top = '0';
                countBadge.style.background = '#4e73df';
                countBadge.style.color = 'white';
                countBadge.style.borderRadius = '12px';
                countBadge.style.padding = '2px 8px';
                countBadge.style.fontSize = '11px';
                countBadge.style.fontWeight = 'bold';
                countBadge.style.zIndex = '100';
                countBadge.textContent = eventsForDay.length + ' appointments';
                
                countBadge.style.cursor = 'pointer';
                countBadge.addEventListener('click', function(e) {
                    e.stopPropagation();
                    showAllEventsModal(dateAttr, eventsForDay);
                });
                
                countsContainer.appendChild(countBadge);
            }
        });
    }
    
    function showAllEventsModal(dateStr, events) {
        // Create modal backdrop
        const backdrop = document.createElement('div');
        backdrop.style.position = 'fixed';
        backdrop.style.top = '0';
        backdrop.style.left = '0';
        backdrop.style.right = '0';
        backdrop.style.bottom = '0';
        backdrop.style.backgroundColor = 'rgba(0,0,0,0.5)';
        backdrop.style.zIndex = '9999';
        backdrop.style.display = 'flex';
        backdrop.style.alignItems = 'center';
        backdrop.style.justifyContent = 'center';
        
        // Format date nicely
        const dateObj = new Date(dateStr);
        const formattedDate = dateObj.toLocaleDateString(undefined, {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Create modal content
        const modal = document.createElement('div');
        modal.style.backgroundColor = 'white';
        modal.style.borderRadius = '8px';
        modal.style.boxShadow = '0 5px 15px rgba(0,0,0,0.3)';
        modal.style.width = '90%';
        modal.style.maxWidth = '800px';
        modal.style.maxHeight = '90vh';
        modal.style.display = 'flex';
        modal.style.flexDirection = 'column';
        modal.style.overflow = 'hidden';
        
        // Create modal header
        const header = document.createElement('div');
        header.style.backgroundColor = '#4e73df';
        header.style.color = 'white';
        header.style.padding = '15px 20px';
        header.style.display = 'flex';
        header.style.justifyContent = 'space-between';
        header.style.alignItems = 'center';
        
        const title = document.createElement('h4');
        title.style.margin = '0';
        title.style.padding = '0';
        title.innerHTML = `<i class="bi bi-calendar-date"></i> ${formattedDate} <span style="font-size:16px; opacity:0.8;">(${events.length} appointments)</span>`;
        
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.background = 'none';
        closeBtn.style.border = 'none';
        closeBtn.style.color = 'white';
        closeBtn.style.fontSize = '24px';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.padding = '0 5px';
        closeBtn.style.marginLeft = '10px';
        closeBtn.onclick = function() {
            document.body.removeChild(backdrop);
        };
        
        header.appendChild(title);
        header.appendChild(closeBtn);
        
        // Create modal body
        const body = document.createElement('div');
        body.style.padding = '15px';
        body.style.overflowY = 'auto';
        body.style.maxHeight = 'calc(90vh - 130px)';
        
        // Create event list
        const eventsList = document.createElement('div');
        
        // Add each event to list
        events.forEach((event, index) => {
            const item = document.createElement('div');
            item.style.padding = '10px';
            item.style.borderBottom = '1px solid #eee';
            item.style.display = 'flex';
            item.style.alignItems = 'flex-start';
            item.style.cursor = 'pointer';
            
            // Add hover effect
            item.onmouseenter = function() { this.style.backgroundColor = '#f8f9fa'; };
            item.onmouseleave = function() { this.style.backgroundColor = 'transparent'; };
            
            // Add click handler to select event
            item.onclick = function() {
                document.body.removeChild(backdrop);
                selectCalendarEvent(event);
            };
            
            // Create color indicator
            const colorDot = document.createElement('div');
            colorDot.style.width = '12px';
            colorDot.style.height = '12px';
            colorDot.style.borderRadius = '50%';
            colorDot.style.backgroundColor = event.backgroundColor || '#4e73df';
            colorDot.style.marginRight = '10px';
            colorDot.style.marginTop = '4px';
            colorDot.style.flexShrink = '0';
            
            // Create content
            const content = document.createElement('div');
            content.style.flex = '1';
            
            const isFlexible = event.extendedProps && event.extendedProps.is_flexible_time;
            let timeStr = '';
            
            if (isFlexible) {
                timeStr = '<i class="bi bi-clock"></i> Flexible Time';
            } else if (event.start && event.end) {
                const startTime = event.start.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
                const endTime = event.end.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
                timeStr = `${startTime} - ${endTime}`;
            }
            
            content.innerHTML = `
                <div style="font-weight:500; margin-bottom:3px;">${event.title}</div>
                <div style="font-size:12px; color:#666; margin-bottom:2px;">${timeStr}</div>
                <div style="font-size:12px; color:#888;">${event.extendedProps ? event.extendedProps.visit_type : ''}</div>
            `;
            
            item.appendChild(colorDot);
            item.appendChild(content);
            eventsList.appendChild(item);
        });
        
        body.appendChild(eventsList);
        
        // Create modal footer
        const footer = document.createElement('div');
        footer.style.padding = '10px 15px';
        footer.style.borderTop = '1px solid #dee2e6';
        footer.style.textAlign = 'right';
        footer.style.backgroundColor = '#f8f9fa';
        
        const closeModalBtn = document.createElement('button');
        closeModalBtn.textContent = 'Close';
        closeModalBtn.className = 'btn btn-secondary';
        closeModalBtn.style.padding = '5px 15px';
        closeModalBtn.style.backgroundColor = '#6c757d';
        closeModalBtn.style.border = 'none';
        closeModalBtn.style.borderRadius = '4px';
        closeModalBtn.style.color = 'white';
        closeModalBtn.style.cursor = 'pointer';
        closeModalBtn.onclick = function() {
            document.body.removeChild(backdrop);
        };
        
        footer.appendChild(closeModalBtn);
        
        // Assemble modal
        modal.appendChild(header);
        modal.appendChild(body);
        modal.appendChild(footer);
        backdrop.appendChild(modal);
        
        // Add to page
        document.body.appendChild(backdrop);
    }
    
    function selectCalendarEvent(event) {
        // Try to find and click the original event element
        let found = false;
        
        document.querySelectorAll('.fc-event').forEach(el => {
            const title = el.querySelector('.event-title');
            if (title && title.textContent === event.title) {
                // Check if the date also matches
                const eventEl = el.closest('.fc-event');
                if (eventEl) {
                    eventEl.click();
                    found = true;
                }
            }
        });
        
        if (!found && window.showEventDetails) {
            // Fall back to programmatic selection
            window.showEventDetails(event);
        }
    }
})();