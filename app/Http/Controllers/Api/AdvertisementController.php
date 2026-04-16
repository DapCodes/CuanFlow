<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data advertisement
        $advertisements = Advertisement::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil semua data advertisement',
            'data' => $advertisements,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $advertisement = Advertisement::find($id);

        if (! $advertisement) {
            return response()->json([
                'success' => false,
                'message' => 'Advertisement tidak ditemukan',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data advertisement',
            'data' => $advertisement,
        ], 200);
    }
}
