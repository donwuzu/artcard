<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Portrait;


class AdminDashboardController extends Controller
{
      public function index()
    {
        $portraits = Portrait::latest()->get();
        return view('admin.dashboard', compact('portraits'));
    }
}
