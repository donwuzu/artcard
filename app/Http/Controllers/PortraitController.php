<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Portrait;
use Illuminate\Support\Facades\Auth;



use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;






class PortraitController extends Controller
{
    public function index()
    {
        $portraits = Portrait::latest()->get();
        return view('dashboard', compact('portraits'));
    }

public function store(Request $request)
{
    $request->validate([
        'portrait' => 'required|image|max:30720', // up to 30MB
        'price' => 'required|numeric|min:0',
    ]);

    $file = $request->file('portrait');
    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

    // Resize + compress image using Intervention
    $image = Image::make($file)
        ->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();    // maintain aspect
            $constraint->upsize();         // no upscaling
        })
        ->encode($file->getClientOriginalExtension(), 75); // 75% quality

    // Save to public_html storage
    Storage::disk('public_html_disk')->put($filename, $image);

    Portrait::create([
        'image_path' => 'portraits/' . $filename,
        'price' => $request->price,
    ]);

    return redirect()->route('dashboard')->with('success', 'Portrait uploaded!');
}



 public function update(Request $request, Portrait $portrait)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $portrait->update([
            'price' => $request->price,
        ]);

        return redirect()->route('dashboard')->with('success', 'Portrait updated!');
    }

    public function destroy(Portrait $portrait)
    {
        Storage::disk('public')->delete($portrait->image_path);
        $portrait->delete();

        return redirect()->route('dashboard')->with('success', 'Portrait deleted!');
    }
}
