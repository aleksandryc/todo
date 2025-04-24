<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Tables;
use App\Models\Workshops;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $stats = [
            'total_orders' => Orders::count(),
            'total_tables' => Tables::count(),
            'active_workshops' => Workshops::with(['processes' => fn($query) => $query->where('status', 'in_progress')])->get()->count(),
        ];

        //queue in workshops
        $workshops = Workshops::query()
        ->with(['processes' => function($query) {
            $query->where('status', 'in_progress')
            ->with('tables');  // Load tables for each proccess
        }])->get();

        return Inertia::render('Name/Dashboard/Index', [
            'stats' => $stats,
            'workshops' =>$workshops,
        ]);
    }
}
