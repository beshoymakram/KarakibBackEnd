<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WasteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WasteItemController extends Controller
{
    public function index()
    {
        $wasteItems = WasteItem::with('wasteType')->get();

        return response()->json($wasteItems);
    }

    public function show($id)
    {
        $wasteItem = WasteItem::with('wasteType')->findOrFail($id);
        return response()->json($wasteItem);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'waste_type_id' => 'required|exists:waste_types,id',
            'name' => 'required|string|max:255',
            'points_per_unit' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,piece',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('waste-items', 'public');
            $data['image'] = $imagePath;
        }



        $wasteItem = WasteItem::create($data);

        return response()->json([
            'message' => 'Waste item created successfully',
            'data' => $wasteItem
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $wasteItem = WasteItem::findOrFail($id);

        $data = $request->validate([
            'waste_type_id' => 'exists:waste_types,id',
            'name' => 'string|max:255',
            'points_per_unit' => 'numeric|min:0',
            'unit' => 'in:kg,piece',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($wasteItem->image && Storage::disk('public')->exists($wasteItem->image)) {
                Storage::disk('public')->delete($wasteItem->image);
            }

            $imagePath = $request->file('image')->store('waste-items', 'public');
            $data['image'] = $imagePath;
        }

        $wasteItem->update($data);

        return response()->json([
            'message' => 'Waste item updated successfully',
            'data' => $wasteItem->load('wasteType')
        ]);
    }

    public function destroy($id)
    {
        $wasteItem = WasteItem::findOrFail($id);

        if ($wasteItem->image && Storage::disk('public')->exists($wasteItem->image)) {
            Storage::disk('public')->delete($wasteItem->image);
        }

        $wasteItem->delete();

        return response()->json([
            'message' => 'Waste item deleted successfully'
        ]);
    }
}
