<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Transaction $transaction;
    public string $schoolName;
    public string $schoolEmail;
    public string $schoolPhone;
    public string $schoolAddress;
    public string $academicYear;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;

        $this->schoolName    = Setting::get('school_name', 'MySPP');
        $this->schoolEmail   = Setting::get('school_email', '');
        $this->schoolPhone   = Setting::get('school_phone', '');
        $this->schoolAddress = Setting::get('school_address', '');
        $this->academicYear  = Setting::get('academic_year', '');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . $this->schoolName . '] Pembayaran SPP Berhasil — ' . $this->transaction->code . ' ✅',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
