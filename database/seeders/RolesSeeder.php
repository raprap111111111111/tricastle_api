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

               // Document Expiry Alerts
            'document-expiry-alert.viewAny',
            'document-expiry-alert.view',
            'document-expiry-alert.create',
            'document-expiry-alert.delete',
            'document-expiry-alert.dismiss',


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

               // Document Expiry Alerts
            'document-expiry-alert.viewAny',
            'document-expiry-alert.view',
            'document-expiry-alert.create',
            'document-expiry-alert.delete',
            'document-expiry-alert.dismiss',


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
        // ============================================
        // 🟢 STAFF - All permissions
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
            'applicant.view',
            'applicant.create',
            'applicant.update',
            'applicant.delete',
            'applicant.assign',
            'applicant.transfer',

            // Correction Approvals
            'correction-approval.viewAny',
            'correction-approval.view',
            'correction-approval.create',
            'correction-approval.update',
            'correction-approval.delete',
            'correction-approval.approve',
            'correction-approval.reject',
            'correction-approval.escalate',

            // Document Versions
            'document-version.viewAny',
            'document-version.view',
            'document-version.create',
            'document-version.delete',
            'document-version.set-current',

            // Quality Scores
            'quality-score.viewAny',
            'quality-score.view',
            'quality-score.create',
            'quality-score.update',
            'quality-score.delete',
            'quality-score.recalculate',

            // Documents
            'document.viewAny',
            'document.view',
            'document.create',
            'document.update',
            'document.delete',
            'document.download',
            'document.replace',
            'document.view-history',

            // Document Verification Records
            'document-verification.viewAny',
            'document-verification.view',
            'document-verification.create',
            'document-verification.update',
            'document-verification.delete',
            'document-verification.start',
            'document-verification.complete',
            'document-verification.approve',
            'document-verification.reject',

            // Document Verification Operations
            'document.verifyAny',
            'document.verifyAssigned',
            'document.reject',
            'document.reopen-verification',
            'document.override-verification',
            'document.bulk-verify',
            'document.lock',
            'document.unlock',

            // Document Types
            'document-type.viewAny',
            'document-type.view',
            'document-type.create',
            'document-type.update',
            'document-type.delete',

            // File Repository
            'file-repository.viewAny',
            'file-repository.view',
            'file-repository.create',
            'file-repository.delete',
            'file-repository.purge',

            // Correction Requests
            'correction-request.viewAny',
            'correction-request.view',
            'correction-request.create',
            'correction-request.update',
            'correction-request.delete',
            'correction-request.approve',
            'correction-request.reject',
            'correction-request.complete',
            'correction-request.cancel',

            // Users
            'user.viewAny',
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.activate',
            'user.deactivate',
            'user.reset-password',
            'user.view-activity',
            'user.impersonate',

            // Roles
            'role.viewAny',
            'role.view',
            'role.create',
            'role.update',
            'role.delete',
            'role.assign',

            // Permissions
            'permission.viewAny',
            'permission.manage',

            // Reports & Analytics
            'report.view',
            'report.generate',
            'analytics.view',
            'data.export',
            'login-history.viewAny',
            'audit-trail.viewAny',

            // System Settings
            'document-type.manage',
            'notification-setting.manage',
            'email-template.manage',
            'workflow.manage',
            'system-health.view',
            'setting.viewAny',
            'setting.view',
            'setting.create',
            'setting.update',
            'setting.delete',

            // Notifications
            'notification.viewAny',
            'notification.viewOwn',
            'notification.delete',
            'notification.send',
            'notification-template.manage',

            // Special / Dangerous
            'approval.bypass',
            'developer-tool.access',
            'sensitive-data.view',
            'api-token.manage',
            'backup.manage',

            // Verification Mismatch
            'verification-mismatch.viewAny',
            'verification-mismatch.view',
            'verification-mismatch.create',
            'verification-mismatch.update',
            'verification-mismatch.delete',
            'verification-mismatch.resolve',
            'verification-mismatch.waive',
            'verification-mismatch.escalate',

            // Activity Logs
            'activity-log.viewAny',
            'activity-log.view',

            // Document Expiry Alerts
            'document-expiry-alert.viewAny',
            'document-expiry-alert.view',
            'document-expiry-alert.create',
            'document-expiry-alert.delete',
            'document-expiry-alert.dismiss',

            // Login History
            'login-history.view',
            'login-history.viewOwn',
            'login-history.create',
            'login-history.delete',

            // Social Account
            'social-account.viewAny',
            'social-account.viewOwn',
            'social-account.view',
            'social-account.create',
            'social-account.delete',

            // OCR Jobs
            'ocr-job.viewAny',
            'ocr-job.viewOwn',
            'ocr-job.view',
            'ocr-job.create',
            'ocr-job.updateAny',
            'ocr-job.updateOwn',
            'ocr-job.delete',
            'ocr-job.forceDelete',
            'ocr-job.restore',
            'ocr-job.queue',
            'ocr-job.cancel',
            'ocr-job.cancelOwn',
            'ocr-job.retry',
            'ocr-job.review',

            // OCR Templates
            'ocr-templates.viewAny',
            'ocr-templates.view',
            'ocr-templates.create',
            'ocr-templates.update',
            'ocr-templates.delete',
            'ocr-templates.approve',
            'ocr-templates.reject',
            'ocr-templates.complete',
            'ocr-templates.cancel',

            // Comments
            'comment.viewAny',
            'comment.view',
            'comment.viewInternal',
            'comment.create',
            'comment.updateOwn',
            'comment.updateAny',
            'comment.deleteOwn',
            'comment.deleteAny',

            // Company Categories
            'company-category.viewAny',
            'company-category.view',
            'company-category.create',
            'company-category.update',
            'company-category.delete',
            'company-category.toggleStatus',

            // Companies
            'company.viewAny',
            'company.view',
            'company.create',
            'company.update',
            'company.delete',
            'company.toggleStatus',

            // Batches
            'batch.viewAny',
            'batch.view',
            'batch.create',
            'batch.update',
            'batch.delete',
            'batch.updateStatus',
            'batch.manageSlots',

            // Applicant Lifestyle
            'applicant-lifestyle.viewAny',
            'applicant-lifestyle.view',
            'applicant-lifestyle.create',
            'applicant-lifestyle.update',
            'applicant-lifestyle.delete',

            // Applicant Tattoos
            'applicant-tattoo.viewAny',
            'applicant-tattoo.view',
            'applicant-tattoo.create',
            'applicant-tattoo.update',
            'applicant-tattoo.delete',
            'applicant-tattoo.toggleVisibility',

            // Applicant Education
            'applicant-education.viewAny',
            'applicant-education.view',
            'applicant-education.create',
            'applicant-education.update',
            'applicant-education.delete',

            // Applicant Employment
            'applicant-employment.viewAny',
            'applicant-employment.view',
            'applicant-employment.create',
            'applicant-employment.update',
            'applicant-employment.delete',
            'applicant-employment.markAsCurrent',

            // Applicant Batch
            'applicant-batch.viewAny',
            'applicant-batch.view',
            'applicant-batch.create',
            'applicant-batch.update',
            'applicant-batch.delete',
            'applicant-batch.updateStatus',
            'applicant-batch.scheduleInterview',
            'applicant-batch.recordExam',
            'applicant-batch.accept',
            'applicant-batch.reject',
            'applicant-batch.withdraw',
            'applicant-batch.deploy',
            'deployment.viewAny',
            'deployment.view',
            'deployment.create', 
            'deployment.update',  
            'deployment.cancel',  
            'deployment.bulk',    
        ]);

        $this->command->line(' ✅ staff (all permissions)');

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
