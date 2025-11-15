<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,room_id',
            'name' => 'required|string|max:255',
            'condition' => [
                'required',
                Rule::in(['Good', 'Needs Repair', 'Broken', 'Missing'])
            ],
            'date_acquired' => 'nullable|date',
        ]);

        Asset::create($validatedData);

        return redirect()->back()->with('success', 'Asset added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'condition' => [
                'sometimes',
                'required',
                Rule::in(['Good', 'Needs Repair', 'Broken', 'Missing'])
            ],
            'date_acquired' => 'nullable|date',
        ]);

        $asset->update($validatedData);

        return redirect()->back()->with('success', 'Asset updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
