<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Transaction;
use App\Models\User;
use App\Enums\TransactionStatus;
use Illuminate\Database\Seeder;

class DepartmentTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================
        // 1. DATA JURUSAN / DEPARTMENT
        // =========================================

        $departments = [
            ['name' => 'Teknik Informatika',       'semester' => 1, 'cost' => 2500000],
            ['name' => 'Teknik Informatika',       'semester' => 2, 'cost' => 2500000],
            ['name' => 'Teknik Informatika',       'semester' => 3, 'cost' => 2750000],
            ['name' => 'Akuntansi',                'semester' => 1, 'cost' => 2000000],
            ['name' => 'Akuntansi',                'semester' => 2, 'cost' => 2000000],
            ['name' => 'Manajemen Bisnis',         'semester' => 1, 'cost' => 2200000],
            ['name' => 'Manajemen Bisnis',         'semester' => 2, 'cost' => 2200000],
            ['name' => 'Teknik Elektro',           'semester' => 1, 'cost' => 2800000],
            ['name' => 'Teknik Elektro',           'semester' => 2, 'cost' => 2800000],
            ['name' => 'Desain Komunikasi Visual', 'semester' => 1, 'cost' => 3000000],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept['name'], 'semester' => $dept['semester']],
                ['cost' => $dept['cost']]
            );
        }

        $this->command->info('✅ ' . count($departments) . ' departments created.');

        // =========================================
        // 2. TRANSAKSI DUMMY UNTUK STUDENT DEFAULT
        // =========================================

        $student = User::where('email', 'student@myspp.com')->first();
        $dept    = Department::first();

        if ($student && $dept) {
            // Transaksi lunas
            Transaction::firstOrCreate(
                ['code' => 'TRX-20250101-DEMO1'],
                [
                    'user_id'        => $student->id,
                    'department_id'  => $dept->id,
                    'payment_method' => 'gopay',
                    'payment_status' => TransactionStatus::Paid,
                    'paid_at'        => now()->subMonth(),
                    'amount'         => $dept->cost,
                ]
            );

            // Transaksi pending (belum bayar)
            Transaction::firstOrCreate(
                ['code' => 'TRX-20250511-DEMO2'],
                [
                    'user_id'        => $student->id,
                    'department_id'  => $dept->id,
                    'payment_status' => TransactionStatus::Pending,
                    'amount'         => $dept->cost,
                ]
            );

            $this->command->info('✅ Demo transactions created for student@myspp.com');
        }
    }
}
