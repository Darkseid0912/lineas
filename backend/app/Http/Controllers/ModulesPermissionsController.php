<?php

namespace App\Http\Controllers;

use App\Models\CatModule;
use App\Models\CatPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ModulesPermissionsController extends Controller
{
    /**
     * List modules with their permissions.
     * Permissions are matched to modules by key prefix (slug-based):
     * examples: "users.view" or "users:create" or "users_list" will map to module slug "users".
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), []);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        try {
            $modulesQuery = CatModule::query()->orderBy('module_name');

            $modules = $modulesQuery->get(['id', 'module_name', 'slug', 'is_active', 'created_at']);

            // Fetch all permissions (no grouping)
            $permissions = CatPermission::query()
                ->orderBy('permission_name')
                ->get(['id', 'permission_name', 'key', 'created_at']);

            return response()->json([
                'data' => [
                    'modules' => $modules,
                    'permissions' => $permissions,
                ],
                'meta' => [
                    'total_modules' => $modules->count(),
                    'total_permissions' => $permissions->count(),
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to list modules with permissions', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Unexpected error',
            ], 500);
        }
    }
}
