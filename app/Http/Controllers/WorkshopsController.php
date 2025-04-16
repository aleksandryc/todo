<?php

namespace App\Http\Controllers;

use App\Models\Workshops;
use App\Http\Requests\StoreWorkshopsRequest;
use App\Http\Requests\UpdateWorkshopsRequest;
use Inertia\Inertia;

class WorkshopsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workshops = Workshops::query()
        ->with(['processes' => fn($query) => $query->where('status', 'in_progress')])->get();
        return Inertia::render('Name/Workshops/Index', [
            'workshops' => $workshops
        ]);
    }

    public function workerWorkshop()
    {
        // Need aditional logic
        $workshops = Workshops::query()
        ->where('name', 'painting')
        ->with(['processes' => fn($query) => $query->where('status', 'in_progress')])->first();
        return Inertia::render('Name/Worker/Workshop', [
            'workshops' => $workshops
        ]);
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
