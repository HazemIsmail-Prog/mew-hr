<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if(auth()->user()->role === 'admin') {
            return redirect()->route('users.index');
        }
        if(auth()->user()->role === 'supervisor') {
            return redirect()->route('requests.missions');
        }
        if(auth()->user()->role === 'employee') {
            return redirect()->route('missions.index');
        }
        return view('dashboard');
    }
}
