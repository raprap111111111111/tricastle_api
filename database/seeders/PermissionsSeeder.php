<?php
// database/seeders/PermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            // ============================================
            // 📊 DASHBOARD
            // ============================================
            'dashboard.view' => 'Access main dashboard',

            // ============================================
            // 👥 APPLICANT MANAGEMENT
            // ============================================
            'applicant.viewAny'  => 'View all applicants list',
            'applicant.view'     => 'View single applicant record',
            'applicant.create'   => 'Create new applicant profiles',
            'applicant.update'   => 'Update applicant profile',
            'applicant.delete'   => 'Delete applicant profiles',
            'applicant.assign'   => 'Assign applicants to staff',
            'applicant.transfer' => 'Transfer applicants between staff',

            // ============================================
            // ✅ CORRECTION APPROVALS
            // ============================================
            'correction-approval.viewAny'  => 'View all correction approvals',
            'correction-approval.view'     => 'View single correction approval',
            'correction-approval.create'   => 'Create correction approval',
            'correction-approval.update'   => 'Update correction approval',
            'correction-approval.delete'   => 'Delete correction approval',
            'correction-approval.approve'  => 'Approve correction approval',
            'correction-approval.reject'   => 'Reject correction approval',
            'correction-approval.escalate' => 'Escalate correction approval',

            // ============================================
            // 📋 DOCUMENT VERSIONS
            // ============================================
            'document-version.viewAny'     => 'View all document versions',
            'document-version.view'        => 'View single document version',
            'document-version.create'      => 'Upload new document version',
            'document-version.delete'      => 'Delete document version',
            'document-version.set-current' => 'Set document version as current',

            // ============================================
            // 📊 QUALITY SCORES
            // ============================================
            'quality-score.viewAny'     => 'View all quality scores',
            'quality-score.view'        => 'View single quality score',
            'quality-score.create'      => 'Create quality score manually',
            'quality-score.update'      => 'Update quality score',
            'quality-score.delete'      => 'Delete quality score',
            'quality-score.recalculate' => 'Recalculate quality score for applicant',

            // ============================================
            // 📄 DOCUMENT MANAGEMENT
            // ============================================
            'document.viewAny'      => 'View all documents',
            'document.view'         => 'View single document',
            'document.create'       => 'Upload new documents',
            'document.update'       => 'Edit document metadata',
            'document.delete'       => 'Delete documents',
            'document.download'     => 'Download document files',
            'document.replace'      => 'Replace existing document',
            'document.view-history' => 'View document version history',

            // ============================================
            // ✅ DOCUMENT VERIFICATION RECORDS (CRUD)
            // ============================================
            'document-verification.viewAny'  => 'View all document verifications',
            'document-verification.view'     => 'View single document verification',
            'document-verification.create'   => 'Create document verification',
            'document-verification.update'   => 'Update document verification',
            'document-verification.delete'   => 'Delete document verification',
            'document-verification.start'    => 'Start document verification process',
            'document-verification.complete' => 'Complete document verification',
            'document-verification.approve'  => 'Approve document verification',
            'document-verification.reject'   => 'Reject document verification',

            // ============================================
            // 🔍 DOCUMENT VERIFICATION OPERATIONS
            // ============================================
            'document.verifyAny'             => 'Verify any document',
            'document.verifyAssigned'        => 'Verify only assigned documents',
            'document.reject'                => 'Reject documents',
            'document.reopen-verification'   => 'Reopen completed verifications',
            'document.override-verification' => 'Override verification decisions',
            'document.bulk-verify'           => 'Bulk verify multiple documents',
            'document.lock'                  => 'Lock documents from editing',
            'document.unlock'                => 'Unlock locked documents',

            // ============================================
            // 📋 DOCUMENT TYPE MANAGEMENT
            // ============================================
            'document-type.viewAny' => 'View all document types',
            'document-type.view'    => 'View single document type',
            'document-type.create'  => 'Create new document type',
            'document-type.update'  => 'Update document type',
            'document-type.delete'  => 'Delete document type',

            // ============================================
            // 📁 FILE REPOSITORY
            // ============================================
            'file-repository.viewAny' => 'View file repository list',
            'file-repository.view'    => 'View single file record',
            'file-repository.create'  => 'Upload files to repository',
            'file-repository.delete'  => 'Soft delete file record',
            'file-repository.purge'   => 'Permanently delete file and physical storage',

            // ============================================
            // 🔄 CORRECTION REQUESTS
            // ============================================
            'correction-request.viewAny'  => 'View all correction requests',
            'correction-request.view'     => 'View single correction request',
            'correction-request.create'   => 'Create correction request',
            'correction-request.update'   => 'Update correction request',
            'correction-request.delete'   => 'Delete correction request',
            'correction-request.approve'  => 'Approve correction request',
            'correction-request.reject'   => 'Reject correction request',
            'correction-request.complete' => 'Complete correction request',
            'correction-request.cancel'   => 'Cancel correction request',

            // ============================================
            // 👤 USER MANAGEMENT
            // ============================================
            'user.viewAny'        => 'View all users list',
            'user.view'           => 'View single user record',
            'user.create'         => 'Create new user accounts',
            'user.update'         => 'Edit user profiles',
            'user.delete'         => 'Delete user accounts',
            'user.activate'       => 'Activate user accounts',
            'user.deactivate'     => 'Deactivate user accounts',
            'user.reset-password' => 'Reset user passwords',
            'user.view-activity'  => 'View user activity logs',
            'user.impersonate'    => 'Login as another user',

            // ============================================
            // 🎭 ROLE MANAGEMENT
            // ============================================
            'role.viewAny' => 'View all roles',
            'role.view'    => 'View single role',
            'role.create'  => 'Create new roles',
            'role.update'  => 'Edit existing roles',
            'role.delete'  => 'Delete roles',
            'role.assign'  => 'Assign roles to users',

            // ============================================
            // 🔐 PERMISSION MANAGEMENT
            // ============================================
            'permission.viewAny' => 'View all permissions',
            'permission.manage'  => 'Manage role permissions',

            // ============================================
            // 📈 REPORTS & ANALYTICS
            // ============================================
            'report.view'           => 'View reports section',
            'report.generate'       => 'Generate custom reports',
            'analytics.view'        => 'View analytics data',
            'data.export'           => 'Export data to Excel/PDF',
            'login-history.viewAny' => 'View login history',
            'audit-trail.viewAny'   => 'View full audit trail',

            // ============================================
            // ⚙️ SYSTEM SETTINGS
            // ============================================
            'document-type.manage'        => 'Manage document type configurations',
            'notification-setting.manage' => 'Manage notification settings',
            'email-template.manage'       => 'Manage email templates',
            'workflow.manage'             => 'Manage approval workflows',
            'system-health.view'          => 'View system health status',

            'setting.viewAny' => 'View all system settings',
            'setting.view'    => 'View single system setting',
            'setting.create'  => 'Create new system setting',
            'setting.update'  => 'Update system setting',
            'setting.delete'  => 'Delete system setting',

            // ============================================
            // 🔔 NOTIFICATIONS
            // ============================================
            'notification.viewAny'         => 'View all notifications (admin)',
            'notification.viewOwn'         => 'View own notifications',
            'notification.delete'          => 'Delete any notification',
            'notification.send'            => 'Send notifications to users',
            'notification-template.manage' => 'Manage notification templates',

            // ============================================
            // 🔐 SPECIAL / DANGEROUS PERMISSIONS
            // ============================================
            'approval.bypass'       => 'Bypass approval workflows',
            'developer-tool.access' => 'Access developer tools',
            'sensitive-data.view'   => 'View sensitive/confidential data',
            'api-token.manage'      => 'Manage API tokens',
            'backup.manage'         => 'Manage system backups',

            // ============================================
            // 🔍 VERIFICATION MISMATCH
            // ============================================
            'verification-mismatch.viewAny'  => 'View all verification mismatches',
            'verification-mismatch.view'     => 'View single verification mismatch',
            'verification-mismatch.create'   => 'Create verification mismatch',
            'verification-mismatch.update'   => 'Update verification mismatch',
            'verification-mismatch.delete'   => 'Delete verification mismatch',
            'verification-mismatch.resolve'  => 'Resolve verification mismatch',
            'verification-mismatch.waive'    => 'Waive verification mismatch',
            'verification-mismatch.escalate' => 'Escalate verification mismatch',

            // ============================================
            // 📋 ACTIVITY LOGS
            // ============================================
            'activity-log.viewAny' => 'View all activity logs',
            'activity-log.view'    => 'View single activity log',

            // ============================================
            // 📋 DOCUMENT EXPIRY ALERTS
            // ============================================
            'document-expiry-alert.viewAny' => 'View all document expiry alerts',
            'document-expiry-alert.view'    => 'View single document expiry alert',
            'document-expiry-alert.create'  => 'Create document expiry alert',
            'document-expiry-alert.delete'  => 'Delete document expiry alert',
            'document-expiry-alert.dismiss' => 'Dismiss document expiry alert',

            // ============================================
            // 📋 LOGIN HISTORY
            // ============================================
            'login-history.view'   => 'View single login history entry',
            'login-history.viewOwn' => 'View own login history',
            'login-history.create' => 'Create login history entry',
            'login-history.delete' => 'Delete login history entry',

            // ============================================
            // 📋 SOCIAL ACCOUNT
            // ============================================
            'social-account.viewAny' => 'View all social accounts',
            'social-account.viewOwn' => 'View own social accounts',
            'social-account.view'    => 'View single social account',
            'social-account.create'  => 'Create social account link',
            'social-account.delete'  => 'Delete social account link',

            // ============================================
            // 🤖 OCR JOBS
            // ============================================
            'ocr-job.viewAny'     => 'View all OCR jobs',
            'ocr-job.viewOwn'     => 'View own OCR jobs',
            'ocr-job.view'        => 'View single OCR job',
            'ocr-job.create'      => 'Create new OCR job',
            'ocr-job.updateAny'   => 'Update any OCR job',
            'ocr-job.updateOwn'   => 'Update own OCR job',
            'ocr-job.delete'      => 'Delete OCR job',
            'ocr-job.forceDelete' => 'Force-delete OCR job',
            'ocr-job.restore'     => 'Restore soft-deleted OCR job',

            // OCR Job custom actions
            'ocr-job.queue'     => 'Queue OCR job for processing',
            'ocr-job.cancel'    => 'Cancel any OCR job',
            'ocr-job.cancelOwn' => 'Cancel own OCR job',
            'ocr-job.retry'     => 'Retry failed OCR job',
            'ocr-job.review'    => 'Review OCR job results',

            // ============================================
            // 📋 OCR TEMPLATES
            // ============================================
            'ocr-templates.viewAny'  => 'View all OCR templates list',
            'ocr-templates.view'     => 'View single OCR template record',
            'ocr-templates.create'   => 'Create new OCR template',
            'ocr-templates.update'   => 'Update OCR template',
            'ocr-templates.delete'   => 'Delete OCR template',
            'ocr-templates.approve'  => 'Approve OCR template',
            'ocr-templates.reject'   => 'Reject OCR template',
            'ocr-templates.complete' => 'Complete OCR template',
            'ocr-templates.cancel'   => 'Cancel OCR template',

            // ============================================
            // 💬 COMMENTS
            // ============================================
            'comment.viewAny'      => 'View all comments',
            'comment.view'         => 'View a public comment',
            'comment.viewInternal' => 'View internal/staff-only comments',
            'comment.create'       => 'Post a new comment',
            'comment.updateOwn'    => 'Edit own comment',
            'comment.updateAny'    => 'Edit any comment',
            'comment.deleteOwn'    => 'Delete own comment',
            'comment.deleteAny'    => 'Delete any comment',

            // ============================================
            // 🏷️ COMPANY CATEGORIES
            // ============================================
            'company-category.viewAny'      => 'View all company categories',
            'company-category.view'         => 'View single company category',
            'company-category.create'       => 'Create new company category',
            'company-category.update'       => 'Update company category',
            'company-category.delete'       => 'Delete company category',
            'company-category.toggleStatus' => 'Toggle company category active status',

            // ============================================
            // 🏢 COMPANIES
            // ============================================
            'company.viewAny'      => 'View all companies',
            'company.view'         => 'View single company',
            'company.create'       => 'Create new company',
            'company.update'       => 'Update company details',
            'company.delete'       => 'Delete company',
            'company.toggleStatus' => 'Toggle company active status',

            // ============================================
            // 📦 BATCHES
            // ============================================
            'batch.viewAny'      => 'View all batches',
            'batch.view'         => 'View single batch',
            'batch.create'       => 'Create new batch',
            'batch.update'       => 'Update batch details',
            'batch.delete'       => 'Delete batch',
            'batch.updateStatus' => 'Change batch status (draft/open/closed/etc.)',
            'batch.manageSlots'  => 'Manage batch slot allocation',

            // ============================================
            // 🚬 APPLICANT LIFESTYLE
            // ============================================
            'applicant-lifestyle.viewAny' => 'View all applicant lifestyle records',
            'applicant-lifestyle.view'    => 'View single applicant lifestyle record',
            'applicant-lifestyle.create'  => 'Create applicant lifestyle record',
            'applicant-lifestyle.update'  => 'Update applicant lifestyle record',
            'applicant-lifestyle.delete'  => 'Delete applicant lifestyle record',

            // ============================================
            // 🎨 APPLICANT TATTOOS
            // ============================================
            'applicant-tattoo.viewAny'          => 'View all applicant tattoos',
            'applicant-tattoo.view'             => 'View single applicant tattoo',
            'applicant-tattoo.create'           => 'Create new applicant tattoo record',
            'applicant-tattoo.update'           => 'Update applicant tattoo record',
            'applicant-tattoo.delete'           => 'Delete applicant tattoo record',
            'applicant-tattoo.toggleVisibility' => 'Toggle tattoo visibility (clothed/exposed)',

            // ============================================
            // 🎓 APPLICANT EDUCATION
            // ============================================
            'applicant-education.viewAny' => 'View all applicant education records',
            'applicant-education.view'    => 'View single applicant education record',
            'applicant-education.create'  => 'Create applicant education record',
            'applicant-education.update'  => 'Update applicant education record',
            'applicant-education.delete'  => 'Delete applicant education record',

            // ============================================
            // 💼 APPLICANT EMPLOYMENT
            // ============================================
            'applicant-employment.viewAny'      => 'View all applicant employment records',
            'applicant-employment.view'         => 'View single applicant employment record',
            'applicant-employment.create'       => 'Create applicant employment record',
            'applicant-employment.update'       => 'Update applicant employment record',
            'applicant-employment.delete'       => 'Delete applicant employment record',
            'applicant-employment.markAsCurrent' => 'Mark employment as current job',

            // ============================================
            // 📝 APPLICANT BATCH (Application Pipeline)
            // ============================================
            'applicant-batch.viewAny'           => 'View all applicant batch applications',
            'applicant-batch.view'              => 'View single applicant batch application',
            'applicant-batch.create'            => 'Apply applicant to a batch',
            'applicant-batch.update'            => 'Update applicant batch application',
            'applicant-batch.delete'            => 'Delete applicant batch application',
            'applicant-batch.updateStatus'      => 'Change applicant batch status',
            'applicant-batch.scheduleInterview' => 'Schedule interview for applicant',
            'applicant-batch.recordExam'        => 'Record exam result',
            'applicant-batch.accept'            => 'Accept applicant into batch',
            'applicant-batch.reject'            => 'Reject applicant from batch',
            'applicant-batch.withdraw'          => 'Withdraw applicant from batch',
            'applicant-batch.deploy'            => 'Deploy accepted applicant',
        ];

        $this->command->info('🔑 Creating permissions...');

        $created  = 0;
        $existing = 0;
        $skipped  = 0;

        foreach ($permissions as $name => $description) {
            // Safety check: skip if $name is an integer (positional entry)
            if (is_int($name)) {
                $this->command->warn("⚠️  Skipping positional entry: {$description}");
                $skipped++;
                continue;
            }

            $permission = Permission::firstOrCreate(
                [
                    'name'       => $name,
                    'guard_name' => 'api',
                ],
                [
                    'description' => $description,
                ]
            );

            if ($permission->wasRecentlyCreated) {
                $created++;
            } else {
                $existing++;
            }
        }

        $this->command->info('✅ Permissions Seeded Successfully!');
        $this->command->line("   ├── Created: {$created}");
        $this->command->line("   ├── Already existed: {$existing}");
        if ($skipped > 0) {
            $this->command->line("   ├── Skipped (bad format): {$skipped}");
        }
        $this->command->line('   └── Total: ' . Permission::count());
    }
}