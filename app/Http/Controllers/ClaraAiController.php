<?php

namespace App\Http\Controllers;

use App\Models\AiChatSession;
use App\Services\ClaraAiService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClaraAiController extends Controller implements HasMiddleware
{
    protected $claraAi;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:akses clara ai', only: ['index', 'videoPrompt', 'affiliateScript', 'adsImagePrompt', 'kalkulaba']),
            new Middleware('permission:chat dengan clara ai', only: ['chat', 'generate', 'uploadKalkulabaImage']),
            new Middleware('permission:sesi baru clara ai', only: ['newSession']),
            new Middleware('permission:hapus sesi clara ai', only: ['deleteSession']),
        ];
    }

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
        if (! $session && $sessions->isNotEmpty()) {
            $session = $sessions->first();
        }

        // Jika sama sekali belum ada session, buat baru
        if (! $session) {
            $session = AiChatSession::create([
                'user_id' => $user->id,
                'outlet_id' => $user->outlet_id,
                'title' => 'Chat Baru',
            ]);

            // Refresh collection
            $sessions = $sessions->prepend($session);
        }

        // Ambil messages dari session yang aktif
        $messages = $session->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        return view('main.clara-ai.index', compact('session', 'sessions', 'messages'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
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

        // Cek apakah ini chat pertama (untuk generate title)
        $isFirstMessage = $session->messages()->count() === 0;

        // Proses chat melalui service
        $result = $this->claraAi->chat($session, $request->message);

        // Generate title dari pesan pertama user
        if ($isFirstMessage && $result['success']) {
            $title = $this->generateTitle($request->message);
            $session->update(['title' => $title]);
            $result['new_title'] = $title;
            $result['session_id'] = $session->id;
        }

        return response()->json($result);
    }

    public function newSession()
    {
        $user = auth()->user();

        // Cek apakah ada session kosong (belum ada chat)
        $emptySession = AiChatSession::where('user_id', $user->id)
            ->where('outlet_id', $user->outlet_id)
            ->whereDoesntHave('messages')
            ->orderBy('created_at', 'desc')
            ->first();

        // Jika ada session kosong, redirect ke sana
        if ($emptySession) {
            return redirect()->route('clara-ai.index', ['session_id' => $emptySession->id]);
        }

        // Jika tidak ada, buat session baru
        $session = AiChatSession::create([
            'user_id' => $user->id,
            'outlet_id' => $user->outlet_id,
            'title' => 'Chat Baru',
        ]);

        return redirect()->route('clara-ai.index', ['session_id' => $session->id]);
    }

    public function deleteSession(Request $request, $id)
    {
        $session = AiChatSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat session berhasil dihapus.',
        ]);
    }

    /**
     * AI Studio — Video Prompt Generator Page
     */
    public function videoPrompt()
    {
        return view('main.clara-ai.video-prompt');
    }

    /**
     * AI Studio — Affiliate Script Generator Page
     */
    public function affiliateScript()
    {
        return view('main.clara-ai.affiliate-script');
    }

    /**
     * AI Studio — Ads Image Prompt Generator Page
     */
    public function adsImagePrompt()
    {
        return view('main.clara-ai.ads-image-prompt');
    }

    /**
     * AI Studio — Kalkulaba Cost Analysis & Pricing Page
     */
    public function kalkulaba()
    {
        return view('main.clara-ai.kalkulaba');
    }

    /**
     * Upload product image for Kalkulaba AI analysis.
     * Stores in storage/app/public/kalkulaba/ and returns URL + base64.
     */
    public function uploadKalkulabaImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120', // max 5MB
        ]);

        try {
            $file = $request->file('image');
            $filename = 'kalkulaba_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/kalkulaba', $filename);

            // Generate base64 for AI vision analysis
            $imageData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($imageData);
            $mimeType = $file->getMimeType();

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'image_base64' => "data:{$mimeType};base64,{$base64}",
                'filename' => $filename,
            ]);
        } catch (\Exception $e) {
            \Log::error('Kalkulaba image upload failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AI Studio — Generate content via selected mode
     */
    public function generate(Request $request)
    {
        $request->validate([
            'mode' => 'required|string|in:video_prompt,affiliate_script,ads_image_prompt,kalkulaba',
            'prompt' => 'required|string|max:2000',
            'tone' => 'nullable|string|in:formal,casual,aggressive',
            'language' => 'nullable|string|in:id,en',
        ]);

        $user = auth()->user();

        if (! $user->outlet_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki outlet. Silakan daftarkan outlet terlebih dahulu.',
            ], 403);
        }

        $options = [
            'tone' => $request->tone ?? 'casual',
            'language' => $request->language ?? 'id',
        ];

        // Forward kalkulaba-specific options
        if ($request->mode === 'kalkulaba') {
            $options['product_name'] = $request->input('product_name', '');
            $options['product_description'] = $request->input('product_description', '');
            $options['image_url'] = $request->input('image_url', '');
            $options['image_base64'] = $request->input('image_base64', '');
            $options['business_type'] = $request->input('business_type', 'food');
            $options['additional_costs'] = $request->input('additional_costs', []);
            $options['target_profit'] = (int) $request->input('target_profit', 0);
            $options['advanced_mode'] = $request->input('advanced_mode', '');
        }

        $result = $this->claraAi->generate(
            $request->mode,
            $request->prompt,
            $user->id,
            $options
        );

        return response()->json($result);
    }

    /**
     * Generate title dari pesan user
     */
    private function generateTitle($message)
    {
        // Potong pesan jika terlalu panjang dan tambahkan elipsis
        $title = Str::limit($message, 50, '...');

        // Bersihkan karakter yang tidak diinginkan
        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title);

        return $title ?: 'Chat Baru';
    }
}
