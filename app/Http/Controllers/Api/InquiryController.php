<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::all();

        return response()->json($inquiries);
    }

    public function show($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        return response()->json($inquiry);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $inquiry = Inquiry::create($data);

        return response()->json([
            'message' => __('messages.inquiry_created_successfully'),
            'data' => $inquiry
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $inquiry->update($data);

        return response()->json([
            'message' => __('messages.inquiry_updated_successfully'),
            'data' => $inquiry
        ]);
    }

    public function destroy($id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $inquiry->delete();

        return response()->json([
            'message' => __('messages.inquiry_deleted_successfully')
        ]);
    }
}
