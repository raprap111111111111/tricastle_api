<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    private const GUARD = 'api';

    /**
     * Create DEFAULT roles only.
     *
     * NOTE:
     * - These are just starter roles
     * - Admins can create more roles later via UI
     * - Keep permission names aligned with policies
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if (Permission::where('guard_name', self::GUARD)->count() === 0) {
            $this->command->error('❌ No permissions found! Run PermissionsSeeder first.');
            return;
        }

        $this->command->info('🎭 Creating default roles...');

        // ============================================
        // 🔴 SUPER ADMIN - Everything
        // ============================================
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => self::GUARD,
        ]);

        $superAdmin->syncPermissions(
            Permission::where('guard_name', self::GUARD)->pluck('name')->all()
        );

        $this->command->line('   ✅ super_admin (all permissions)');

        // ============================================
        // 🟠 ADMIN - Almost everything
        // ============================================
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => self::GUARD,
        ]);

        $this->syncRolePermissions($admin, [
            // Dashboard
            'dashboard.view',

            // Applicants
            'applicant.viewAny',
            'applicant.viewOwn',
            'applicant.create',
            'applicant.updateAny',
            'applicant.updateOwn',
            'applicant.delete',
            'applicant.assign',
            'applicant.transfer',

            // Documents
            'document.viewAny',
            'document.viewOwn',
            'document.create',
            'document.updateAny',
            'document.updateOwn',
            'document.delete',
            'document.download',
            'document.replace',
            'document.view-history',

            // Verification
            'document.verifyAny',
            'document.verifyAssigned',
            'document.reject',
            'document.reopen-verification',
            'document.bulk-verify',

            // Corrections
            'correction.create',
            'correction.viewAny',
            'correction.viewOwn',
            'correction.approve',
            'correction.reject',
            'correction.escalate',
            'correction.cancel',

            // Users
            'user.viewAny',
            'user.create',
            'user.update',
            'user.activate',
            'user.deactivate',
            'user.reset-password',
            'user.view-activity',

            // Roles / Permissions
            'role.viewAny',
            'role.create',
            'role.update',
            'role.assign',
            'permission.viewAny',
            'permission.manage',

            // Reports / Analytics
            'report.view',
            'analytics.view',
            'report.generate',
            'data.export',
            'activity-log.viewAny',
            'login-history.viewAny',
            'audit-trail.viewAny',

            // Settings
            'setting.view',
            'setting.update',
            'document-type.manage',
            'notification-setting.manage',
            'email-template.manage',
            'workflow.manage',
            'system-health.view',

            // Files
            'file-repository.viewAny',
            'file-repository.manage',

            // Notifications
            'notification.viewAny',
            'notification.send',
            'notification-template.manage',

            // Special
            'document.lock',
            'document.unlock',
            'sensitive-data.view',
        ]);

        $this->command->line('   ✅ admin (management access)');

        // ============================================
        // 🟡 SUPERVISOR - Team management
        // ============================================
        $supervisor = Role::firstOrCreate([
            'name' => 'supervisor',
            'guard_name' => self::GUARD,
        ]);

        $this->syncRolePermissions($supervisor, [
            // Dashboard
            'dashboard.view',

            // Applicants
            'applicant.viewAny',
            'applicant.viewOwn',
            'applicant.create',
            'applicant.updateAny',
            'applicant.updateOwn',
            'applicant.assign',
            'applicant.transfer',

            // Documents
            'document.viewAny',
            'document.viewOwn',
            'document.create',
            'document.updateAny',
            'document.updateOwn',
            'document.download',
            'document.replace',
            'document.view-history',

            // Verification
            'document.verifyAny',
            'document.verifyAssigned',
            'document.reject',
            'document.reopen-verification',
            'document.bulk-verify',

            // Corrections
            'correction.create',
            'correction.viewAny',
            'correction.viewOwn',
            'correction.approve',
            'correction.reject',
            'correction.escalate',

            // Users
            'user.viewAny',
            'user.view-activity',

            // Reports
            'report.view',
            'analytics.view',
            'data.export',
            'activity-log.viewAny',

            // Files
            'file-repository.viewAny',

            // Notifications
            'notification.viewAny',

            // Special
            'document.lock',
            'document.unlock',
        ]);

        $this->command->line('   ✅ supervisor (team management)');

        // ============================================
        // 🟢 STAFF - Daily work
        // ============================================
        $staff = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => self::GUARD,
        ]);

        $this->syncRolePermissions($staff, [
            // Dashboard
            'dashboard.view',

            // Applicants
            'applicant.viewAny',
            'applicant.viewOwn',
            'applicant.create',
            'applicant.updateOwn',

            // Documents
            'document.viewAny',
            'document.viewOwn',
            'document.create',
            'document.updateOwn',
            'document.download',
            'document.view-history',

            // Verification
            'document.verifyAssigned',

            // Corrections
            'correction.create',
            'correction.viewOwn',
            'correction.cancel',

            // Notifications
            'notification.viewAny',
        ]);

        $this->command->line('   ✅ staff (daily operations)');

        // ============================================
        // 🔵 VIEWER - Read only
        // ============================================
        $viewer = Role::firstOrCreate([
            'name' => 'viewer',
            'guard_name' => self::GUARD,
        ]);

        $this->syncRolePermissions($viewer, [
            'dashboard.view',
            'applicant.viewAny',
            'document.viewAny',
            'document.download',
            'report.view',
            'notification.viewAny',
        ]);

        $this->command->line('   ✅ viewer (read-only access)');

        $this->command->info('');
        $this->command->info('✅ Roles Seeded Successfully!');
        $this->command->line('   └── Total Roles: ' . Role::where('guard_name', self::GUARD)->count());
    }

    private function syncRolePermissions(Role $role, array $permissions): void
    {
        $existingPermissions = Permission::where('guard_name', self::GUARD)
            ->whereIn('name', $permissions)
            ->pluck('name')
            ->all();

        $missingPermissions = array_diff($permissions, $existingPermissions);

        if (! empty($missingPermissions)) {
            $this->command->warn("   ⚠️ Missing permissions for role [{$role->name}]:");
            foreach ($missingPermissions as $permission) {
                $this->command->line("      - {$permission}");
            }
        }

        $role->syncPermissions($existingPermissions);
    }
}