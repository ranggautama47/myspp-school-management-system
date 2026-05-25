<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public string $schoolName;
    public string $schoolEmail;
    public string $schoolPhone;
    public string $schoolAddress;
    public string $academicYear;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;

        // Ambil data sekolah dari DB di constructor
        // semua property public otomatis tersedia di blade
        $this->schoolName    = Setting::get('school_name', 'MySPP');
        $this->schoolEmail   = Setting::get('school_email', '');
        $this->schoolPhone   = Setting::get('school_phone', '');
        $this->schoolAddress = Setting::get('school_address', '');
        $this->academicYear  = Setting::get('academic_year', '');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . $this->schoolName . '] Tagihan SPP Baru — Rp ' . number_format($this->invoice->amount, 0, ',', '.'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
