<?php

namespace App\Http\Controllers;

use App\Models\RumahIbadah;
use Illuminate\Http\Request;

class IbadahController extends Controller
{
    public function index()
    {
        return response()->json(RumahIbadah::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|integer',
        ]);

        $ibadah = RumahIbadah::create($validated);
        return response()->json(['success' => true, 'data' => $ibadah]);
    }

    public function update(Request $request, $id)
    {
        $ibadah = RumahIbadah::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer',
        ]);

        $ibadah->update($validated);
        return response()->json(['success' => true, 'data' => $ibadah]);
    }

    public function destroy($id)
    {
        $ibadah = RumahIbadah::findOrFail($id);
        $ibadah->delete();
        return response()->json(['success' => true]);
    }
}
