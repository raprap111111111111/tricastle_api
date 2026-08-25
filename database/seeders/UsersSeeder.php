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
            // 👑 16. SUPER ADMIN
            [
                'first_name'    => 'Ralph',
                'middle_name'   => 'J.',
                'last_name'     => 'Barioga',
                'email'         => 'bariogahot@gmail.com',
                'phone'         => '09915947463',
                'password'      => 'password',
                'employee_code' => 'EMP-001',
                'department'    => 'IT',
                'position'      => 'System Developer',
                'role'          => 'super_admin',
            ],

            // 💼 1. ADMINISTRATOR
            [
                'first_name'    => 'Ariel',
                'middle_name'   => 'D.',
                'last_name'     => 'Taocta',
                'email'         => 'ad_taocta75@yahoo.com',
                'phone'         => '09637574220',
                'password'      => 'password',
                'employee_code' => 'EMP-002',
                'department'    => 'Management',
                'position'      => 'Company Administrator',
                'role'          => 'staff',
            ],

            // 👔 2. SUPERVISOR
            [
                'first_name'    => 'Jonel',
                'middle_name'   => 'G.',
                'last_name'     => 'Perater',
                'email'         => 'jonel.perater@tricastle.com',
                'phone'         => '09397646211',
                'password'      => 'password',
                'employee_code' => 'EMP-003',
                'department'    => 'Operations',
                'position'      => 'Documents Supervisor',
                'role'          => 'staff',
            ],

            // 📋 STAFF MEMBERS (3 to 15 & 17)
            [
                'first_name'    => 'Mary Grace',
                'middle_name'   => 'C.',
                'last_name'     => 'Rebato',
                'email'         => 'rebatograce0@gmail.com',
                'phone'         => '09171473773',
                'password'      => 'password',
                'employee_code' => 'EMP-004',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Norene',
                'middle_name'   => 'E.',
                'last_name'     => 'Moyani',
                'email'         => 'moyaninorene@gmail.com',
                'phone'         => '09955004799',
                'password'      => 'password',
                'employee_code' => 'EMP-005',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Mark Anthony',
                'middle_name'   => 'L.',
                'last_name'     => 'Bermudez',
                'email'         => 'mark.bermudez@tricastle.com',
                'phone'         => '09937769719',
                'password'      => 'password',
                'employee_code' => 'EMP-006',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Cristy',
                'middle_name'   => 'C.',
                'last_name'     => 'Sayosa',
                'email'         => 'cristysayosa123@gmail.com',
                'phone'         => '09106516517',
                'password'      => 'password',
                'employee_code' => 'EMP-007',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Ramil',
                'middle_name'   => 'C.',
                'last_name'     => 'Sayosa',
                'email'         => 'ramilsayosa22@gmail.com',
                'phone'         => '09461951896',
                'password'      => 'password',
                'employee_code' => 'EMP-008',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Ernesto',
                'middle_name'   => 'C.',
                'last_name'     => 'Sayosa Jr.',
                'email'         => 'ernesto.sayosa@tricastle.com',
                'phone'         => '09955239510',
                'password'      => 'password',
                'employee_code' => 'EMP-009',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Gina',
                'middle_name'   => 'A.',
                'last_name'     => 'Garalda',
                'email'         => 'gina.garalda@tricastle.com',
                'phone'         => '09054322078',
                'password'      => 'password',
                'employee_code' => 'EMP-010',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Nelsie',
                'middle_name'   => 'B.',
                'last_name'     => 'Malabja',
                'email'         => 'nelsie.malabja@tricastle.com',
                'phone'         => '09187124343',
                'password'      => 'password',
                'employee_code' => 'EMP-011',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Jerlyn',
                'middle_name'   => 'P.',
                'last_name'     => 'Saldo',
                'email'         => 'jerlynsaldo03@gmail.com',
                'phone'         => '09453258491',
                'password'      => 'password',
                'employee_code' => 'EMP-012',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Rodrigo II',
                'middle_name'   => null,
                'last_name'     => 'Apostol',
                'email'         => 'rodrodapostol007@gmail.com',
                'phone'         => '09272724850',
                'password'      => 'password',
                'employee_code' => 'EMP-013',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Eric',
                'middle_name'   => 'O.',
                'last_name'     => 'Toreres',
                'email'         => 'erictoreres@gmail.com',
                'phone'         => '09129381508',
                'password'      => 'password',
                'employee_code' => 'EMP-014',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Paul',
                'middle_name'   => 'C.',
                'last_name'     => 'Pitogo',
                'email'         => 'pitogo_paul@yahoo.com',
                'phone'         => '09917587868',
                'password'      => 'password',
                'employee_code' => 'EMP-015',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'John Lee',
                'middle_name'   => 'B.',
                'last_name'     => 'Gumban',
                'email'         => 'johnlee.gumban@tricastle.com',
                'phone'         => '09772030374',
                'password'      => 'password',
                'employee_code' => 'EMP-016',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
            [
                'first_name'    => 'Joizen',
                'middle_name'   => 'M.',
                'last_name'     => 'Contayoso',
                'email'         => 'joizenmonasterio@gmail.com',
                'phone'         => '09664403152',
                'password'      => 'password',
                'employee_code' => 'EMP-017',
                'department'    => 'Documents',
                'position'      => 'Document Verifier',
                'role'          => 'staff',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);
            
            $userData['password'] = Hash::make($userData['password']);
            $userData['email_verified_at'] = now();
            $userData['is_active'] = true;
            
            $user = User::updateOrCreate(
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