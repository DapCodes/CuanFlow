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

        // Ambil semua session user untuk sidebar
        $sessions = AiChatSession::where('user_id', $user->id)
            ->where('outlet_id', $user->outlet_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $session = null;

        // Cek apakah ada session_id di query string
        if ($request->has('session_id')) {
            $session = AiChatSession::where('id', $request->session_id)
                ->where('user_id', $user->id)
                ->where('outlet_id', $user->outlet_id)
                ->first();
        }

        // Jika tidak ada atau tidak valid, ambil session terbaru
        if (!$session && $sessions->isNotEmpty()) {
            $session = $sessions->first();
        }

        // Jika sama sekali belum ada session, buat baru
        if (!$session) {
            $session = AiChatSession::create([
                'user_id'   => $user->id,
                'outlet_id' => $user->outlet_id,
                'title'     => 'Chat dengan Clara AI',
            ]);

            // Refresh collection
            $sessions = $sessions->prepend($session);
        }

        // Ambil messages dari session yang aktif
        $messages = $session->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        return view('clara-ai.index', compact('session', 'sessions', 'messages'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:1000',
            'session_id' => 'required|exists:ai_chat_sessions,id',
        ]);

        $session = AiChatSession::findOrFail($request->session_id);

        // Verifikasi kepemilikan session
        if ($session->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke sesi chat ini.',
            ], 403);
        }

        // Proses chat melalui service
        $result = $this->claraAi->chat($session, $request->message);

        return response()->json($result);
    }

    public function newSession()
    {
        $user = auth()->user();

        // Buat session baru
        $session = AiChatSession::create([
            'user_id'   => $user->id,
            'outlet_id' => $user->outlet_id,
            'title'     => 'Chat Baru - ' . now()->format('d M Y H:i'),
        ]);

        // Redirect ke session baru dengan query parameter
        return redirect()->route('clara-ai.index', ['session_id' => $session->id]);
    }
}