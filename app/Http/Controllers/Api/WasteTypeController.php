<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WasteType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WasteTypeController extends Controller
{
    public function index()
    {
        $wasteTypes = WasteType::all();

        return response()->json($wasteTypes);
    }

    public function show($id)
    {
        $wasteType = WasteType::with('wasteItems')->findOrFail($id);

        return response()->json($wasteType);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('waste-types', 'public');
            $data['image'] = $imagePath;
        }

        $wasteType = WasteType::create($data);

        return response()->json([
            'message' => 'Waste type created successfully',
            'data' => $wasteType
        ], 201);
    }

    public function update(WasteType $wasteType, Request $request)
    {
        $data = $request->validate([
            'name' => 'string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($wasteType->image && Storage::disk('public')->exists($wasteType->image)) {
                Storage::disk('public')->delete($wasteType->image);
            }

            $imagePath = $request->file('image')->store('waste-types', 'public');
            $data['image'] = $imagePath;
        }

        $wasteType->update($data);

        return response()->json([
            'message' => 'Waste type updated successfully',
            'data' => $wasteType
        ]);
    }

    public function destroy($id)
    {
        $wasteType = WasteType::findOrFail($id);

        if ($wasteType->image && Storage::disk('public')->exists($wasteType->image)) {
            Storage::disk('public')->delete($wasteType->image);
        }

        $wasteType->delete();

        return response()->json([
            'message' => 'Waste type deleted successfully'
        ]);
    }
}
