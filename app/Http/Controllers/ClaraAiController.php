<?php

namespace App\Http\Controllers;

use App\Models\AiChatSession;
use App\Services\ClaraAiService;
use Illuminate\Http\Request;

class ClaraAiController extends Controller
{
    protected $claraAi;

    public function __construct(ClaraAiService $claraAi)
    {
        $this->claraAi = $claraAi;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua session user (untuk sidebar riwayat)
        $sessions = AiChatSession::where('user_id', $user->id)
            ->where('outlet_id', $user->outlet_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $session = null;

        // Kalau ada session_id di query, pakai itu
        if ($request->filled('session_id')) {
            $session = $sessions->firstWhere('id', $request->session_id);
        }

        // Kalau tidak ada / tidak ketemu, pakai session terbaru
        if (!$session) {
            $session = $sessions->first();
        }

        // Kalau sama sekali belum punya session, buat baru
        if (!$session) {
            $session = AiChatSession::create([
                'user_id'   => $user->id,
                'outlet_id' => $user->outlet_id,
                'title'     => 'Chat dengan Clara AI',
            ]);

            // refresh collection biar sidebar ada item
            $sessions->prepend($session);
        }

        $messages = $session->messages()->orderBy('created_at')->get();

        return view('clara-ai.index', compact('session', 'sessions', 'messages'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:1000',
            'session_id' => 'required|exists:ai_chat_sessions,id',
        ]);

        $session = AiChatSession::findOrFail($request->session_id);

        // Check authorization
        if ($session->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $result = $this->claraAi->chat($session, $request->message);

        return response()->json($result);
    }

    public function newSession()
    {
        $user = auth()->user();

        $session = AiChatSession::create([
            'user_id'   => $user->id,
            'outlet_id' => $user->outlet_id,
            'title'     => 'Chat Baru - ' . now()->format('d M Y H:i'),
        ]);

        // Langsung redirect ke session baru
        return redirect()->route('clara-ai.index', ['session_id' => $session->id]);
    }
}
