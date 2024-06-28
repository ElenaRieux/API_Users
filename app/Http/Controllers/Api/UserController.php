<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    // Register (POST)

    public function register(Request $request)
    {
        try {
            // Validazione delle credenziali dell'utente
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|confirmed'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            // Creazione dell'utente
            $user = User::create([
                'uuid' => Uuid::uuid4()->toString(),
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user->roles()->attach(2);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                // 'token' => $user->createToken("API TOKEN")->accessToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Login (POST)

    public function login(Request $request)
    {

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email and Password does not match.',
                ], 401);
            }

            $user = Auth::user();

            $userData = [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toDateTimeString(),
                'updated_at' => $user->updated_at->toDateTimeString(),
                'roles' => $user->roles->pluck('name'),
            ];

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->accessToken,
                'userData' => $userData,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Logout (POST)

    public function logout()
    {
        try {
            auth()->user()->token()->revoke();

            return response()->json([
                'status' => true,
                'message' => 'User logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to logout user: ' . $e->getMessage()
            ], 500);
        }
    }

    // Creare un nuovo user

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'confirmed'],
                'roles' => ['required', 'array', 'exists:roles,name'],
            ]);

            $user = User::create([
                'uuid' => Uuid::uuid4()->toString(),
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Ottieni gli ID dei ruoli basati sui nomi selezionati
            $roles = Role::whereIn('name', $request->input('roles'))->pluck('id')->toArray();

            // Collega l'utente ai ruoli
            $user->roles()->sync($roles);

            return response()->json(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Mostrare user
    public function show($uuid)
    {
        try {
            // Verifica che l'utente possa leggere i dati
            if ($uuid !== Auth::user()->uuid) {
                Gate::authorize('canReadUser');
            }

            // Trova l'utente
            $user = User::where('uuid', $uuid)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Prepara i dati di risposta
            $userData = [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toDateTimeString(),
                'updated_at' => $user->updated_at->toDateTimeString(),
                'roles' => $user->roles->pluck('name'),
            ];

            return response()->json(['message' => 'User retrieved successfully', 'userData' => $userData], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Modificare user

    public function update(Request $request, $uuid)
    {
        try {
            // Verifica che l'utente possa aggiornare i dati
            if ($uuid !== Auth::user()->uuid) {
                Gate::authorize('canUpdateUser');
            }

            // Trova l'utente
            $user = User::where('uuid', $uuid)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Validazione dei dati
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
                    'password' => 'nullable|confirmed',
                    'roles' => ['required', 'array', 'exists:roles,name']
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $validatedData = $validateUser->validated();

            // Aggiorna i dati dell'utente
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            // Verifica se è stata fornita solo la conferma della password senza una nuova password
            if (empty($validatedData['password']) && !empty($request->input('password_confirmation'))) {
                return response()->json(['message' => 'Please enter a new password'], 400);
            }

            // Verifica se è stata fornita una nuova password
            if (!empty($validatedData['password'])) {
                $user->password = bcrypt($validatedData['password']);
            }

            $user->save();

            // Ottieni gli ID dei ruoli basati sui nomi selezionati
            $roles = Role::whereIn('name', $validatedData['roles'])->pluck('id')->toArray();

            // Sostituisce i ruoli esistenti con i nuovi ruoli
            $user->roles()->sync($roles);

            // Prepara i dati di risposta
            $userData = [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toDateTimeString(),
                'updated_at' => $user->updated_at->toDateTimeString(),
                'roles' => $user->roles->pluck('name'),
            ];

            return response()->json(['message' => 'User updated successfully', 'userData' => $userData], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    // Cancellare User

    public function destroy($uuid)
    {
        try {

            if ($uuid !== Auth::user()->uuid) {
                Gate::authorize('canDeleteUser');
            }

            $user = User::where('uuid', $uuid)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->delete();

            return response()->json(['message' => 'User deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user: ' . $e->getMessage()], 500);
        }
    }

    // GET tutti gli users

    public function index()
    {
        try {

            // Recupera tutti gli utenti con i loro ruoli
            $users = User::with('roles')->get();

            // Prepara i dati di risposta
            $userData = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->toDateTimeString(),
                    'updated_at' => $user->updated_at->toDateTimeString(),
                    'roles' => $user->roles->pluck('name'),
                ];
            });

            return response()->json(['message' => 'Users retrieved successfully', 'userData' => $userData], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
