<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        $paths = Path::all();
        return view('pages.admin.dashboard', compact('paths'));
    }
}
