<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Processes;
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
            'active_workshops' => Processes::query()->select('workshops_id')->distinct()->pluck('workshops_id')->count(),
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
