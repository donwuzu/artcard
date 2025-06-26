<?php

namespace App\Http\Controllers;

use App\Models\PortraitClock;

use App\Models\ClockOrder;

use Illuminate\Http\Request;

class PortraitClockController extends Controller
{
    public function index()
    {
        $clocks = PortraitClock::latest()->paginate(50);

        return view('portraitClock', [
            'clocks' => $clocks,
            'showDiscountBanner' => true
        ]);
    }

      public function dashboard()
    {
        $clocks = PortraitClock::latest()->get();
        return view('clocksDashboard', compact('clocks'));
    }
    public function store(Request $request)
{
    $request->validate([
        'clock' => 'required|image|max:30720',
        'price' => 'required|numeric|min:0',
    ]);

    $file = $request->file('clock');
    $mime = $file->getMimeType();

    $filename = uniqid() . '.jpg';

    $src = match (true) {
        str_contains($mime, 'jpeg') => imagecreatefromjpeg($file->getPathname()),
        str_contains($mime, 'png')  => imagecreatefrompng($file->getPathname()),
        str_contains($mime, 'webp') => imagecreatefromwebp($file->getPathname()),
        default => abort(415, 'Unsupported image type.'),
    };

    $originalWidth = imagesx($src);
    $originalHeight = imagesy($src);
    $newWidth = 1200;
    $newHeight = intval(($newWidth / $originalWidth) * $originalHeight);

    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    $savePath = '/home1/artcardc/public_html/storage/portraits/' . $filename;
    imagejpeg($resized, $savePath, 75);

    imagedestroy($src);
    imagedestroy($resized);

    PortraitClock::create([
        'image_path' => 'portraits/' . $filename,
        'price' => $request->price,
    ]);

    return redirect()->route('clocksDashboard')->with('success', 'Clock uploaded!');
}

}
