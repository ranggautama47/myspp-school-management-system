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
        // 1. DAFTAR SEMUA PERMISSIONS (Gabungan Lama & Baru)
        // Tetap menggunakan format '-' agar kodingan lama tidak error
        // =========================================
        $permissions = [
            // Academic
            'manage-departments',
            'manage-classrooms',
            'manage-students',
            'view-academic-reports',

            // Finance
            'view-dashboard',
            'manage-transactions',
            'approve-payment',
            'view-reports',
            'export-reports',

            // System
            'manage-users',
            'manage-roles',
            'manage-settings',
            'view-audit-logs',

            // Student Specific (Dari kodemu yang lama)
            'view-own-transactions',
            'make-payment',
            'upload-proof',
            'edit-profile',
        ];

        // Buat Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        $this->command->info('✅ Permissions created: ' . count($permissions));

        // =========================================
        // 2. STRUKTUR ROLES (Dari Claude, tapi disesuaikan)
        // =========================================
        $roles = [
            'super-admin' => '*', // Dapat semua akses
            'admin' => [
                'view-dashboard',
                'manage-departments',
                'manage-classrooms',
                'manage-students',
                'manage-transactions',
                'approve-payment',
                'view-reports',
                'manage-users',
            ],
            'operator' => [
                'view-dashboard',
                'manage-students',
                'manage-transactions',
                'view-reports',
            ],
            'bendahara' => [
                'view-dashboard',
                'manage-transactions',
                'approve-payment',
                'view-reports',
                'export-reports',
            ],
            'student' => [
                // Mengembalikan hak akses student milikmu
                'view-own-transactions',
                'make-payment',
                'upload-proof',
                'edit-profile',
            ],
        ];

        // Buat Roles & Assign Permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if ($rolePermissions === '*') {
                $role->syncPermissions(Permission::all());
            } elseif (!empty($rolePermissions)) {
                $role->syncPermissions($rolePermissions);
            } else {
                $role->syncPermissions([]);
            }
            $this->command->info("✅ Role [{$roleName}] configured.");
        }

        // =========================================
        // 3. KEMBALIKAN PEMBUATAN USER DEFAULT KAMU (Penting!)
        // =========================================
        $admin = User::firstOrCreate(
            ['email' => 'admin@myspp.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'phone'    => '081234567890',
            ]
        );
        $admin->assignRole('super-admin'); // Ubah ke super-admin agar full akses
        $this->command->info('✅ Admin user created: admin@myspp.com / password');

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

        // Note: Bagian Default Settings dari Claude saya buang sementara
        // untuk mencegah error "Table settings not found".
    }
}
