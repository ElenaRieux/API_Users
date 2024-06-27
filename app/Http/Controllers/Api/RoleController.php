<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Recupera tutti i ruoli con i relativi permessi
            $roles = Role::with('permissions')->get();

            // Prepara la risposta JSON
            $rolesData = $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'uuid' => $role->uuid,
                    'name' => $role->name,
                    'description' => $role->description,
                    'created_at' => $role->created_at->toDateTimeString(),
                    'updated_at' => $role->updated_at->toDateTimeString(),
                    'permissions' => $role->permissions->pluck('name'),
                ];
            });

            return response()->json(['status' => true, 'roles' => $rolesData], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validateRole = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'unique:roles,name'],
                    'description' => 'nullable',
                    'permissions' => ['nullable', 'array', 'exists:permissions,name']
                ]
            );

            if ($validateRole->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateRole->errors()
                ], 401);
            }

            $validatedData = $validateRole->validated();

            // Creazione del ruolo
            $role = new Role();
            $role->name = $validatedData['name'];
            $role->description = $validatedData['description'];
            $role->uuid = Uuid::uuid4()->toString();
            $role->save();

            if (isset($validatedData['permissions'])) {
                $permissions = Permission::whereIn('name', $validatedData['permissions'])->get();
                $role->permissions()->sync($permissions);
                return response()->json(['message' => 'Role created successfully', 'role' => $role, 'permissions' => $permissions], 201);
            }
            return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        try {

            $role = Role::where('uuid', $uuid)->first();

            if (!$role) {
                return response()->json(['status' => false, 'message' => 'Role not found'], 404);
            }

            // Carica i permessi del ruolo
            $role->load('permissions');

            // Prepara i dati del ruolo per la risposta JSON
            $roleData = [
                'id' => $role->id,
                'uuid' => $role->uuid,
                'name' => $role->name,
                'description' => $role->description,
                'created_at' => $role->created_at->toDateTimeString(),
                'updated_at' => $role->updated_at->toDateTimeString(),
                'permissions' => $role->permissions->pluck('name'),
            ];

            return response()->json(['status' => true, 'role' => $roleData], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, $uuid)
    {
        try {
            $role = Role::where('uuid', $uuid)->first();

            // Verifica che il ruolo esista
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }

            // Validazione dei dati
            $validateRole = Validator::make(
                $request->all(),
                [
                    'name' => ['required', Rule::unique('roles')->ignore($role->id)],
                    'description' => 'nullable',
                    'permissions' => ['required', 'array', 'exists:permissions,name']
                ]
            );

            if ($validateRole->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateRole->errors()
                ], 401);
            }

            $validatedData = $validateRole->validated();

            // Aggiorna il ruolo con i dati validati
            $role->name = $validatedData['name'];
            $role->description = $validatedData['description'];
            $role->save();

            // Sincronizza i permessi del ruolo, se forniti
            if (isset($validatedData['permissions'])) {
                $permissions = Permission::whereIn('name', $validatedData['permissions'])->get();
                $role->permissions()->sync($permissions);

                return response()->json(['message' => 'Role updated successfully', 'role' => $role, 'permissions' => $permissions], 200);
            }

            return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        try {
            
            $role = Role::where('uuid', $uuid)->first();

            // Verifica che il ruolo esista
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }

            // Elimina il ruolo
            $role->delete();

            return response()->json(['message' => 'Role deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
