<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PointController extends Controller
{
    public function convertPoints(Request $request)
    {
        $validated = $request->validate([
            'points' => 'required|numeric|min:19|max:' . $request->user()->points
        ]);

        try {
            $cash = $request->user()->convertPoints($validated['points']);
            return response()->json([
                'message' =>  __('messages.converted_successfully'),
                'cash_added' => $cash
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
