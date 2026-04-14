<?php
// app/Http/Controllers/RecipientController.php

namespace App\Http\Controllers;

use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipientController extends Controller
{
    public function index()
    {
        $recipients = Recipient::all();
        return response()->json($recipients);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'cod' => 'nullable|string|max:255',
            'item' => 'nullable|string|max:255',
            'payer' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $recipient = Recipient::updateOrCreate(
            ['phone' => $request->phone],
            $request->all()
        );

        return response()->json(['message' => 'Người nhận đã được lưu thành công!', 'recipient' => $recipient], 201);
    }

    public function update(Request $request, $id)
    {
        $recipient = Recipient::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'cod' => 'nullable|string|max:255',
            'item' => 'nullable|string|max:255',
            'payer' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $recipient->update($request->all());

        return response()->json(['message' => 'Người nhận đã được cập nhật thành công!', 'recipient' => $recipient], 200);
    }
}
