<?php

namespace App\Http\Controllers;

use App\Models\Tables;
use App\Http\Requests\StoreTablesRequest;
use App\Http\Requests\UpdateTablesRequest;
use Inertia\Inertia;

class TablesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Tables::query()
        ->with('order')
        ->get();

        return Inertia::render('Name/Tables/Index', [
            'tables' => $tables
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
