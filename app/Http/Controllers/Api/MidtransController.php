<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * MidtransController — Skinny Controller.
 *
 * Hanya menerima webhook dan meneruskan ke MidtransService.
 * Tidak ada logic di sini.
 */
class MidtransController extends Controller
{
    public function __construct(
        private readonly MidtransService $service,
    ) {}

    /**
     * POST /api/midtrans/webhook
     *
     * Endpoint ini PUBLIC — tidak perlu auth.
     * Keamanan dijaga lewat signature verification di MidtransService.
     *
     * Sesuai payment-flow.md Step 6-9.
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $this->service->handleWebhook($request->all());

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
