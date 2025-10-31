<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactRequestController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'girl_anketa_id' => 'nullable|string',
            'girl_name' => 'nullable|string',
            'girl_phone' => 'nullable|string',
            'girl_url' => 'nullable|string',
            'page_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        ContactRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'girl_anketa_id' => $request->girl_anketa_id,
            'girl_name' => $request->girl_name,
            'girl_phone' => $request->girl_phone,
            'girl_url' => $request->girl_url,
            'page_url' => $request->page_url ?? $request->header('referer'),
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ваша заявка успешно отправлена!'
        ]);
    }
}
