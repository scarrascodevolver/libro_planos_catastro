<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isRegistro()) {
            abort(403, 'No tienes permisos para gestionar usuarios');
        }

        $usuarios = User::orderBy('created_at', 'desc')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isRegistro()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:consulta,registro'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'initial_password' => $request->password,
            'role' => $request->role
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente'
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->isRegistro()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:consulta,registro'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ]);

        // Solo actualizar password si se proporciona
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
                'initial_password' => $request->password
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente'
        ]);
    }

    public function destroy($id)
    {
        if (!auth()->user()->isRegistro()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        // No permitir eliminar el propio usuario
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }
}
