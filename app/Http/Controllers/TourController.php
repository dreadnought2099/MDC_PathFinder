<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request) {
        $room = null;
        
        if ($request->has('room')) {
            $room = Room::find($request->room);
        }
        
        return view('pages.client.scan', compact('room'));
    }
}
