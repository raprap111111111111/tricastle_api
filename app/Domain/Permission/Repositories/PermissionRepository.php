<?php
// app/Domain/Permission/Repositories/PermissionRepository.php

namespace App\Domain\Permission\Repositories;

use App\Domain\Permission\DTOs\CreatePermissionDTO;
use App\Domain\Permission\DTOs\UpdatePermissionDTO;
use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class PermissionRepository
{
    // ═══════════════════════════════════════════════════════
    // Read
    // ═══════════════════════════════════════════════════════

    /**
     * Paginated list with filters
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Permission::query()
            ->search($filters['search'] ?? null)
            ->when($filters['module'] ?? null, fn ($q, $m) => $q->byModule($m))
            ->when($filters['guard_name'] ?? null, fn ($q, $g) => $q->where('guard_name', $g))
            ->orderBy(
                $filters['order_by']  ?? 'name',
                $filters['order_dir'] ?? 'asc'
            )
            ->paginate($filters['limit'] ?? 10);
    }

    /**
     * Find by ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    /**
     * Find by name
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByName(string $name, string $guardName = 'api'): Permission
    {
        return Permission::findByName($name, $guardName);
    }

    /**
     * All grouped by module
     */
    public function getGroupedByModule(): SupportCollection
    {
        return Permission::orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission) {
                if ($permission->module) {
                    return $permission->module;
                }

                // Fallback: derive from name prefix
                $parts = explode('.', $permission->name);
                return $parts[0] ?? 'general';
            });
    }

    /**
     * Get all distinct modules
     */
    public function getAllModules(): SupportCollection
    {
        return Permission::query()
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');
    }

    // ═══════════════════════════════════════════════════════
    // Write
    // ═══════════════════════════════════════════════════════

    public function create(CreatePermissionDTO $dto): Permission
    {
        return Permission::create([
            'name'        => $dto->name,
            'guard_name'  => 'api',
            'description' => $dto->description,
            'module'      => $dto->module ?? $this->deriveModule($dto->name),
        ]);
    }

    public function update(Permission $permission, UpdatePermissionDTO $dto): Permission
    {
        $permission->update(array_filter([
            'name'        => $dto->name,
            'description' => $dto->description,
            'module'      => $dto->module,
        ], fn ($v) => !is_null($v)));

        return $permission->fresh();
    }

    public function delete(Permission $permission): void
    {
        $permission->delete();
    }

    // ═══════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════

    private function deriveModule(string $name): string
    {
        $parts = explode('.', $name);
        return $parts[0] ?? 'general';
    }
}