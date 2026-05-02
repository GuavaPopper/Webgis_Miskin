<?php

namespace App\Http\Controllers;

use App\Models\RumahMiskin;
use Illuminate\Http\Request;

class MiskinController extends Controller
{
    public function index()
    {
        return response()->json(RumahMiskin::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_rumah' => 'required|string|unique:rumah_miskin,id_rumah',
            'alamat' => 'required|string',
            'jumlah_kk' => 'required|integer',
            'jumlah_orang' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $miskin = RumahMiskin::create($validated);
        return response()->json(['success' => true, 'data' => $miskin]);
    }

    public function update(Request $request, $id_rumah)
    {
        $miskin = RumahMiskin::findOrFail($id_rumah);
        $validated = $request->validate([
            'alamat' => 'required|string',
            'jumlah_kk' => 'required|integer',
            'jumlah_orang' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $miskin->update($validated);
        return response()->json(['success' => true, 'data' => $miskin]);
    }

    public function destroy($id_rumah)
    {
        $miskin = RumahMiskin::findOrFail($id_rumah);
        $miskin->delete();
        return response()->json(['success' => true]);
    }
}
