<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CatUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 422);
        }

        // Validate company status: only allow login if company is active (status = 1)
        $user->load('company');
        $company = $user->company;
        if ($company && (int)($company->status ?? 0) !== 1) {
            return response()->json(['message' => 'La compañía no está activa'], 422);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        // Ensure relations needed in response are loaded
        $user->load('typeCatalog', 'company');
        $companyTradeName = optional($user->company)->trade_name;

        // Build modules + permission keys for this user/role
        $roleName = optional($user->typeCatalog)->name;
        $isAdmin = ($roleName === 'Administrador');
        $modulesPermissions = [];

        if (!$isAdmin) {
            $query = DB::table('rel_role_permissions as rrp')
                ->join('cat_modules as cm', 'cm.id', '=', 'rrp.id_module')
                ->join('cat_permissions as cp', 'cp.id', '=', 'rrp.id_permission')
                ->where('cm.is_active', 1)
                ->where('rrp.id_role', (int)($user->type))
                ->select(['cm.module_name', 'cp.key']);

            $rows = $query->get();

            // Aggregate keys per module_name
            $grouped = [];
            foreach ($rows as $r) {
                $m = $r->module_name;
                if (!isset($grouped[$m])) {
                    $grouped[$m] = [];
                }
                $grouped[$m][] = $r->key;
            }
            foreach ($grouped as $moduleName => $keys) {
                $modulesPermissions[] = [
                    'module_name' => $moduleName,
                    'keypermissions' => array_values(array_unique($keys)),
                ];
            }
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
            'type_text' => optional($user->typeCatalog)->name,
            'company_trade_name' => $companyTradeName,
            'company_id' => $user->id_companies,
            'role_name' => $roleName,
            'modules_permissions' => $modulesPermissions,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->load('typeCatalog', 'company');
        }
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'type' => $user->type,
            'type_catalog' => $user->typeCatalog,
            'type_text' => optional($user->typeCatalog)->name,
            'company' => $user->company,
            'company_trade_name' => optional($user->company)->trade_name,
            'company_id' => $user->id_companies,
        ]);
    }
}
