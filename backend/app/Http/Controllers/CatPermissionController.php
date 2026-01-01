<?php

namespace App\Http\Controllers;

use App\Models\CatPermission;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CatPermissionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permission_name' => ['required', 'string', 'max:50'],
            'key' => ['required', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        try {
            $permission = CatPermission::create($data)->fresh();
            return response()->json($permission, 201);
        } catch (QueryException $e) {
            Log::error('Failed to create CatPermission', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database constraint violation'], 400);
        } catch (\Throwable $e) {
            Log::error('Unexpected error creating CatPermission', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Unexpected error',
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
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

        try {
            $query = CatPermission::query();

            $paginator = $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $paginator->items(),
                'meta' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ],
            ]);
        } catch (QueryException $e) {
            Log::error('Failed to list CatPermission', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database query error'], 400);
        } catch (\Throwable $e) {
            Log::error('Unexpected error listing CatPermission', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Unexpected error'], 500);
        }
    }
}
