<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Models\Department;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement(TransactionStatus::cases());

        return [
            'code'             => Transaction::generateCode(),
            'user_id'          => User::factory(),
            'department_id'    => Department::factory(),
            'payment_method'   => $status === TransactionStatus::Paid
                                    ? fake()->randomElement(['gopay', 'ovo', 'qris', 'bank_transfer', 'credit_card'])
                                    : null,
            'payment_status'   => $status,
            'snap_token'       => null,
            'midtrans_url'     => null,
            'proof_of_payment' => null,
            'paid_at'          => $status === TransactionStatus::Paid ? fake()->dateTimeBetween('-3 months', 'now') : null,
        ];
    }

    /** State: transaksi pending */
    public function pending(): static
    {
        return $this->state([
            'payment_status' => TransactionStatus::Pending,
            'payment_method' => null,
            'paid_at'        => null,
        ]);
    }

    /** State: transaksi lunas */
    public function paid(): static
    {
        return $this->state([
            'payment_status' => TransactionStatus::Paid,
            'payment_method' => fake()->randomElement(['gopay', 'ovo', 'qris', 'bank_transfer']),
            'paid_at'        => now(),
        ]);
    }

    /** State: transaksi expired */
    public function expired(): static
    {
        return $this->state([
            'payment_status' => TransactionStatus::Expired,
            'payment_method' => null,
            'paid_at'        => null,
        ]);
    }
}
