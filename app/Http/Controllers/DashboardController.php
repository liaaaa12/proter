<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // If AJAX request, return only the inner content so the sidebar isn't duplicated
        if (request()->ajax() || request()->wantsJson()) {
            return view('dashboard._content');
        }

        // Full-page render (direct load)
        return view('dashboard');
    }
}