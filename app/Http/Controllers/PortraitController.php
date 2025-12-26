<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Portrait;
use Illuminate\Support\Facades\Auth;

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
        'portrait' => 'required|image|max:30720',
        'price' => 'required|numeric|min:0',
    ]);

    $file = $request->file('portrait');
    $mime = $file->getMimeType();

    // Generate unique filename
    $filename = uniqid() . '.jpg'; // or .webp

    // Load the image using native PHP
    $src = match (true) {
        str_contains($mime, 'jpeg') => imagecreatefromjpeg($file->getPathname()),
        str_contains($mime, 'png')  => imagecreatefrompng($file->getPathname()),
        str_contains($mime, 'webp') => imagecreatefromwebp($file->getPathname()),
        default => abort(415, 'Unsupported image type.'),
    };

    // Resize to 1200px width (keep aspect ratio)
    $originalWidth = imagesx($src);
    $originalHeight = imagesy($src);
    $newWidth = 1200;
    $newHeight = intval(($newWidth / $originalWidth) * $originalHeight);

    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    // Save resized image to final path
    $savePath = '/home1/artcardc/public_html/storage/portraits/' . $filename;
    imagejpeg($resized, $savePath, 75); // Save as JPEG (75% quality)

    // Clean up memory
    imagedestroy($src);
    imagedestroy($resized);

    // Save DB entry
    Portrait::create([
        'image_path' => 'portraits/' . $filename,
        'price' => $request->price,
    ]);

    return redirect()->route('admin.dashboard')->with('success', 'Portrait uploaded!');
}


 public function update(Request $request, Portrait $portrait)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $portrait->update([
            'price' => $request->price,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Portrait updated!');
    }

    public function destroy(Portrait $portrait)
    {
        Storage::disk('public')->delete($portrait->image_path);
        $portrait->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Portrait deleted!');
    }
}
