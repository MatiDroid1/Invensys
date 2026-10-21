<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // El historial de inventario es inmutable: un usuario con movimientos
        // no puede borrarse porque la clave foránea de `movimientos` lo impide.
        // Se comprueba antes de cerrar la sesión para no dejar al usuario fuera
        // y, a la vez, evitar un error 500 al intentar el DELETE.
        if ($user->movimientos()->exists()) {
            return Redirect::route('profile.edit')->with(
                'error',
                'No se puede eliminar la cuenta porque tiene movimientos registrados. '
                .'Solicita a un administrador que la desactive.'
            );
        }

        // El logout debe ir antes del borrado: al cerrar la sesión Laravel rota
        // el token "recordarme" y guarda el modelo. Si el usuario ya no
        // existiera, ese guardado lo volvería a insertar.
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
