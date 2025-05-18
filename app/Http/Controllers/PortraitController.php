<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Portrait;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;




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
        'portrait' => 'required|array',
        'portrait.*' => 'required|image|max:30720',
        'price' => 'required|numeric|min:0',
    ]);

    foreach ($request->file('portrait') as $file) {
        $mime = $file->getMimeType();
        $extension = match (true) {
            str_contains($mime, 'jpeg') => 'jpg',
            str_contains($mime, 'png')  => 'jpg', // convert PNG to JPEG
            str_contains($mime, 'webp') => 'jpg', // convert to JPEG for universal support
            default => abort(415, 'Unsupported image type.'),
        };

        $filename = uniqid() . '.' . $extension;

        // Load original image
        $src = match ($extension) {
            'jpg' => imagecreatefromstring(file_get_contents($file->getPathname())),
        };

        // Resize (max width 1200)
        $originalWidth = imagesx($src);
        $originalHeight = imagesy($src);
        $newWidth = 1200;
        $newHeight = intval(($newWidth / $originalWidth) * $originalHeight);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Final save path (publicly accessible)
        $savePath = '/home1/artcardc/public_html/storage/portraits/' . $filename;
        imagejpeg($resized, $savePath, 75);

        // Clean up memory
        imagedestroy($src);
        imagedestroy($resized);

        // Save to database
        Portrait::create([
            'image_path' => 'storage/portraits/' . $filename,
            'price' => $request->price,
        ]);
    }

    return redirect()->route('dashboard')->with('success', 'Portraits uploaded successfully!');
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
