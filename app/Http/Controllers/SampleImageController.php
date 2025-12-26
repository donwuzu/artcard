<?php

namespace App\Http\Controllers;

use App\Models\SampleImage;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class SampleImageController extends Controller
{

    public function index()
{
    $sampleImages = SampleImage::latest()->paginate(50);

    return view('sample-images.index', compact('sampleImages'));
}

    public function dashboard()
    {
        $sampleImages = SampleImage::latest()->get();
        return view('admin.sample-images.dashboard', compact('sampleImages'));
    }

   public function store(Request $request)
{
    $request->validate([
        'image' => 'required|image|max:30720',
        'price' => 'required|numeric|min:0',
    ]);

    $file = $request->file('image');
    $mime = $file->getMimeType();
    $filename = uniqid() . '.jpg';

    // Create image resource
    $src = match (true) {
        str_contains($mime, 'jpeg') => imagecreatefromjpeg($file->getPathname()),
        str_contains($mime, 'png')  => imagecreatefrompng($file->getPathname()),
        str_contains($mime, 'webp') => imagecreatefromwebp($file->getPathname()),
        default => abort(415, 'Unsupported image format'),
    };

    // Resize (1200px width)
    $w = imagesx($src);
    $h = imagesy($src);
    $newW = 1200;
    $newH = intval(($newW / $w) * $h);

    $resized = imagecreatetruecolor($newW, $newH);
    imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

    // ✅ cPanel absolute path (CHANGE DOMAIN USERNAME IF NEEDED)
    $absoluteDir = '/home1/artcardc/public_html/storage/sample_images';

    if (!is_dir($absoluteDir)) {
        mkdir($absoluteDir, 0755, true);
    }

    $absolutePath = $absoluteDir . '/' . $filename;

    imagejpeg($resized, $absolutePath, 75);

    imagedestroy($src);
    imagedestroy($resized);

    // Save relative path in DB
    SampleImage::create([
        'image_path' => 'sample_images/' . $filename,
        'price' => $request->price,
    ]);

    return redirect()
        ->route('admin.sample-images.dashboard')
        ->with('success', 'Sample image uploaded');
}



    public function destroy(SampleImage $sampleImage)
    {
        Storage::disk('public')->delete($sampleImage->image_path);
        $sampleImage->delete();

        return back()->with('success', 'Sample image deleted');
    }
}
