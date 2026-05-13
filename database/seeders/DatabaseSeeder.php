<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Roles & Permissions dulu (Spatie)
            RolePermissionSeeder::class,

            // 2. Departments & Transactions dummy
            DepartmentTransactionSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 MySPP database seeded successfully!');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',   'admin@myspp.com',   'password'],
                ['Student', 'student@myspp.com', 'password'],
            ]
        );
    }
}
