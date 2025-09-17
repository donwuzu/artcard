<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
     public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('user_type', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'user_type' => 'required|string|max:50',
        ]);

        $user->update([
            'user_type' => $request->input('user_type'),
        ]);

        return redirect()->route('users.index')->with('success', 'User type updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
