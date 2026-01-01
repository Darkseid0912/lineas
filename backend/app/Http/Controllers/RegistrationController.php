<?php

namespace App\Http\Controllers;

use App\Models\CatCompany;
use App\Models\CatUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    /**
     * Atomically create company and its admin user.
     */
    public function companyAdmin(Request $request)
    {
        try {
            $validated = $request->validate([
                // Company fields
                'tax_id' => ['nullable', 'string', 'max:20', Rule::unique('cat_companies', 'tax_id')->whereNull('deleted_at')],
                'business_name' => ['required', 'string', 'max:200'],
                'trade_name' => ['nullable', 'string', 'max:200'],
                'email' => ['required', 'string', 'email', 'max:150'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string'],
                'city' => ['nullable', 'string', 'max:100'],
                'country' => ['nullable', 'string', 'max:100'],
                'status' => ['nullable', 'integer', Rule::in([0, 1])],
                // User fields
                'name' => ['required', 'string', 'max:255'],
                'user_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $result = DB::transaction(function () use ($validated) {
            // Create company
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

            // Ensure Admin role exists for company
            $adminRole = CatUser::firstOrCreate([
                'name' => 'Administrador',
                'id_companies' => $company->id,
            ], [
                'name' => 'Administrador',
                'id_companies' => $company->id,
            ]);

            // Create user linked to company with Admin role
            $user = User::create([
                'name' => trim($validated['name']),
                'email' => trim($validated['user_email']),
                'password' => Hash::make($validated['password']),
                'type' => $adminRole->id,
                'id_companies' => $company->id,
            ]);

            // Create token
            $token = $user->createToken('api')->plainTextToken;

            return compact('company', 'user', 'token');
        });

        return response()->json($result, 201);
    }
}
