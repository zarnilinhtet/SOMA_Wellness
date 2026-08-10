<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workshops = Workshop::all();
        return view('backends.workshop.workshop_index', compact('workshops'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workshop_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/workshop'), $imageName);
            $validated['image'] = 'uploads/workshop/' . $imageName;
        }

        Workshop::create($validated);

        return redirect()->back()->with('success', 'Workshop registered successfully.');
    }

    public function edit(Workshop $workshop)
    {
        return view('backends.workshop.edit', compact('workshop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'workshop_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle Image Replacement
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($workshop->image && File::exists(public_path($workshop->image))) {
                File::delete(public_path($workshop->image));
            }

            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/workshop'), $imageName);
            $validated['image'] = 'uploads/workshop/' . $imageName;
        }

        $workshop->update($validated);

        return redirect()->back()->with('success', 'Workshop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop)
    {
        // Delete image file when deleting workshop record
        if ($workshop->image && File::exists(public_path($workshop->image))) {
            File::delete(public_path($workshop->image));
        }

        $workshop->delete();
        return redirect()->back()->with('success', 'Workshop deleted successfully.');
    }
}