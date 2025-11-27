<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeOutletController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|integer'
        ]);

        $user = Auth::user();

        // Validasi apakah user boleh akses outlet tsb
        if (! $user->canAccessOutlet($request->outlet_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke outlet ini.');
        }

        // Update outlet aktif user
        $user->update([
            'outlet_id' => $request->outlet_id
        ]);

        // Mengubah 'return back()' menjadi redirect ke route 'dashboard'
        return redirect()->route('dashboard')->with('success', 'Outlet berhasil diganti!');
    }
}
