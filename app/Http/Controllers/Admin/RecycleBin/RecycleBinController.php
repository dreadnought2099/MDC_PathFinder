<?php

namespace App\Http\Controllers\Admin\RecycleBin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;

class RecycleBinController extends Controller
{
    public function index()
    {
        $rooms = Room::onlyTrashed()->get();
        $staffs = Staff::onlyTrashed()->get();
        $users = User::onlyTrashed()->get();

        return view('pages.admin.recycle-bin', compact('rooms', 'staffs', 'users'));
    }
}
