<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrialVerificationRequest;
use Illuminate\Http\Request;

class TrialVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = TrialVerificationRequest::with('user')
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.subscription.trial-requests.index', compact('requests', 'status'));
    }

    public function show(TrialVerificationRequest $trialRequest)
    {
        $trialRequest->load('user');

        return view('admin.subscription.trial-requests.show', compact('trialRequest'));
    }

    public function approve(Request $request, TrialVerificationRequest $trialRequest)
    {
        if (! $trialRequest->isPending()) {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $trialRequest->approve(auth()->user(), $request->notes);

        // Start trial for user
        $user = $trialRequest->user;

        // Get limits of finding subscription (don't use $user->subscription relationship as it filters active status)
        $latestSubscription = $user->subscriptions()->latest()->first();

        // If user has any subscription, activate it as trial (updates existing data)
        if ($latestSubscription) {
            $latestSubscription->startTrial(
                \App\Models\SubscriptionSetting::getTrialDays()
            );
        } else {
            // Create new trial if absolutely no subscription exists (fallback)
            $user->subscriptions()->create([
                'tier_id' => 1, // Default to lowest tier
                'status' => 'trial',
                'is_trial' => true,
                'started_at' => now(),
                'trial_ends_at' => now()->addDays(\App\Models\SubscriptionSetting::getTrialDays()),
            ]);
        }

        // Clear cache to ensure immediate effect
        $user->clearSubscriptionCache();

        return redirect()->route('admin.subscription-trial-requests.index')
            ->with('success', 'Permintaan trial disetujui. Email notifikasi akan dikirim ke user.');
    }

    public function reject(Request $request, TrialVerificationRequest $trialRequest)
    {
        if (! $trialRequest->isPending()) {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $trialRequest->reject(auth()->user(), $request->reason);

        // Cancel pending subscription if exists
        $latestSubscription = $trialRequest->user->subscriptions()->latest()->first();

        if ($latestSubscription && $latestSubscription->isPendingVerification()) {
            $latestSubscription->cancel();
        }

        return redirect()->route('admin.subscription-trial-requests.index')
            ->with('success', 'Permintaan trial ditolak.');
    }
}
