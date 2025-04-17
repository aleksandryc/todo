<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as FRequest;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $filters = FRequest::only(['status', 'client', 'id']);

        $query = Orders::query()
        ->with(['client', 'tables'])
        ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
        ->when($filters['client'] ?? null, fn($query, $client) => $query->whereHas('client', fn($q) => $q->where('name', 'like', "%$client%")->orWhere('email', 'like', "%$client%")))
        ->when($filters['id'] ?? null, fn($query, $id) => $query->where('id', $id));


        $orders = $query->paginate(10)->withQueryString();


        return Inertia::render('Name/Orders/Index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    public function clientOrders()
    {

        $orders = Orders::query()
        ->where('client_id', auth('id'))
        ->with('tables')
        ->get();


        return Inertia::render('Name/Clients/', [
            'orders' => $orders,

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
    public function store(StoreOrdersRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Orders $orders)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrdersRequest $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orders $orders)
    {
        //
    }
}
