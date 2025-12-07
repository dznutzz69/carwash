<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json(Service::all());
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
        ]);

        $service = Service::create($data);

        return response()->json($service, 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $service = Service::findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price'       => 'sometimes|numeric|min:0',
        ]);

        $service->update($data);

        return response()->json($service);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
