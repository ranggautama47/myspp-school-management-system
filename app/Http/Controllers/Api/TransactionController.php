<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    // =========================================
    // INDEX
    // GET /api/transactions
    // Admin → semua transaksi
    // Student → hanya transaksi milik sendiri
    // =========================================

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Transaction::with(['user', 'department'])->latest();

        // Student hanya lihat transaksi milik sendiri
        if ($user->hasRole('student')) {
            $query->where('user_id', $user->id);
        }

        // Filter by status (opsional)
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $transactions = $query->paginate(15);

        return response()->json([
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    // =========================================
    // SHOW
    // GET /api/transactions/{transaction}
    // =========================================

    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        $user = $request->user();

        // Student hanya bisa lihat transaksi milik sendiri
        if ($user->hasRole('student') && $transaction->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $transaction->load(['user', 'department', 'paymentLogs']);

        return response()->json([
            'data' => $this->formatTransaction($transaction),
        ]);
    }

    // =========================================
    // STORE
    // POST /api/transactions
    // Admin only — buat transaksi manual
    // Body: { user_id, department_id, payment_method }
    // =========================================

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole(['admin', 'super admin', 'operator'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'payment_method' => ['nullable', 'in:bank_transfer,e_wallet,manual'],
        ]);

        $department = Department::findOrFail($validated['department_id']);

        $transaction = Transaction::create([
            'user_id' => $validated['user_id'],
            'department_id' => $validated['department_id'],
            'amount' => $department->cost,
            'payment_method' => $validated['payment_method'] ?? 'manual',
            'payment_status' => TransactionStatus::Pending,
        ]);

        $transaction->load(['user', 'department']);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat.',
            'data' => $this->formatTransaction($transaction),
        ], 201);
    }

    // =========================================
    // PAY — request snap token untuk bayar
    // POST /api/transactions/{transaction}/pay
    // Student: request Midtrans snap token
    // Response: snap_token yang dipakai di frontend
    // =========================================

    public function pay(Request $request, Transaction $transaction): JsonResponse
    {
        $user = $request->user();

        // Student hanya bisa bayar transaksi milik sendiri
        if ($user->hasRole('student') && $transaction->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$transaction->canBePaid()) {
            return response()->json([
                'message' => 'Transaksi tidak bisa dibayar. Status saat ini: ' . $transaction->payment_status->label(),
            ], 422);
        }

        // Kembalikan snap token yang sudah ada jika masih valid
        if ($transaction->snap_token) {
            return response()->json([
                'snap_token' => $transaction->snap_token,
                'client_key' => config('services.midtrans.client_key'),
                'is_production' => config('services.midtrans.is_production'),
            ]);
        }

        // Generate snap token baru via MidtransService
        try {
            $midtransService = app(\App\Services\MidtransService::class);
            $snapToken = $midtransService->createSnapToken($transaction);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('services.midtrans.client_key'),
                'is_production' => config('services.midtrans.is_production'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal generate snap token: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================
    // APPROVE — manual approval
    // POST /api/transactions/{transaction}/approve
    // Admin / Bendahara only
    // =========================================

    public function approve(Request $request, Transaction $transaction): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole(['admin', 'super admin', 'bendahara'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$transaction->isPending()) {
            return response()->json([
                'message' => 'Hanya transaksi pending yang bisa di-approve.',
            ], 422);
        }

        $transaction->markAsPaid(
            $transaction->payment_method ?? 'manual'
        );

        return response()->json([
            'message' => 'Transaksi berhasil di-approve.',
            'data' => $this->formatTransaction($transaction->fresh()),
        ]);
    }

    // =========================================
    // UPLOAD PROOF
    // POST /api/transactions/{transaction}/upload-proof
    // Student — upload bukti bayar manual
    // Body: multipart/form-data, field: proof (image/pdf, max 5MB)
    // =========================================

    public function uploadProof(Request $request, Transaction $transaction): JsonResponse
    {
        $user = $request->user();

        // Student hanya bisa upload untuk transaksi milik sendiri
        if ($user->hasRole('student') && $transaction->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$transaction->isPending()) {
            return response()->json([
                'message' => 'Bukti bayar hanya bisa diupload untuk transaksi yang masih pending.',
            ], 422);
        }

        $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        // Hapus file lama jika ada
        if ($transaction->proof_of_payment) {
            Storage::disk('public')->delete($transaction->proof_of_payment);
        }

        $path = $request->file('proof')->store('proofs', 'public');

        $transaction->update([
            'proof_of_payment' => $path,
        ]);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diupload.',
            'proof_url' => Storage::url($path),
        ]);
    }

    // =========================================
    // PRIVATE — format response
    // =========================================

    private function formatTransaction(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'code' => $transaction->code,
            'amount' => (int) $transaction->amount,
            'amount_display' => 'Rp ' . number_format((float) $transaction->amount, 0, ',', '.'),
            'payment_method' => $transaction->payment_method,
            'payment_status' => [
                'value' => $transaction->payment_status->value,
                'label' => $transaction->payment_status->label(),
                'color' => $transaction->payment_status->color(),
            ],
            'snap_token' => $transaction->snap_token,
            'proof_of_payment' => $transaction->proof_of_payment
                ? Storage::url($transaction->proof_of_payment)
                : null,
            'paid_at' => $transaction->paid_at?->format('Y-m-d H:i:s'),
            'created_at' => $transaction->created_at?->format('Y-m-d H:i:s'),
            'user' => $transaction->user ? [
                'id' => $transaction->user->id,
                'name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ] : null,
            'department' => $transaction->department ? [
                'id' => $transaction->department->id,
                'name' => $transaction->department->name,
                'semester' => $transaction->department->semester,
            ] : null,
        ];
    }
}