<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache Spatie sebelum seeder
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // =========================================
        // 1. BUAT SEMUA PERMISSIONS
        // =========================================

        $adminPermissions = [
            'view-dashboard',
            'manage-departments',
            'manage-users',
            'manage-transactions',
            'approve-payment',
            'export-reports',
        ];

        $studentPermissions = [
            'view-own-transactions',
            'make-payment',
            'upload-proof',
            'edit-profile',
        ];

        $allPermissions = array_merge($adminPermissions, $studentPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ ' . count($allPermissions) . ' permissions created.');

        // =========================================
        // 2. BUAT ROLES DAN ASSIGN PERMISSIONS
        // =========================================

        // Role: admin
        $adminRole = Role::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions($adminPermissions);

        // Role: student
        $studentRole = Role::firstOrCreate([
            'name'       => 'student',
            'guard_name' => 'web',
        ]);
        $studentRole->syncPermissions($studentPermissions);

        $this->command->info('✅ Roles created: admin, student');

        // =========================================
        // 3. BUAT USER ADMIN DEFAULT
        // =========================================

        $admin = User::firstOrCreate(
            ['email' => 'admin@myspp.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'phone'    => '081234567890',
            ]
        );
        $admin->assignRole('admin');

        $this->command->info('✅ Admin user created: admin@myspp.com / password');

        // =========================================
        // 4. BUAT USER STUDENT DEFAULT
        // =========================================

        $student = User::firstOrCreate(
            ['email' => 'student@myspp.com'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('password'),
                'phone'    => '089876543210',
            ]
        );
        $student->assignRole('student');

        $this->command->info('✅ Student user created: student@myspp.com / password');
    }
}
