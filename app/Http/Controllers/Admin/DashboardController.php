<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Room;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();

        if ($user->hasRole('Admin')) {

            // Admin sees all rooms
            $totalPaths = Path::count();
            $totalRooms = Room::count();
            $totalStaffs = Staff::count();
            $totalAssignments = Staff::whereNotNull('room_id')->count();
        } else {
            // Office Manager (or any non-admin) sees only their assigned rooms
            $totalPaths = 0;
            $totalRooms = $user->room ? 1 : 0;
            $totalStaffs = Staff::where('room_id', $user->room_id)->count();
            $totalAssignments = Staff::where('room_id', $user->room_id)->count();
        }

        return view('pages.admin.dashboard', compact('totalPaths', 'totalRooms', 'totalStaffs', 'totalAssignments'));
    }
}
