<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->get();
        return view("backends.gallery.gallery_index", compact('galleries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'title' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('uploads/gallery'), $imageName);
            $imagePath = 'uploads/gallery/' . $imageName;

            Gallery::create([
                'image' => $imagePath,
                'title' => $request->input('title', ''), // Set title to empty string if not provided
            ]);
        }

        return redirect()->back()->with('success', 'Image uploaded successfully.');
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'title' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image from server
            if ($gallery->image && file_exists(public_path($gallery->image))) {
                unlink(public_path($gallery->image));
            }

            // Upload new image
            $file = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('uploads/gallery'), $imageName);

            $gallery->image = 'uploads/gallery/' . $imageName;
        }

        $gallery->title = $request->input('title', $gallery->title);
        $gallery->save();

        return redirect()->back()->with('success', 'Image updated successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        // Delete image file from server
        if ($gallery->image && file_exists(public_path($gallery->image))) {
            unlink(public_path($gallery->image));
        }

        $gallery->delete();

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }
}