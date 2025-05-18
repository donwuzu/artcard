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
                'portrait' => 'required|array',
                'portrait.*' => 'required|image|max:30720',
                'price' => 'required|numeric|min:0',
            ]);

            foreach ($request->file('portrait') as $file) {
                $mime = $file->getMimeType();
                $filename = uniqid() . '.jpg';

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
                imagejpeg($resized, $savePath, 75);

                // Clean up
                imagedestroy($src);
                imagedestroy($resized);

                // Save DB record
                Portrait::create([
                    'image_path' => 'storage/portraits/' . $filename,
                    'price' => $request->price,
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Portraits uploaded!');
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
