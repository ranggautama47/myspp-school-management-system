<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtransService
    ) {}

    // =========================================
    // LIST — semua transaksi milik siswa
    // GET /transactions
    // =========================================

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Transaction::where('user_id', $user->id)
            ->with('department')
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $transactions = $query->paginate(10);

        return view('student.transactions.index', compact('transactions'));
    }

    // =========================================
    // SHOW — detail transaksi + tombol bayar
    // GET /transactions/{transaction}
    // =========================================

    public function show(Request $request, Transaction $transaction): View
    {
        // Guard: siswa hanya bisa lihat transaksi miliknya
        abort_unless(
            $transaction->user_id === $request->user()->id,
            403,
            'Transaksi ini bukan milikmu.'
        );

        $transaction->load(['department', 'paymentLogs']);

        return view('student.transactions.show', compact('transaction'));
    }

    // =========================================
    // GET SNAP TOKEN — untuk trigger Snap popup
    // POST /transactions/{transaction}/snap-token
    // Response: JSON { snap_token, client_key }
    // =========================================

    public function getSnapToken(Request $request, Transaction $transaction): JsonResponse
    {
        abort_unless(
            $transaction->user_id === $request->user()->id,
            403
        );

        if (! $transaction->canBePaid()) {
            return response()->json([
                'message' => 'Transaksi ini tidak bisa dibayar. Status: ' . $transaction->payment_status->label(),
            ], 422);
        }

        try {
            $snapToken = $this->midtransService->createSnapToken($transaction);

            return response()->json([
                'snap_token'    => $snapToken,
                'client_key'    => config('services.midtrans.client_key'),
                'is_production' => config('services.midtrans.is_production'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================
    // UPLOAD PROOF — bukti bayar manual
    // POST /transactions/{transaction}/upload-proof
    // =========================================

    public function uploadProof(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless(
            $transaction->user_id === $request->user()->id,
            403
        );

        abort_unless(
            $transaction->isPending(),
            422,
            'Bukti bayar hanya bisa diupload untuk transaksi pending.'
        );

        $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        // Hapus file lama
        if ($transaction->proof_of_payment) {
            Storage::disk('public')->delete($transaction->proof_of_payment);
        }

        $path = $request->file('proof')->store('proofs', 'public');

        $transaction->update(['proof_of_payment' => $path]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Admin akan memverifikasi segera.');
    }
}
