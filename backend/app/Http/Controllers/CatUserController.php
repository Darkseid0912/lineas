<?php

namespace App\Http\Controllers;

use App\Models\CatUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CatUserController extends Controller
{
    /**
     * Return paginated list of catalog user types (10 per page).
     */
    public function index(Request $request)
    {
        $companyId = $request->input('id_companies') ?? optional($request->user())->id_companies;
        $query = CatUser::query();
        if ($companyId) {
            $query->where('id_companies', $companyId);
        }
        // Excluir roles cuyo nombre sea "Administrador"
        $query->where('name', '<>', 'Administrador');
        $paginated = $query->orderByDesc('id')->paginate(10);

        return response()->json($paginated);
    }

    /**
     * Store a newly created catalog user type.
     */
    public function store(Request $request)
    {
        // If client didn't send id_companies, default to authenticated user's company
        $companyId = $request->input('id_companies') ?? optional($request->user())->id_companies;
        if ($companyId) {
            $request->merge(['id_companies' => $companyId]);
        }
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::notIn(['Administrador']),
                    Rule::unique('cat_users', 'name')
                        ->whereNull('deleted_at')
                        ->where(function ($q) use ($request) {
                            $q->where('id_companies', $request->input('id_companies'));
                        }),
                ],
                'id_companies' => ['required', 'integer', 'exists:cat_companies,id'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $catUser = CatUser::create([
            'name' => trim($validated['name']),
            'id_companies' => $validated['id_companies'],
        ]);

        return response()->json($catUser, 201);
    }
}
