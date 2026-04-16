<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;

class TermAndConditionController extends Controller
{
    public function edit()
    {
        $terms = TermsAndCondition::first();

        return view('admin.terms.edit', compact('terms'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $terms = TermsAndCondition::first();
        if (! $terms) {
            $terms = new TermsAndCondition;
        }
        $terms->content = $request->content;
        $terms->save();

        return redirect()->back()->with('success', 'Syarat & Ketentuan berhasil diperbarui.');
    }
}
