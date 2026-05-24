<?php

namespace App\Observers;

use App\Mail\InvoiceCreatedMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        Log::info('[Invoice] Tagihan baru berhasil dibuat', ['invoice_number' => $invoice->number]);

        // Cek apakah relasi siswa ada, relasi user ada, dan memiliki email
        if ($invoice->student && $invoice->student->user && $invoice->student->user->email) {
            Mail::to($invoice->student->user->email)->queue(new InvoiceCreatedMail($invoice));
            Log::info('[Email] InvoiceCreatedMail masuk antrean untuk: ' . $invoice->student->user->email);
        } else {
            Log::warning('[Email] Gagal mengirim InvoiceCreatedMail: Data email siswa tidak ditemukan.', ['invoice_id' => $invoice->id]);
        }
    }
}
