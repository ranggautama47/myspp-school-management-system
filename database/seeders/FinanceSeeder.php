<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('super-admin')->first() ?? User::first();

        if (!$admin) {
            $this->command->warn('❌ Tidak ada admin user. Jalankan RolePermissionSeeder + UserSeeder dulu.');
            return;
        }

        $students = Student::with(['user', 'department'])->limit(5)->get();

        if ($students->isEmpty()) {
            $this->command->warn('❌ Tidak ada student data. Buat student dulu via panel.');
            return;
        }

        // ── Seed Invoices ──────────────────────────────────
        foreach ($students as $student) {
            if (!$student->department) {
                continue;
            }

            Invoice::firstOrCreate(
                ['student_id' => $student->id],
                [
                    'department_id' => $student->department_id,
                    'amount' => $student->department->cost,
                    'due_date' => now()->addDays(30),
                    'status' => 'unpaid',
                    'notes' => 'SPP ' . now()->format('F Y'),
                ]
            );
        }

        $this->command->info('✅ Invoices seeded: ' . $students->count());

        // ── Seed Expenses ──────────────────────────────────
        $sampleExpenses = [
            ['name' => 'Pembelian ATK', 'category' => 'operational', 'amount' => 500000, 'expense_date' => now()->subDays(5)],
            ['name' => 'Bayar Listrik Bulan Ini', 'category' => 'utilities', 'amount' => 1200000, 'expense_date' => now()->subDays(10)],
            ['name' => 'Perawatan AC Kelas', 'category' => 'maintenance', 'amount' => 350000, 'expense_date' => now()->subDays(15)],
            ['name' => 'Pembelian Printer', 'category' => 'equipment', 'amount' => 2500000, 'expense_date' => now()->subDays(20)],
            ['name' => 'Honor Guru Ekstrakurikuler', 'category' => 'salary', 'amount' => 750000, 'expense_date' => now()->subDays(3)],
        ];

        foreach ($sampleExpenses as $expense) {
            Expense::create(array_merge($expense, ['recorded_by' => $admin->id]));
        }

        $this->command->info('✅ Expenses seeded: ' . count($sampleExpenses));
    }
}