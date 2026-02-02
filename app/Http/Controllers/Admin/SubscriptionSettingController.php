<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionSetting;
use Illuminate\Http\Request;

class SubscriptionSettingController extends Controller
{
    public function edit()
    {
        $settings = SubscriptionSetting::instance();
        return view('admin.subscription.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'trial_duration_days' => ['required', 'integer', 'min:0'],
            'grace_period_days' => ['required', 'integer', 'min:0'],
            'enable_trial' => ['boolean'],
            'require_trial_verification' => ['boolean'],
            'auto_renew_default' => ['boolean'],
        ]);

        $settings = SubscriptionSetting::instance();
        
        $settings->update([
            'trial_duration_days' => $validated['trial_duration_days'],
            'grace_period_days' => $validated['grace_period_days'],
            'enable_trial' => $request->boolean('enable_trial'),
            'require_trial_verification' => $request->boolean('require_trial_verification'),
            'auto_renew_default' => $request->boolean('auto_renew_default'),
        ]);

        return redirect()->back()
            ->with('success', 'Pengaturan langganan berhasil diperbarui.');
    }
}
