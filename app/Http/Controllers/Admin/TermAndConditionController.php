<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermAndConditionController extends Controller
{
    public function edit()
    {
        $terms = \App\Models\TermsAndCondition::first();
        return view('admin.terms.edit', compact('terms'));
    }

    public function update(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $terms = \App\Models\TermsAndCondition::first();
        if (!$terms) {
            $terms = new \App\Models\TermsAndCondition();
        }
        $terms->content = $request->content;
        $terms->save();

        return redirect()->back()->with('success', 'Syarat & Ketentuan berhasil diperbarui.');
    }
}
