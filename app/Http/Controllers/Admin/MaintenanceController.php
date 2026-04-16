<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendMaintenanceBroadcast;
use App\Models\BroadcastMessage;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class MaintenanceController extends Controller
{
    public function index()
    {
        // Get active users (online based on last_seen_at)
        $onlineThreshold = now()->subMinutes(5);

        $activeUsers = User::role('owner') // role selain admin
            ->where('last_seen_at', '>=', $onlineThreshold)
            ->with('roles')
            ->orderBy('last_seen_at', 'desc')
            ->get()
            ->map(function ($user) {
                // Attach session info if database session driver is used
                $session = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->orderBy('last_activity', 'desc')
                    ->first();

                $user->session_id = $session?->id;
                $user->ip_address = $session?->ip_address;

                return $user;
            });

        $maintenance = Maintenance::where('is_active', true)->first();

        return view('admin.maintenance.index', compact('activeUsers', 'maintenance'));
    }

    public function toggle(Request $request)
    {
        $activeMaintenance = Maintenance::where('is_active', true)->first();

        if ($activeMaintenance) {
            $activeMaintenance->update([
                'is_active' => false,
                'ended_at' => now(),
            ]);

            return back()->with('success', 'Maintenance mode dimatikan.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'reason' => 'nullable|string',
        ]);

        Maintenance::create([
            'admin_id' => auth()->id(),
            'title' => $request->title,
            'reason' => $request->reason,
            'is_active' => true,
            'started_at' => now(),
        ]);

        return back()->with('success', 'Maintenance mode diaktifkan.');
    }

    public function history()
    {
        $history = Maintenance::with('admin')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.maintenance.history', compact('history'));
    }

    public function broadcast()
    {
        $owners = User::role('owner')->get();
        $broadcasts = BroadcastMessage::with('admin')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.maintenance.broadcast', compact('owners', 'broadcasts'));
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'type' => 'required|in:maintenance_alert,custom_broadcast',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'target' => 'required|in:all_owners,specific_users',
            'user_ids' => 'required_if:target,specific_users|array',
        ]);

        $users = [];
        if ($request->target === 'all_owners') {
            $users = User::role('owner')->get();
        } else {
            $users = User::whereIn('id', $request->user_ids)->get();
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada user yang dipilih.');
        }

        $broadcast = BroadcastMessage::create([
            'admin_id' => auth()->id(),
            'type' => $request->type,
            'subject' => $request->subject,
            'content' => $request->content,
            'target_role' => $request->target === 'all_owners' ? 'owner' : null,
            'target_user_ids' => $request->target === 'specific_users' ? $request->user_ids : null,
            'total_recipients' => $users->count(),
        ]);

        // Dispatch Job to Queue
        foreach ($users as $user) {
            dispatch(new SendMaintenanceBroadcast($user, $broadcast));
        }

        return back()->with('success', "Pesan sedang dikirim ke {$users->count()} pengguna.");
    }

    public function terminateSession($sessionId)
    {
        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Session user berhasil di-terminate.');
    }
}
