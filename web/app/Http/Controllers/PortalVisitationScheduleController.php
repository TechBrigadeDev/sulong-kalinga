<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitation;
use App\Models\VisitationOccurrence;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PortalVisitationScheduleController extends Controller
{
    /**
     * Display the visitation schedule page
     */
    public function index(Request $request)
    {
        // Determine which guard is being used (beneficiary or family)
        $guard = null;
        $user = null;
        
        if (Auth::guard('beneficiary')->check()) {
            $guard = 'beneficiary';
            $user = Auth::guard('beneficiary')->user();
        } elseif (Auth::guard('family')->check()) {
            $guard = 'family';
            $user = Auth::guard('family')->user();
        } else {
            // No valid authentication found, redirect to login
            return redirect()->route('login')->with('error', 'Please login to access this page');
        }
        
        $userType = ($guard === 'beneficiary') ? 'beneficiary' : 'family';
        
        // Get beneficiary ID based on user type
        $beneficiaryId = null;
        if ($userType === 'beneficiary') {
            $beneficiaryId = $user->beneficiary_id;
        } else {
            // For family member, get the related beneficiary ID
            $beneficiaryId = $user->related_beneficiary_id;
        }
        
        // Load beneficiary data for display
        $beneficiary = Beneficiary::find($beneficiaryId);
        
        return view("$userType"."Portal.visitationSchedule", compact('beneficiary'));
    }
    
    /**
     * Get visitation events for the calendar
     */
    public function getEvents(Request $request)
    {
        // Add detailed session debug information
        \Log::info('Authentication check in getEvents', [
            'session_id' => session()->getId(),
            'session_active' => $request->hasSession() && session()->isStarted(),
            'session_token' => csrf_token(),
            'request_token' => $request->header('X-CSRF-TOKEN'),
            'beneficiary_auth' => Auth::guard('beneficiary')->check(),
            'family_auth' => Auth::guard('family')->check(),
            'cookie_exists' => $request->hasCookie(config('session.cookie')),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // Determine which guard is being used
        $guard = null;
        $user = null;

        if (Auth::guard('beneficiary')->check()) {
            $guard = 'beneficiary';
            $user = Auth::guard('beneficiary')->user();
        } elseif (Auth::guard('family')->check()) {
            $guard = 'family';
            $user = Auth::guard('family')->user();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Get beneficiary ID based on user type
        $beneficiaryId = null;
        if ($guard === 'beneficiary') {
            $beneficiaryId = $user->beneficiary_id;
        } else {
            // For family member, get the related beneficiary ID
            $beneficiaryId = $user->related_beneficiary_id;
        }
        
        $startDate = $request->input('start');
        $endDate = $request->input('end');
        $statusFilter = $request->input('status', 'all');
        $timeframeFilter = $request->input('timeframe', 'all');
        
        try {
            Log::info('Fetching visitation events', [
                'beneficiary_id' => $beneficiaryId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status_filter' => $statusFilter,
                'timeframe_filter' => $timeframeFilter
            ]);
            
            // THE CODE BELOW IS COMMENTED OUT OR MISSING
            // Build the query to get visitation occurrences
            $query = VisitationOccurrence::with(['visitation.beneficiary', 'visitation.careWorker'])
                ->whereHas('visitation', function($q) use ($beneficiaryId) {
                    $q->where('beneficiary_id', $beneficiaryId);
                })
                ->whereBetween('occurrence_date', [$startDate, $endDate]);
            
            // Apply status filter
            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
            
            // Apply timeframe filter
            $today = Carbon::today();
            if ($timeframeFilter === 'upcoming') {
                $query->where('occurrence_date', '>=', $today);
            } elseif ($timeframeFilter === 'past') {
                $query->where('occurrence_date', '<', $today);
            }
            
            $occurrences = $query->get(); // THIS IS LINE 98 - USING UNDEFINED $query
        
        Log::info('Found visitation occurrences', [
            'count' => $occurrences->count(),
            'beneficiary_id' => $beneficiaryId
                ]);
                
                // Format the occurrences for the calendar
                $events = [];
                foreach ($occurrences as $occurrence) {
                    $visitation = $occurrence->visitation;
                    
                    if (!$visitation) {
                        Log::warning('Occurrence has no visitation', ['occurrence_id' => $occurrence->occurrence_id]);
                        continue;
                    }
                    
                    // Format start time
                    $startTime = $occurrence->start_time ? 
                        Carbon::parse($occurrence->start_time)->format('H:i:s') : '08:00:00';
                    
                    // Format end time
                    $endTime = $occurrence->end_time ? 
                        Carbon::parse($occurrence->end_time)->format('H:i:s') : '09:00:00';
                    
                    // Format date
                    $date = $occurrence->occurrence_date;
                    
                    // Full event start and end date/time strings
                    $startDateTime = $date . 'T' . $startTime;
                    $endDateTime = $date . 'T' . $endTime;
                    
                    Log::info('Creating event', [
                        'occurrence_id' => $occurrence->occurrence_id,
                        'start' => $startDateTime,
                        'end' => $endDateTime
                    ]);
                    
                    // Get the color based on status
                    $color = $this->getStatusColor($occurrence->status);
                    
                    // Format the event
                    $events[] = [
                        'id' => $occurrence->occurrence_id,
                        'visitation_id' => $visitation->visitation_id,
                        'title' => $visitation->visit_type ? ucwords(str_replace('_', ' ', $visitation->visit_type)) : 'Visitation',
                        'start' => $startDateTime,
                        'end' => $endDateTime,
                        'status' => $occurrence->status,
                        'backgroundColor' => $color,
                        'borderColor' => $color,
                        'textColor' => '#fff',
                        'extendedProps' => [
                            'occurrence_id' => $occurrence->occurrence_id,
                            'careWorker' => $visitation->careWorker ? $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name : 'N/A',
                            'visitType' => $visitation->visit_type ? ucwords(str_replace('_', ' ', $visitation->visit_type)) : 'Standard Visit',
                            'notes' => $occurrence->notes ?? $visitation->notes ?? 'No notes available',
                            'is_flexible_time' => $visitation->is_flexible_time ?? false,
                        ]
                    ];
                }
                
                Log::info('Returning events for calendar', ['event_count' => count($events)]);
                
                return response()->json($events);
                
            } catch (\Exception $e) {
            Log::error('Error fetching visitation events', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching visitation events: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get occurrence details for display in the modal
     */
    public function getOccurrenceDetails($id)
    {
        $guard = null;
        $user = null;
        
        if (Auth::guard('beneficiary')->check()) {
            $guard = 'beneficiary';
            $user = Auth::guard('beneficiary')->user();
        } elseif (Auth::guard('family')->check()) {
            $guard = 'family';
            $user = Auth::guard('family')->user();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Get beneficiary ID based on user type
        $beneficiaryId = null;
        if ($guard === 'beneficiary') {
            $beneficiaryId = $user->beneficiary_id;
        } else {
            // For family member, get the related beneficiary ID
            $beneficiaryId = $user->related_beneficiary_id;
        }
        
        try {
            // Get the occurrence with related data
            $occurrence = VisitationOccurrence::with(['visitation.beneficiary', 'visitation.careWorker'])
                ->whereHas('visitation', function($q) use ($beneficiaryId) {
                    $q->where('beneficiary_id', $beneficiaryId);
                })
                ->where('occurrence_id', $id)
                ->first();
                
            if (!$occurrence) {
                return response()->json([
                    'success' => false,
                    'message' => 'Occurrence not found or you do not have permission to view it.'
                ], 404);
            }
            
            $visitation = $occurrence->visitation;
            
            // Format time string based on whether it's flexible or not
            $timeString = $visitation->is_flexible_time ? 
                'Flexible timing' : 
                Carbon::parse($visitation->start_time)->format('g:i A') . ' - ' . 
                Carbon::parse($visitation->end_time)->format('g:i A');
                
            // Format the occurrence details
            $details = [
                'occurrence_id' => $occurrence->occurrence_id,
                'visitation_id' => $visitation->visitation_id,
                'date' => Carbon::parse($occurrence->occurrence_date)->format('l, F j, Y'),
                'time' => $timeString,
                'status' => ucfirst($occurrence->status),
                'visit_type' => ucwords(str_replace('_', ' ', $visitation->visit_type)),
                'care_worker' => $visitation->careWorker ? 
                                $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name : 'Not assigned',
                'notes' => $occurrence->notes ?? $visitation->notes ?? 'No notes available',
                'beneficiary' => $visitation->beneficiary ? 
                                $visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name : 'N/A',
                'is_flexible_time' => $visitation->is_flexible_time,
            ];
            
            return response()->json([
                'success' => true,
                'details' => $details
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching occurrence details', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'occurrence_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching occurrence details: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get filtered upcoming visits for the sidebar
     */
    public function getUpcomingVisits(Request $request)
    {
        // Determine which guard is being used
        $guard = null;
        $user = null;
        
        if (Auth::guard('beneficiary')->check()) {
            $guard = 'beneficiary';
            $user = Auth::guard('beneficiary')->user();
        } elseif (Auth::guard('family')->check()) {
            $guard = 'family';
            $user = Auth::guard('family')->user();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Get beneficiary ID based on user type
        $beneficiaryId = null;
        if ($guard === 'beneficiary') {
            $beneficiaryId = $user->beneficiary_id;
        } else {
            // For family member, get the related beneficiary ID
            $beneficiaryId = $user->related_beneficiary_id;
        }
        
        try {
            $statusFilter = $request->input('status', 'all');
            $timeframeFilter = $request->input('timeframe', 'upcoming'); // Default to upcoming
            
            // Build the query
            $query = VisitationOccurrence::with(['visitation.beneficiary', 'visitation.careWorker'])
                ->whereHas('visitation', function($q) use ($beneficiaryId) {
                    $q->where('beneficiary_id', $beneficiaryId);
                });
            
            // Apply status filter
            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
            
            // Apply timeframe filter
            $today = Carbon::today();
            if ($timeframeFilter === 'upcoming') {
                $query->where('occurrence_date', '>=', $today)->orderBy('occurrence_date', 'asc');
            } elseif ($timeframeFilter === 'past') {
                $query->where('occurrence_date', '<', $today)->orderBy('occurrence_date', 'desc');
            } else {
                $query->orderBy('occurrence_date', 'desc');
            }
            
            // Get the visits
            $visits = $query->take(50)->get(); // Limit to 50 for performance
            
            Log::info('Found upcoming visits', [
                'count' => $visits->count()
            ]);
            
            // Format the visits for display
            $formattedVisits = [];
            foreach ($visits as $visit) {
                $visitation = $visit->visitation;
                
                // Skip if the parent visitation doesn't exist
                if (!$visitation) {
                    continue;
                }
                
                // Format time string based on whether it's flexible or not
                $timeString = $visitation->is_flexible_time ? 
                    'Flexible timing' : 
                    Carbon::parse($visitation->start_time)->format('g:i A') . ' - ' . 
                    Carbon::parse($visitation->end_time)->format('g:i A');
                
                $formattedVisits[] = [
                    'occurrence_id' => $visit->occurrence_id,
                    'date' => Carbon::parse($visit->occurrence_date)->format('D, M j, Y'),
                    'time' => $timeString,
                    'status' => $visit->status,
                    'status_label' => ucfirst($visit->status),
                    'visit_type' => ucwords(str_replace('_', ' ', $visitation->visit_type)),
                    'care_worker' => $visitation->careWorker ? 
                                    $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name : 'Not assigned',
                    'notes' => $visit->notes ?? $visitation->notes ?? 'No notes available'
                ];
            }
            
            return response()->json([
                'success' => true,
                'visits' => $formattedVisits
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching upcoming visits', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching upcoming visits: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper: Get color for status (for calendar).
     */
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'scheduled':
                return '#4e73df'; // blue
            case 'completed':
                return '#1cc88a'; // green
            case 'canceled':
                return '#e74a3b'; // red
            default:
                return '#6c757d'; // gray
        }
    }

    /**
     * Get the next upcoming visitation for display on the dashboard
     * 
     * @param int|null $beneficiaryId Optional beneficiary ID (for family members)
     * @return array|null Returns visitation details or null if none found
     */
    public function getNextVisit($beneficiaryId = null)
    {
        try {
            if (Auth::guard('beneficiary')->check()) {
                $beneficiaryId = Auth::guard('beneficiary')->id();
            } elseif (Auth::guard('family')->check() && !$beneficiaryId) {
                $familyMember = Auth::guard('family')->user();
                $beneficiaryId = $familyMember->related_beneficiary_id;
            }

            if (!$beneficiaryId) {
                return null;
            }
            
            // Get the next scheduled occurrence that hasn't happened yet
            $nextVisit = \App\Models\VisitationOccurrence::with(['visitation', 'visitation.careWorker'])
                ->whereHas('visitation', function($query) use ($beneficiaryId) {
                    $query->where('beneficiary_id', $beneficiaryId);
                })
                ->where('status', 'scheduled') // Only get scheduled visits
                ->where('occurrence_date', '>=', now()->format('Y-m-d')) // Only future visits
                ->orderBy('occurrence_date', 'asc') // Earliest first
                ->first();
            
            if (!$nextVisit) {
                return null;
            }
            
            // Get the parent visitation record
            $visitation = $nextVisit->visitation;
                
            // Format the return data
            return [
                'date' => \Carbon\Carbon::parse($nextVisit->occurrence_date)->format('F d, Y'),
                'time' => $visitation->is_flexible_time ? 
                    'Flexible timing' : 
                    \Carbon\Carbon::parse($visitation->start_time)->format('g:i A'),
                'care_worker' => $visitation->careWorker ? 
                    $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name :
                    'Not assigned',
                'visit_type' => ucwords(str_replace('_', ' ', $visitation->visit_type)),
                'is_flexible_time' => $visitation->is_flexible_time ?? false,
                'occurrence_id' => $nextVisit->occurrence_id
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting next visit: ' . $e->getMessage(), [
                'exception' => $e,
                'beneficiary_id' => $beneficiaryId
            ]);
            return null;
        }
    }
}