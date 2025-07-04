<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Services\UploadService;

class ViewAccountProfileController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index()
    {
        // Get the authenticated user with their organization role
        $user = Auth::user()->load('organizationRole');
        
        // Format the user's birthday (if it exists)
        $formattedBirthday = null;
        if ($user->birthday) {
            $formattedBirthday = Carbon::parse($user->birthday)->format('F d, Y');
        }
        
        // Format the account creation date
        $memberSince = Carbon::parse($user->created_at)->format('M Y');

        // Get temporary photo URL if photo path exists and file exists in Spaces
        $photoUrl = null;
        if ($user->photo) {
            try {
                $photoUrl = $this->uploadService->getTemporaryPrivateUrl($user->photo, 30);
            } catch (\Exception $e) {
                $photoUrl = null;
            }
        }
        
        return view('admin.adminViewProfile', compact('user', 'formattedBirthday', 'memberSince', 'photoUrl'));
    }
    
    public function settings()
    {
        // Redirect to the settings section of the profile page
        return redirect()->route('admin.account.profile.index')->with('activeTab', 'settings');
    }

    public function careManagerIndex()
    {
        // Get the authenticated user with their organization role
        $user = Auth::user()->load('organizationRole');
        
        // Format the user's birthday (if it exists)
        $formattedBirthday = null;
        if ($user->birthday) {
            $formattedBirthday = Carbon::parse($user->birthday)->format('F d, Y');
        }
        
        // Format the account creation date
        $memberSince = Carbon::parse($user->created_at)->format('M Y');
        // Format the last login date if available
        $lastLogin = $user->last_login ? Carbon::parse($user->last_login)->format('M d, Y \a\t h:i A') : 'Not available';

        $photoUrl = null;
        if ($user->photo) {
            try {
                $photoUrl = $this->uploadService->getTemporaryPrivateUrl($user->photo, 30);
            } catch (\Exception $e) {
                $photoUrl = null;
            }
        }
        
        return view('careManager.managerViewProfile', compact('user', 'formattedBirthday', 'memberSince', 'lastLogin', 'photoUrl'));
    }

    public function careManagerSettings()
    {
        // Redirect to the settings section of the profile page
        return redirect()->route('care-manager.account.profile.index')->with('activeTab', 'settings');
    }

    public function careWorkerIndex()
    {
        // Get the authenticated user with their organization role
        $user = Auth::user()->load(['organizationRole', 'municipality', 'assignedCareManager']);
        
        // Format the user's birthday (if it exists)
        $formattedBirthday = null;
        if ($user->birthday) {
            $formattedBirthday = Carbon::parse($user->birthday)->format('F d, Y');
        }
        
        // Format the account creation date
        $memberSince = Carbon::parse($user->created_at)->format('M Y');
        // Format the last login date if available
        $lastLogin = $user->last_login ? Carbon::parse($user->last_login)->format('M d, Y \a\t h:i A') : 'Not available';

        $photoUrl = null;
        if ($user->photo) {
            try {
                $photoUrl = $this->uploadService->getTemporaryPrivateUrl($user->photo, 30);
            } catch (\Exception $e) {
                $photoUrl = null;
            }
        }
        
        return view('careWorker.workerViewProfile', compact('user', 'formattedBirthday', 'memberSince', 'lastLogin', 'photoUrl'));
    }

    public function careWorkerSettings()
    {
        // Redirect to the settings section of the profile page
        return redirect()->route('care-worker.account.profile.index')->with('activeTab', 'settings');
    }
}