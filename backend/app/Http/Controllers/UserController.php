<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'id_companies' => ['nullable', 'integer', 'exists:cat_companies,id'],
            'type' => ['nullable', 'integer', 'exists:cat_users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $page = (int)($data['page'] ?? 1);
        $perPage = (int)($data['per_page'] ?? 10);

        // Derive company from authenticated user if not provided
        $companyId = $data['id_companies'] ?? null;
        $userAuth = $request->user();
        if (!$companyId && $userAuth) {
            $companyId = $userAuth->company_id ?? $userAuth->id_companies ?? null;
        }

        try {
            $query = User::query()->with(['typeCatalog', 'company']);
            if ($companyId) {
                $query->where('id_companies', $companyId);
            }
            if (!empty($data['type'])) {
                $query->where('type', $data['type']);
            }

            $paginator = $query->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $paginator->items(),
                'meta' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ],
            ], 200);
        } catch (QueryException $e) {
            Log::error('Failed to list Users', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database query error'], 400);
        } catch (\Throwable $e) {
            Log::error('Unexpected error listing Users', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Unexpected error'], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'type' => ['required', 'integer', 'exists:cat_users,id'],
            'id_companies' => ['required', 'integer', 'exists:cat_companies,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        try {
            // Password will be hashed automatically due to model cast
            $user = User::create($data)->fresh(['typeCatalog', 'company']);
            return response()->json($user, 201);
        } catch (QueryException $e) {
            Log::error('Failed to create User', ['error' => $e->getMessage()]);
            $message = 'Database constraint violation';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'Email already exists';
                return response()->json(['message' => $message], 409);
            }
            return response()->json(['message' => $message], 400);
        } catch (\Throwable $e) {
            Log::error('Unexpected error creating User', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Unexpected error'], 500);
        }
    }

    /**
     * Devuelve módulos y permisos asociados al rol del usuario solicitado.
     * Requiere el parámetro `id` del usuario.
     * Resultado por módulo con permisos agregados en arreglo JSON.
     */
    public function roleModulesPermissions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = (int)($validator->validated()['id']);

        try {
            $rows = DB::table('users as s')
                ->join('cat_users as cu', 'cu.id', '=', 's.type')
                ->join('rel_role_permissions as rrp', 'rrp.id_role', '=', 'cu.id')
                ->join('cat_modules as cm', 'cm.id', '=', 'rrp.id_module')
                ->join('cat_permissions as cp', 'cp.id', '=', 'rrp.id_permission')
                ->where('s.id', $userId)
                ->where('cm.is_active', 1)
                ->groupBy('s.id', 's.name', 's.email', 'cu.name', 'cm.module_name')
                ->select([
                    's.id',
                    's.name',
                    's.email',
                    DB::raw('cu.name as role_name'),
                    DB::raw('cm.module_name'),
                    DB::raw('JSON_ARRAYAGG(cp.permission_name) as permissions'),
                ])
                ->get();

            if ($rows->isEmpty()) {
                return response()->json([
                    'data' => [],
                    'meta' => [ 'user_id' => $userId, 'modules' => 0 ],
                ], 200);
            }

            $first = $rows->first();
            $user = [
                'id' => (int)$first->id,
                'name' => $first->name,
                'email' => $first->email,
                'role_name' => $first->role_name,
            ];

            $modules = $rows->map(function ($r) {
                $perms = json_decode($r->permissions, true);
                if (!is_array($perms)) { $perms = []; }
                return [
                    'module_name' => $r->module_name,
                    'permissions' => $perms,
                ];
            })->values()->all();

            return response()->json([
                'data' => [
                    'user' => $user,
                    'modules' => $modules,
                ],
                'meta' => [ 'user_id' => $userId, 'modules' => count($modules) ],
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Failed to get role modules & permissions', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database query error'], 400);
        } catch (\Throwable $e) {
            Log::error('Unexpected error in roleModulesPermissions', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Unexpected error'], 500);
        }
    }
}
