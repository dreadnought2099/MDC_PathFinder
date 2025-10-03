<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $paths = Path::all();
        $user  = Auth::user();

        if ($user->hasRole('Admin')) {
            // Admin sees all rooms
            $totalRooms = Room::count();
        } else {
            // Office Manager (or any non-admin) sees only their assigned room
            $totalRooms = $user->room ? 1 : 0;
        }

        return view('pages.admin.dashboard', compact('paths', 'totalRooms'));
    }
}
