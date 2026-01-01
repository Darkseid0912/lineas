<?php

namespace App\Http\Controllers;

use App\Models\RelRolePermission;
use App\Models\CatUser;
use App\Models\CatModule;
use App\Models\CatPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class RelRolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'id_role' => ['nullable', 'integer', 'exists:cat_users,id'],
            'id_module' => ['nullable', 'integer', 'exists:cat_modules,id'],
            'id_permission' => ['nullable', 'integer', 'exists:cat_permissions,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parámetros inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $perPage = (int) ($request->input('per_page', 10));
        $page = (int) ($request->input('page', 1));

        $query = RelRolePermission::with(['role', 'module', 'permission'])
            ->orderByDesc('created_at');

        if ($request->filled('id_role')) {
            $query->where('id_role', $request->integer('id_role'));
        }
        if ($request->filled('id_module')) {
            $query->where('id_module', $request->integer('id_module'));
        }
        if ($request->filled('id_permission')) {
            $query->where('id_permission', $request->integer('id_permission'));
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_role' => ['required', 'integer', 'exists:cat_users,id'],
            'id_module' => ['required', 'integer', 'exists:cat_modules,id'],
            'id_permission' => ['required', 'integer', 'exists:cat_permissions,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $role = CatUser::find($request->integer('id_role'));
            $module = CatModule::find($request->integer('id_module'));
            $permission = CatPermission::find($request->integer('id_permission'));

            // Validar que pertenezcan a la misma empresa
            $roleCompanyId = $role?->id_companies;
            $moduleCompanyId = $module?->id_companies;
            $permissionCompanyId = $permission?->id_companies;

            if (!($roleCompanyId && $moduleCompanyId && $permissionCompanyId) ||
                !($roleCompanyId === $moduleCompanyId && $moduleCompanyId === $permissionCompanyId)) {
                return response()->json([
                    'message' => 'Los catálogos deben pertenecer a la misma empresa',
                    'errors' => [
                        'id_companies' => ['Los catálogos deben pertenecer a la misma empresa'],
                    ],
                ], 422);
            }

            // Evitar duplicados exactos
            $exists = RelRolePermission::where([
                'id_role' => $role->id,
                'id_module' => $module->id,
                'id_permission' => $permission->id,
            ])->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'La relación ya existe',
                ], 409);
            }

            $rel = RelRolePermission::create([
                'id_role' => $role->id,
                'id_module' => $module->id,
                'id_permission' => $permission->id,
            ])->fresh(['role', 'module', 'permission']);

            return response()->json([
                'data' => $rel,
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al guardar la relación',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Error inesperado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Guarda relaciones de rol con permisos en bloque, reemplazando las existentes para el rol.
     * Espera un payload con la forma:
     * {
     *   "role_id": 21,
     *   "id_companies": 5,
     *   "modules": [
     *     { "module_id": 1, "permission_ids": [2] },
     *     { "module_id": 5, "permission_ids": [1, 2] }
     *   ]
     * }
     */
    public function storeBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => ['required', 'integer', 'exists:cat_users,id'],
            'id_companies' => ['required', 'integer', 'exists:cat_companies,id'],
            'modules' => ['required', 'array', 'min:1'],
            'modules.*.module_id' => ['required', 'integer', 'exists:cat_modules,id'],
            'modules.*.permission_ids' => ['required', 'array', 'min:1'],
            'modules.*.permission_ids.*' => ['required', 'integer', 'exists:cat_permissions,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        try {
            $role = CatUser::find($data['role_id']);
            if (!$role) {
                return response()->json(['message' => 'Rol no encontrado'], 404);
            }

            // Validar que el rol pertenezca a la empresa indicada
            if ((int)($role->id_companies) !== (int)$data['id_companies']) {
                return response()->json([
                    'message' => 'El rol no pertenece a la empresa indicada',
                    'errors' => ['id_companies' => ['El rol no pertenece a la empresa indicada']],
                ], 422);
            }

            // Validar módulos y permisos contra la empresa
            $moduleIds = collect($data['modules'])->pluck('module_id')->unique()->values();
            $permissionIds = collect($data['modules'])
                ->flatMap(fn ($m) => $m['permission_ids'] ?? [])
                ->unique()->values();

            $modules = CatModule::whereIn('id', $moduleIds)->get(['id', 'id_companies']);
            $permissions = CatPermission::whereIn('id', $permissionIds)->get(['id', 'id_companies']);

            $invalidModule = $modules->first(fn ($m) => (int)$m->id_companies !== (int)$data['id_companies']);
            $invalidPermission = $permissions->first(fn ($p) => (int)$p->id_companies !== (int)$data['id_companies']);
            if ($invalidModule || $invalidPermission) {
                return response()->json([
                    'message' => 'Módulos y permisos deben pertenecer a la misma empresa',
                    'errors' => ['id_companies' => ['Módulos y permisos deben pertenecer a la misma empresa']],
                ], 422);
            }

            // Reemplazar relaciones del rol en una transacción
            $created = [];
            DB::transaction(function () use ($data, $role, &$created) {
                // Nota: no se elimina el set previo; se agregan nuevas relaciones

                // Insertar nuevas relaciones (evitar duplicados dentro del mismo payload)
                $pairs = collect($data['modules'])->flatMap(function ($m) use ($role) {
                    $mid = (int)$m['module_id'];
                    $permIds = collect($m['permission_ids'])->map(fn ($pid) => (int)$pid)->unique()->values();
                    return $permIds->map(fn ($pid) => [
                        'id_role' => (int)$role->id,
                        'id_module' => $mid,
                        'id_permission' => $pid,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                })->unique(function ($row) {
                    return $row['id_role'].'-'.$row['id_module'].'-'.$row['id_permission'];
                })->values();

                if ($pairs->isNotEmpty()) {
                    // Usar insert masivo para rendimiento
                    RelRolePermission::insert($pairs->all());
                    $created = $pairs->all();
                }
            });

            return response()->json([
                'message' => 'Relaciones actualizadas',
                'data' => [
                    'role_id' => (int)$role->id,
                    'count' => count($created),
                    'items' => $created,
                ],
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al guardar relaciones',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Error inesperado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lista TODAS las relaciones de permisos de un rol sin paginación.
     */
    public function listByRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_role' => ['required', 'integer', 'exists:cat_users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parámetros inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $roleId = (int)$request->input('id_role');

        $items = RelRolePermission::query()
            ->where('id_role', $roleId)
            ->orderBy('id_module')
            ->get(['id', 'id_role', 'id_module', 'id_permission', 'created_at']);

        return response()->json([
            'data' => $items,
        ], 200);
    }

    /**
     * Sincroniza relaciones: elimina las no seleccionadas y agrega/restaura las seleccionadas.
     * Payload igual a storeBulk.
     */
    public function syncBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => ['required', 'integer', 'exists:cat_users,id'],
            'id_companies' => ['required', 'integer', 'exists:cat_companies,id'],
            'modules' => ['required', 'array'],
            'modules.*.module_id' => ['required', 'integer', 'exists:cat_modules,id'],
            'modules.*.permission_ids' => ['required', 'array'],
            'modules.*.permission_ids.*' => ['required', 'integer', 'exists:cat_permissions,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        try {
            $role = CatUser::find($data['role_id']);
            if (!$role) {
                return response()->json(['message' => 'Rol no encontrado'], 404);
            }
            if ((int)$role->id_companies !== (int)$data['id_companies']) {
                return response()->json([
                    'message' => 'El rol no pertenece a la empresa indicada',
                    'errors' => ['id_companies' => ['El rol no pertenece a la empresa indicada']]
                ], 422);
            }

            // Nota: CatModule y CatPermission ya no tienen relación de empresa.
            // Se valida existencia vía reglas `exists` y se asegura la empresa sólo por el rol.

            $desiredByModule = collect($data['modules'])
                ->mapWithKeys(function ($m) {
                    return [ (int)$m['module_id'] => collect($m['permission_ids'])->map(fn ($pid) => (int)$pid)->unique()->values()->all() ];
                });

            $created = 0;
            $deleted = 0;

            DB::transaction(function () use ($role, $desiredByModule, &$created, &$deleted) {
                $roleId = (int)$role->id;

                $desiredModuleIds = $desiredByModule->keys()->all();

                // 1) Eliminar relaciones de módulos deseleccionados (no presentes en el payload)
                if (count($desiredModuleIds) > 0) {
                    $deleted += RelRolePermission::where('id_role', $roleId)
                        ->whereNotIn('id_module', $desiredModuleIds)
                        ->forceDelete();
                } else {
                    // Si no hay módulos deseados, eliminar todas las relaciones del rol (hard delete)
                    $deleted += RelRolePermission::where('id_role', $roleId)->forceDelete();
                }

                // 2) Por cada módulo seleccionado, eliminar permisos no deseados
                foreach ($desiredByModule as $mid => $permIds) {
                    $permIds = is_array($permIds) ? $permIds : [];
                    if (count($permIds) > 0) {
                        $deleted += RelRolePermission::where('id_role', $roleId)
                            ->where('id_module', (int)$mid)
                            ->whereNotIn('id_permission', $permIds)
                            ->forceDelete();
                    } else {
                        // Si el módulo está sin permisos en el payload, borra todas las relaciones de ese módulo (hard delete)
                        $deleted += RelRolePermission::where('id_role', $roleId)
                            ->where('id_module', (int)$mid)
                            ->forceDelete();
                    }
                }

                // 3) Insertar o restaurar las relaciones deseadas
                foreach ($desiredByModule as $mid => $permIds) {
                    foreach ($permIds as $pid) {
                        $exists = RelRolePermission::where('id_role', $roleId)
                            ->where('id_module', (int)$mid)
                            ->where('id_permission', (int)$pid)
                            ->exists();

                        if (!$exists) {
                            RelRolePermission::create([
                                'id_role' => $roleId,
                                'id_module' => (int)$mid,
                                'id_permission' => (int)$pid,
                            ]);
                            $created++;
                        }
                    }
                }
            });

            return response()->json([
                'message' => 'Relaciones sincronizadas',
                'data' => [
                    'role_id' => (int)$role->id,
                    'created' => (int)$created,
                    'deleted' => (int)$deleted,
                ],
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al sincronizar relaciones',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Error inesperado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
