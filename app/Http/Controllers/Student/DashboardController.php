<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Load student profile dengan relasi
        $student = $user->student()->with(['classroom', 'department', 'academicYear'])->first();

        // Invoice yang belum dibayar (unpaid + overdue)
        $pendingInvoices = Invoice::where('student_id', $student?->id)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->with(['department', 'transaction'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Transaksi terbaru milik siswa ini
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('department')
            ->latest()
            ->limit(5)
            ->get();

        // Summary stats
        $totalPaid    = Transaction::where('user_id', $user->id)->paid()->sum('amount');
        $totalPending = Transaction::where('user_id', $user->id)->pending()->count();

        return view('student.dashboard', compact(
            'student',
            'pendingInvoices',
            'recentTransactions',
            'totalPaid',
            'totalPending',
        ));
    }
}
