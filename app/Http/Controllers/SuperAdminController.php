<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Location;
use App\Models\ActivityLog;
use App\Models\BeachOwnerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationApproved;
use App\Mail\ApplicationRejected;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_admins' => User::whereIn('role', ['admin', 'beach_owner'])->count(),
            'active_admins' => User::whereIn('role', ['admin', 'beach_owner'])->where('is_active', true)->count(),
            'total_locations' => Location::count(),
            'pending_applications' => BeachOwnerApplication::where('status', 'pending')->count(),
            'total_activities' => ActivityLog::count(),
        ];

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentActivities'));
    }

    public function admins()
    {
        $admins = User::whereIn('role', ['admin', 'beach_owner'])
            ->latest()
            ->get();

        return view('superadmin.admins', compact('admins'));
    }

    public function applications()
    {
        $applications = BeachOwnerApplication::latest()
            ->paginate(20);

        $stats = [
            'pending' => BeachOwnerApplication::where('status', 'pending')->count(),
            'approved' => BeachOwnerApplication::where('status', 'approved')->count(),
            'rejected' => BeachOwnerApplication::where('status', 'rejected')->count(),
        ];

        return view('superadmin.applications', compact('applications', 'stats'));
    }

    public function approveApplication(Request $request, BeachOwnerApplication $application)
    {
        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'This application has already been reviewed');
        }

        // Generate PIN
        $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create beach owner user
        $user = User::create([
            'name' => $application->full_name,
            'email' => $application->email,
            'password' => Hash::make($pin),
            'pin' => $pin,
            'role' => 'beach_owner',
            'is_active' => true,
        ]);

        // Update application
        $application->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'user_id' => $user->id,
        ]);

        // Send approval email with PIN
        Mail::to($application->email)->send(new ApplicationApproved($application, $pin));

        ActivityLog::log('update', $application, "Approved beach owner application: {$application->full_name}");

        return redirect()->back()->with('success', "Application approved! Email sent to {$application->email} with PIN: {$pin}");
    }

    public function rejectApplication(Request $request, BeachOwnerApplication $application)
    {
        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'This application has already been reviewed');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        // Update application
        $application->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        // Send rejection email
        Mail::to($application->email)->send(new ApplicationRejected($application));

        ActivityLog::log('update', $application, "Rejected beach owner application: {$application->full_name}");

        return redirect()->back()->with('success', "Application rejected. Email sent to {$application->email}");
    }

    public function toggleAdminStatus(User $admin)
    {
        if ($admin->role === 'super_admin') {
            return redirect()->back()->with('error', 'Cannot modify super admin status');
        }

        $admin->update(['is_active' => !$admin->is_active]);

        $status = $admin->is_active ? 'activated' : 'deactivated';
        ActivityLog::log('update', $admin, "Beach owner {$status}: {$admin->name}");

        return redirect()->back()->with('success', "Beach owner {$status} successfully");
    }

    public function deleteAdmin(User $admin)
    {
        if ($admin->role === 'super_admin') {
            return redirect()->back()->with('error', 'Cannot delete super admin');
        }

        $name = $admin->name;
        $admin->delete();

        ActivityLog::log('delete', $admin, "Deleted beach owner: {$name}");

        return redirect()->back()->with('success', "Beach owner '{$name}' deleted successfully");
    }

    public function resetAdminPin(User $admin)
    {
        if ($admin->role === 'super_admin') {
            return redirect()->back()->with('error', 'Cannot reset super admin PIN');
        }

        $newPin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $admin->update([
            'pin' => $newPin,
            'password' => Hash::make($newPin),
        ]);

        ActivityLog::log('update', $admin, "Reset PIN for beach owner: {$admin->name}");

        return redirect()->back()->with('success', "PIN reset for '{$admin->name}'. New PIN: {$newPin}");
    }

    public function activityLogs()
    {
        $activities = ActivityLog::with('user')
            ->latest()
            ->paginate(50);

        return view('superadmin.activity_logs', compact('activities'));
    }

    public function adminDetails(User $admin)
    {
        $admin->load(['featureRequests', 'activities' => function ($query) {
            $query->latest()->take(20);
        }]);

        return view('superadmin.admin_details', compact('admin'));
    }

    public function users()
    {
        $users = User::where('role', 'user')
            ->latest()
            ->paginate(20);

        return view('superadmin.users', compact('users'));
    }

    public function weatherData()
    {
        $locations = Location::all();
        
        return view('superadmin.weather_data', compact('locations'));
    }
}
