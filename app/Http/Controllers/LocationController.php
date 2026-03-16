<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('created_at', 'desc')->get();

        $locationsJson = $locations->map(function ($l) {
            return [
                'id' => $l->id,
                'name' => $l->name,
                'lat' => (float) $l->latitude,
                'lng' => (float) $l->longitude,
                'image' => $l->image,
            ];
        })->toJson();

        return view('admin.locations', compact('locations', 'locationsJson'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'rating' => 'nullable|integer|min:0|max:5',
            'image_file' => 'nullable|image|max:5120',
            'address' => 'nullable|string|max:500',
            'fees' => 'nullable|string|max:500',
            'facilities' => 'nullable|string',
            'cottage' => 'nullable|string|max:500',
            'maps_embed_url' => 'nullable|string',
        ]);

        // Server-side: only allow locations within Philippines bounds
        $lat = (float) $data['latitude'];
        $lng = (float) $data['longitude'];
        $inPhilippines = ($lat >= 4 && $lat <= 21 && $lng >= 115 && $lng <= 130);
        if (! $inPhilippines) {
            return redirect()->back()->withInput()->withErrors(['latitude' => 'Location must be within the Philippines bounds.']);
        }

        // Handle uploaded image file if provided
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('locations', 'public');
            // store as storage path for asset() to resolve
            $data['image'] = 'storage/' . $path;
        } else {
            $data['image'] = null;
        }

        Location::create($data);

        return redirect()->back()->with('success', 'Location added');
    }

    public function apiIndex()
    {
        $locations = Location::orderBy('created_at', 'desc')->get();
        return response()->json($locations->map(function ($l) {
            $img = $l->image;
            if ($img && str_starts_with($img, 'storage/')) {
                $img = asset($img);
            }

            return [
                'id' => $l->id,
                'name' => $l->name,
                'description' => $l->description,
                'lat' => (float) $l->latitude,
                'lng' => (float) $l->longitude,
                'rating' => $l->rating,
                'image' => $img,
                'address' => $l->address,
                'fees' => $l->fees,
                'facilities' => $l->facilities,
                'cottage' => $l->cottage,
                'maps_embed_url' => $l->maps_embed_url,
            ];
        }));
    }

    public function edit(Location $location)
    {
        $locations = Location::orderBy('created_at', 'desc')->get();
        return view('admin.edit_location', compact('location', 'locations'));
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'rating' => 'nullable|integer|min:0|max:5',
            'image_file' => 'nullable|image|max:5120',
            'address' => 'nullable|string|max:500',
            'fees' => 'nullable|string|max:500',
            'facilities' => 'nullable|string',
            'cottage' => 'nullable|string|max:500',
            'maps_embed_url' => 'nullable|string',
        ]);

        // Server-side: only allow locations within Philippines bounds
        $lat = (float) $data['latitude'];
        $lng = (float) $data['longitude'];
        $inPhilippines = ($lat >= 4 && $lat <= 21 && $lng >= 115 && $lng <= 130);
        if (! $inPhilippines) {
            return redirect()->back()->withInput()->withErrors(['latitude' => 'Location must be within the Philippines bounds.']);
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('locations', 'public');
            $data['image'] = 'storage/' . $path;
            // Optionally delete old image file - skipping for now
        } else {
            // keep existing image if none uploaded
            $data['image'] = $location->image;
        }

        $location->update($data);

        return redirect('/admin/locations')->with('success', 'Location updated');
    }

    public function destroy(Location $location)
    {
        // Optionally delete stored image file
        $location->delete();
        return redirect('/admin/locations')->with('success', 'Location deleted');
    }

    public function apiShow($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        $img = $location->image;
        if ($img && str_starts_with($img, 'storage/')) {
            $img = asset($img);
        }

        $tourImages = $location->tour_images ?? [];
        $tourImages = array_map(function($image) {
            if (str_starts_with($image, 'storage/')) {
                return asset($image);
            }
            return $image;
        }, $tourImages);

        return response()->json([
            'id' => $location->id,
            'name' => $location->name,
            'description' => $location->description,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'rating' => $location->rating,
            'image' => $img,
            'address' => $location->address,
            'fees' => $location->fees,
            'facilities' => $location->facilities,
            'cottage' => $location->cottage,
            'maps_embed_url' => $location->maps_embed_url,
            'tour_images' => $tourImages,
            'tour_type' => $location->tour_type,
            'tour_video_url' => $location->tour_video_url,
            'lat' => (float) $location->latitude,
            'lng' => (float) $location->longitude,
        ]);
    }
}
