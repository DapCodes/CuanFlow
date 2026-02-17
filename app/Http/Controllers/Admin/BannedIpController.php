<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedIp;
use Illuminate\Http\Request;

class BannedIpController extends Controller
{
    public function index()
    {
        $bannedIps = BannedIp::latest()->paginate(20);

        return view('admin.security.banned-ips.index', compact('bannedIps'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => ['required', 'string', 'max:45'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        // Avoid duplicate bans
        $existing = BannedIp::where('ip_address', $validated['ip_address'])->first();

        if ($existing) {
            return redirect()->back()->with('error', 'IP ini sudah diblokir sebelumnya.');
        }

        BannedIp::create($validated);
        BannedIp::clearCache($validated['ip_address']);

        return redirect()->back()->with('success', "IP {$validated['ip_address']} berhasil diblokir.");
    }

    public function destroy(BannedIp $bannedIp)
    {
        $ip = $bannedIp->ip_address;
        $bannedIp->delete();
        BannedIp::clearCache($ip);

        return redirect()->route('admin.security.banned-ips.index')
            ->with('success', "IP {$ip} berhasil dihapus dari daftar blokir.");
    }
}
