<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appeal;
use Illuminate\Http\Request;

class AppealsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'steamid' => 'nullable|string|max:255',
            'reason'  => 'required|string|max:5000',
        ]);

        Appeal::create($validated);

        return response()->json(['message' => 'Appeal submitted successfully.'], 201);
    }
}
