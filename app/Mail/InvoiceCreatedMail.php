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
    // Tambahkan properti baru
    public string $deptName;
    public string $semesterName;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;

        // Ambil data sekolah dari DB
        $this->schoolName    = Setting::get('school_name', 'MySPP');
        $this->schoolEmail   = Setting::get('school_email', '');
        $this->schoolPhone   = Setting::get('school_phone', '');
        $this->schoolAddress = Setting::get('school_address', '');
        $this->academicYear  = Setting::get('academic_year', '');

        // Semester dalam aplikasi ini disimpan pada Department,
        // bukan langsung pada Student. Gunakan relasi Invoice->department.
        $department = $this->invoice->department;
        $this->deptName = $department?->name ?? '-';
        $this->semesterName = $department?->semester ? 'Semester ' . $department->semester : '-';
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
            // Variabel public di atas sudah otomatis terlempar ke view,
            // tapi bisa juga didefinisikan eksplisit di sini jika mau.
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
