<?php

namespace App\Http\Controllers\Student;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use App\Enums\TransactionStatus;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    // =========================================
    // CHECKOUT INVOICE — ubah tagihan menjadi transaksi pending
    // POST /invoices/{invoice}/checkout
    // =========================================

    public function checkoutInvoice(Request $request, Invoice $invoice): RedirectResponse
    {
        // Guard: pastikan invoice ini milik student yang sedang login
        abort_unless(
            $invoice->student_id === $request->user()->student?->id,
            403,
            'Tagihan ini bukan milik Anda.'
        );

        // Guard: pastikan invoice masih bisa dibayar (unpaid atau overdue)
        if (!$invoice->canBePaid()) {
            return back()->with('error', 'Tagihan ini tidak dapat dibayar atau sudah lunas.');
        }

        // Cek apakah invoice ini sudah terhubung ke transaksi yang pending
        if ($invoice->transaction_id) {
            $existingTx = Transaction::find($invoice->transaction_id);
            if ($existingTx && $existingTx->isPending()) {
                return redirect()->route('student.transactions.show', $existingTx)
                    ->with('info', 'Anda sudah memiliki transaksi aktif untuk tagihan ini. Silakan lanjutkan pembayaran.');
            }
        }

        // Buat transaksi baru
        $transaction = Transaction::create([
            'user_id'        => $request->user()->id,
            'department_id'  => $invoice->department_id,
            'amount'         => $invoice->amount,
            'payment_status' => TransactionStatus::Pending,
        ]);

        // Hubungkan invoice ke transaksi ini
        $invoice->update(['transaction_id' => $transaction->id]);

        return redirect()->route('student.transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil dibuat. Silakan pilih metode pembayaran.');
    }

    // =========================================
    // DOWNLOAD INVOICE — simple HTML receipt (print to PDF)
    // GET /transactions/{transaction}/download
    // =========================================

    public function download(Request $request, Transaction $transaction)
    {
        abort_unless(
            $transaction->user_id === $request->user()->id,
            403,
            'Transaksi ini bukan milikmu.'
        );

        $transaction->load([
            'user.student.classroom',
            'user.student.department',
            'department',
            'invoice',
        ]);

        try {
            $pdf = Pdf::loadView('student.transactions.invoice', [
                'transaction'  => $transaction,
                'schoolName'   => Setting::get('school_name', 'Nama Sekolah'),
                'schoolEmail'  => Setting::get('school_email', ''),
                'schoolPhone'  => Setting::get('school_phone', ''),
                'schoolAddress'=> Setting::get('school_address', ''),
                'academicYear' => Setting::get('academic_year', ''),
            ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

            return $pdf->download('Invoice-' . $transaction->code . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF Generation Failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat file PDF. Silakan coba lagi nanti.');
        }
    }
}
