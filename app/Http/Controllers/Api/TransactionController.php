<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UploadProofRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

/**
 * TransactionController — Skinny Controller.
 *
 * Sesuai architecture.md: controller hanya terima request,
 * panggil service, kembalikan response.
 * Tidak ada business logic di sini.
 */
class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $service,
    ) {
    }

    /**
     * GET /api/transactions
     * Admin: semua transaksi. Student: milik sendiri saja.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $transactions = Transaction::with(['user', 'department'])
            ->when($user->isStudent(), fn($q) => $q->byUser($user->id))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * GET /api/transactions/{transaction}
     */
    public function show(Transaction $transaction): JsonResponse
    {
        Gate::authorize('view', $transaction);

        return response()->json([
            'success' => true,
            'data' => $transaction->load(['user', 'department', 'paymentLogs']),
        ]);
    }

    /**
     * POST /api/transactions
     * Admin buat tagihan baru untuk siswa.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->service->createBill(
            student: \App\Models\User::findOrFail($request->validated('user_id')),
            departmentId: $request->validated('department_id'),
        );

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibuat.',
            'data' => $transaction->load(['user', 'department']),
        ], 201);
    }

    /**
     * POST /api/transactions/{transaction}/pay
     * Siswa mulai bayar via Midtrans — dapat snap_token.
     */
    public function pay(Transaction $transaction): JsonResponse
    {
        Gate::authorize('pay', $transaction);

        $snapData = $this->service->initiateMidtransPayment($transaction);

        return response()->json([
            'success' => true,
            'snap_token' => $snapData['token'],
            'redirect_url' => $snapData['redirect_url'],
        ]);
    }

    /**
     * POST /api/transactions/{transaction}/upload-proof
     * Siswa upload bukti bayar manual.
     */
    public function uploadProof(UploadProofRequest $request, Transaction $transaction): JsonResponse
    {
        Gate::authorize('uploadProof', $transaction);

        $path = $request->file('proof_of_payment')->store('proofs', 'public');
        $this->service->submitManualPayment($transaction, $path);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin.',
        ]);
    }

    /**
     * PUT /api/transactions/{transaction}/approve
     * Admin approve pembayaran manual.
     */
    public function approve(Transaction $transaction): JsonResponse
    {
        Gate::authorize('approve', $transaction);

        $this->service->approveManualPayment($transaction);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi.',
        ]);
    }
}
