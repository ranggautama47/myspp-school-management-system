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

        /** * Mengambil data Jurusan dan Semester melalui relasi.
         * Pastikan model Invoice kamu punya relasi ke Student,
         * dan Student punya relasi ke Department dan Semester.
         */
        $this->deptName = $this->invoice->student->department->name ?? '-';
        $this->semesterName = $this->invoice->student->semester->name ?? '-';
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
