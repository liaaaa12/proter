<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // sementara: kirim ke view "dashboard"
        return view('dashboard');
    }
}