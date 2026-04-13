<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class DebtSettingController extends Controller
{
    public function index()
    {
        $lateFeePercentage = Setting::getValue('debt', 'late_fee_percentage', 5);
        return view('admin.debt.settings', compact('lateFeePercentage'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'late_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        Setting::setValue('debt', 'late_fee_percentage', $request->late_fee_percentage, 'float');

        return redirect()->back()->with('success', 'Pengaturan denda jatuh tempo berhasil diperbarui.');
    }
}
