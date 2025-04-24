<?php

namespace App\Http\Controllers;

use App\Models\Workshops;
use App\Http\Requests\StoreWorkshopsRequest;
use App\Http\Requests\UpdateWorkshopsRequest;
use App\Models\Processes;
use App\Models\Tables;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkshopsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $workshops = Workshops::query()
            ->with(['processes.tables'])
            ->where('user_id', $user->id)
            ->get();

        $data = $workshops->map(function ($workshop) {
            return [
                'workshop_name' => $workshop->name,
                'workshop_id' => $workshop->id,
                'processes' => $workshop->processes->where('status', '!=', 'completed')->map(function ($process) {
                    return [
                        'process_status' => $process->status,
                        'tables' => $process->tables
                    ];
                })
            ];
        });
        return Inertia::render('Name/Worker/Index', [
            'workshops' =>  $data,
        ]);
    }

    public function completeProcess(Request $request, $processId, $shopId)
    {
        $paint = Processes::query()->where('workshops_id', 2)->get()->count();
        $assembly = Processes::query()->where('workshops_id', 3)->get()->count();
        $nextShop = $shopId + 1;

        if ($nextShop === 2 && $paint >= 3 || $nextShop === 3 && $assembly >= 3){
            return redirect()->route('worker.index')->with('message', 'Workshop is full!');
        }

        $tables = Processes::query()->with('Tables')->where('table_id', $processId)->get();
        foreach ($tables as $table) {
            if ($table->workshops_id === 4 && $table->tables->status === 'in_delivery') {
                $table->update(['status' => 'completed']);
                $table->tables->update(['status' => 'completed']);
            } else {
                $nextWorkshop = match ($table->workshops_id) {
                    1 => 2,
                    2 => 3,
                    3 => 4,
                    default => $table->select('workshops_id')
                };
                $nextStatus = match ($table->tables->status) {
                    'in_acceptance' => 'in_painting',
                    'in_painting' => 'in_assembly',
                    'in_assembly' => 'in_delivery',
                    'in_delivery' => 'completed',
                    default => $table->tables->status,
                };
                $table->tables->update(['status' => $nextStatus]);
                $table->update(['workshops_id' => $nextWorkshop]);
            }
        }
        return redirect()->route('worker.index')->with('message', 'Process completed successfully');
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
    public function store(StoreWorkshopsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Workshops $workshops)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workshops $workshops)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkshopsRequest $request, Workshops $workshops)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshops $workshops)
    {
        //
    }
}
