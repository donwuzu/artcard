<?php

namespace App\Http\Controllers;

use App\Models\SampleDecor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SampleDecorController extends Controller
{
    /**
     * Public guest page
     */
    public function index()
    {
        $sampleDecors = SampleDecor::latest()->paginate(50);

        return view('sample-clocks.index', compact('sampleClocks'));
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        $sampleClocks = SampleDecor::latest()->get();

        return view('admin.sample-clocks.dashboard', compact('sampleClocks'));
    }

    /**
     * Store clock image
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:30720',
            // 'price' => 'required|numeric|min:0',
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

        // ✅ cPanel absolute path (UPDATE USERNAME IF NEEDED)
        $absoluteDir = '/home1/artcardc/public_html/storage/sample_clocks';

        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0755, true);
        }

        $absolutePath = $absoluteDir . '/' . $filename;

        imagejpeg($resized, $absolutePath, 75);

        imagedestroy($src);
        imagedestroy($resized);

        // Save relative path
        SampleDecor::create([
            'image_path' => 'sample_clocks/' . $filename,
            // 'price' => $request->price,
        ]);

        return redirect()
            ->route('admin.sample-clocks.dashboard')
            ->with('success', 'Sample clock uploaded');
    }

    /**
     * Delete clock
     */
    public function destroy(SampleDecor $sampleDecor)
    {
        Storage::disk('public')->delete($sampleDecor->image_path);
        $sampleDecor->delete();

        return back()->with('success', 'Sample decor deleted');
    }
}
