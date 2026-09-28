<?php

namespace App\Http\Controllers;

use App\Models\Decor;
use App\Models\DecorOrder;

use Illuminate\Http\Request;

class DecorController extends Controller
{
    public function index()
    {
        $decors = Decor::latest()->paginate(50);

        return view('decor', [
            'decors' => $decors,
            'showDiscountBanner' => true
        ]);
    }

      public function dashboard()
    {
        $decors = Decor::latest()->get();
        return view('decor.dashboard', compact('decors'));
    }
    public function store(Request $request)
{
    $request->validate([
        'decor' => 'required|image|max:30720',
        'price' => 'required|numeric|min:0',
    ]);

    $file = $request->file('decor');
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

    Decor::create([
        'image_path' => 'portraits/' . $filename,
        'price' => $request->price,
    ]);

    return redirect()->route('admin.decors.dashboard')->with('success', 'decor uploaded!');
}

}
