<?php

namespace App\Http\Controllers;

use App\Models\CatCompany;
use App\Models\CatUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CatCompanyController extends Controller
{
    /**
     * Store a newly created company in catalog.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tax_id' => [
                    'nullable', 'string', 'max:20',
                    Rule::unique('cat_companies', 'tax_id')->whereNull('deleted_at'),
                ],
                'business_name' => ['required', 'string', 'max:200'],
                'trade_name' => ['nullable', 'string', 'max:200'],
                'email' => ['required', 'string', 'email', 'max:150'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string'],
                'city' => ['nullable', 'string', 'max:100'],
                'country' => ['nullable', 'string', 'max:100'],
                'status' => ['nullable', 'integer', Rule::in([0, 1])],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $company = CatCompany::create([
            'tax_id' => $validated['tax_id'] ?? null,
            'business_name' => trim($validated['business_name']),
            'trade_name' => $validated['trade_name'] ?? null,
            'email' => trim($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'status' => $validated['status'] ?? 1,
        ]);

        // Ensure default 'Administrador' role exists for the company
        CatUser::firstOrCreate([
            'name' => 'Administrador',
            'id_companies' => $company->id,
        ], [
            'name' => 'Administrador',
            'id_companies' => $company->id,
        ]);

        return response()->json($company, 201);
    }
}
