<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index() {
         return view('ar.scan');
    }

    public function showAvailableRooms($markerId)
{
    // You can optionally filter rooms by marker proximity or logic here
    $rooms = \App\Models\Room::with('marker')->get();

    return view('ar.select-room', compact('rooms', 'markerId'));
}

}
