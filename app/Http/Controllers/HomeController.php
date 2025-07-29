<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Building;

use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Load buildings with their latest aggregated progress data
        $buildings = Building::orderBy('name')
            ->get();

        return view('home', compact('buildings'));
    }
}
