<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👤 Creating default users...');

        // Verify roles exist first
        $requiredRoles = ['super_admin', 'admin', 'supervisor', 'staff', 'viewer'];
        foreach ($requiredRoles as $roleName) {
            if (!Role::where('name', $roleName)->where('guard_name', 'api')->exists()) {
                $this->command->error("❌ Role '{$roleName}' not found for guard 'api'! Run RolesSeeder first.");
                return;
            }
        }

        $users = [
            [
                'first_name'    => 'Ralph',
                'middle_name'   => null,
                'last_name'     => 'Barioga',
                'email'         => 'bariogahot@gmail.com',
                'password'      => 'password',
                'employee_code' => 'EMP-001',
                'department'    => 'IT',
                'position'      => 'System Developer',
                'role'          => 'super_admin',
            ],
            [
                'first_name'    => 'John',
                'middle_name'   => 'Michael',
                'last_name'     => 'Administrator',
                'email'         => 'admin@tricastle.com',
                'password'      => 'password',
                'employee_code' => 'EMP-002',
                'department'    => 'Management',
                'position'      => 'Company Administrator',
                'role'          => 'admin',
            ],
            [
                'first_name'    => 'Maria',
                'middle_name'   => 'Cruz',
                'last_name'     => 'Santos',
                'email'         => 'supervisor@tricastle.com',
                'password'      => 'password',
                'employee_code' => 'EMP-003',
                'department'    => 'Documents',
                'position'      => 'Documents Team Lead',
                'role'          => 'supervisor',
            ],
            [
                'first_name'    => 'Juan',
                'middle_name'   => 'Ponce',
                'last_name'     => 'Dela Cruz',
                'email'         => 'staff1@tricastle.com',
                'password'      => 'password',
                'employee_code' => 'EMP-004',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Pedro',
                'middle_name'   => 'Garcia',
                'last_name'     => 'Reyes',
                'email'         => 'staff2@tricastle.com',
                'password'      => 'password',
                'employee_code' => 'EMP-005',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Ana',
                'middle_name'   => 'Mendoza',
                'last_name'     => 'Cruz',
                'email'         => 'staff3@tricastle.com',
                'password'      => 'password',
                'employee_code' => 'EMP-006',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Roberto',
                'middle_name'   => null,
                'last_name'     => 'Auditor',
                'email'         => 'auditor@tricastle.com',
                'password'      => 'password',
                'employee_code' => 'EMP-007',
                'department'    => 'External',
                'position'      => 'POEA Auditor',
                'role'          => 'viewer',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);
            
            $userData['password'] = Hash::make($userData['password']);
            $userData['email_verified_at'] = now();
            $userData['is_active'] = true;
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // ✅ Explicitly fetch role with 'api' guard
            $role = Role::findByName($roleName, 'api');
            
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }

            $fullName = trim("{$userData['first_name']} {$userData['middle_name']} {$userData['last_name']}");
            $this->command->line("   ✅ {$fullName} ({$roleName})");
        }

        $this->command->info("");
        $this->command->info("✅ Users Seeded Successfully!");
        $this->command->line("   └── Total Users: " . User::count());
        $this->command->info("");
        $this->command->warn("🔐 Default password for all users: password");
    }
}