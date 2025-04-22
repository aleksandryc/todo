<?php

namespace App\Http\Controllers;

use App\Models\Workshops;
use App\Http\Requests\StoreWorkshopsRequest;
use App\Http\Requests\UpdateWorkshopsRequest;
use App\Models\Processes;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkshopsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function workerWorkshop(Request $request)
    {
        $user= $request->user();
        $workshops = Workshops::query()
                ->where('user_id', $user->id)
                ->firstOrFail();

        $processes = Processes::query()
            ->where('workshops_id', $workshops->id)
            ->where('status', 'in_progress')
            ->with(['tables' => fn($query) => $query->select('id', 'name', 'status', 'orders_id')])
            ->get()
            ->map(fn($process) => [
                'id' =>$process->id,
                'table' => [
                    'id' => $process->tables->id,
                    'name' => $process->tables->name,
                    'status' => $process->tables->status,
                    'orders_id' => $process->tables->orders_id,
                ],
                'status' => $process->status,
                ])
                ->filter()
                ->values()
                ->sortBy(function ($process) {

                    $statusorder = [
                        'pending' => 1,
                        'in_acceptance' => 2,
                        'in_painting'=> 3,
                        'in_assembly' => 4,
                        'in_delivery' => 5,
                        'completed' => 6,
                    ];
                    return $statusorder[$process['table']['status']] ?? 7;
                }
                )
                ->values();

        $activeProcessCount = $processes->count();
        $maxProcesses = 3;

        return Inertia::render('Name/Worker/Workshop', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'processLimit' => [
                'current' => $activeProcessCount,
                'max' => $maxProcesses,
            ],
            'processes' => $processes,
            'workshop' => $workshops->name,
        ]);
    }

    public function completeProcess(Request $request, $processId)
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'User not authorize');
        }
        try {
            $workshops = Workshops::query()->where('user_id', $user->id)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Workshop not found');
        }
        $process = Processes::query()->findOrFail($processId);
        if($process->workshops_id !== $workshops->id){
            abort(403, 'Not authorize');
        }
        if(!$process->tables){
            abort(404, "Tables not found");
        }

        $process->update(['status' => 'completed']);

        $tables = $process->tables;
        $nextStatus = match ($tables->status) {
            'pending' => 'in_acceptance',
            'in_acceptance' => 'in_painting',
            'in_painting' => 'in_assembly',
            'in_assembly' => 'in_delivery',
            'in_delivery' => 'completed',
            default => $tables->status,
        };
        $tables->update(['status' => $nextStatus]);

        return redirect()->route('worker.workshop')->with('message', 'Process completed successfully');
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
