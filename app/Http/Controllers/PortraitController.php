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
            'portrait' => 'required|image|max:2048',
            'price' => 'required|numeric|min:0',
        ]);

        $path = $request->file('portrait')->store('portraits', 'public');

        Portrait::create([
            
            'image_path' => $path,
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
