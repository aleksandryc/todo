<?php

namespace App\Http\Controllers;

use App\Models\Tables;
use App\Http\Requests\StoreTablesRequest;
use App\Http\Requests\UpdateTablesRequest;
use App\Models\Processes;
use App\Models\Workshops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FRequest;
use Inertia\Inertia;

class TablesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = FRequest::only(['status', 'material', 'color']);

        $query = Tables::query()
        ->with('order')
        ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
        ->when($filters['material'] ?? null, fn($query, $material) => $query->where('material', $material))
        ->when($filters['color'] ?? null, fn($query, $color) => $query->where('color', 'like', "%$color%"));


        $tables = $query->paginate(10)->withQueryString();


        return Inertia::render('Name/Tables/Index', [
            'tables' => $tables,
            'filters' => $filters,
        ]);
    }

    public function udateStatus (Request $request, Tables $tables)
    {
        $request->validate([
            'status' => 'required|in:pending,in_acceptance,in_painting,in_assembly,in_delivery,completed  '
        ]);

        $newStaatus = $request->status;

        // Define workshop based on status
        $workshopName = match ($newStaatus) {
            'in_acceptance' => 'acceptance',
            'in_painting' => 'painting',
            'in_assembly' => 'assembly',
            'in_delivery' => 'delivery',
            default => null,
        };

        // Update table status
        $tables->update(['status' => $newStaatus]);

        if ($workshopName) {
            //Finished actual process
            Processes::query()
            ->where('tables_id', $tables->id)
            ->where('status', 'in_progress')
            ->update(['status' => 'completed']);

            // Check the limits
            $workshop = Workshops::query()->where('name', $workshopName)->first();
            $activeProcesses = Processes::query()
            ->where('workshops_id', $workshop->id)
            ->where('status', 'in_progress')
            ->count();

            if ($activeProcesses >= 3) {
                return back()->withErrors(['status' => 'Workshop ' . $workshopName . 'allready have maximum tables in work']);
            }

            // Create new process
            Processes::query()
            ->create([
                'tables_id' => $tables->id,
                'workshops_id' => $workshop->id,
                'status' => 'in_progress',
            ]);
        }
        return redirect()->route('dashboard')->with('success', 'Table status updated');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTablesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tables $tables)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tables $tables)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTablesRequest $request, Tables $tables)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tables $tables)
    {
        //
    }
}
