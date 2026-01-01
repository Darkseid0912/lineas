<?php

namespace App\Http\Controllers;

use App\Models\CatModule;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CatModuleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $perPage = $data['per_page'] ?? 10;
        $page = $data['page'] ?? 1;

        try {
            $query = CatModule::query();

            if (array_key_exists('is_active', $data)) {
                $query->where('is_active', (bool) $data['is_active']);
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
            Log::error('Failed to list CatModules', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database constraint violation'], 400);
        } catch (\Throwable $e) {
            Log::error('Unexpected error listing CatModules', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Unexpected error'], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'module_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:50', 'unique:cat_modules,slug'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        if (!array_key_exists('is_active', $data)) {
            $data['is_active'] = true;
        }

        try {
            $module = CatModule::create($data)->fresh();
            return response()->json($module, 201);
        } catch (QueryException $e) {
            Log::error('Failed to create CatModule', ['error' => $e->getMessage()]);
            $message = 'Database constraint violation';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'Slug already exists';
                return response()->json(['message' => $message], 409);
            }
            return response()->json(['message' => $message], 400);
        } catch (\Throwable $e) {
            Log::error('Unexpected error creating CatModule', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Unexpected error',
            ], 500);
        }
    }
}
