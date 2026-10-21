<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use App\Support\OrganizacionActual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrganizacionController extends Controller
{
    public function crear(): View
    {
        $user = Auth::user();

        $tieneMembresia = DB::table('organizacion_usuario')
            ->where('user_id', $user->id)
            ->exists();

        if ($tieneMembresia && OrganizacionActual::id()) {
            return view('organizaciones.crear', [
                'modo' => 'agregar',
            ]);
        }

        return view('organizaciones.crear', [
            'modo' => 'onboarding',
        ]);
    }

    public function guardar(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'razon_social' => ['nullable', 'string', 'max:150'],
            'rut' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $slug = str($validated['nombre'])->slug()->limit(60)->toString();

        if (Organizacion::where('slug', $slug)->exists()) {
            $slug = $slug.'-'.str()->random(4);
        }

        $tieneMembresia = DB::table('organizacion_usuario')
            ->where('user_id', $user->id)
            ->exists();

        $organizacion = DB::transaction(function () use ($validated, $slug, $user, $tieneMembresia) {
            $organizacion = Organizacion::create([
                'nombre' => $validated['nombre'],
                'slug' => $slug,
                'razon_social' => $validated['razon_social'] ?? null,
                'rut' => $validated['rut'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'] ?? null,
                'perfil' => 'GENERAL',
                'activo' => true,
            ]);

            $esPrimera = ! $tieneMembresia;

            DB::table('organizacion_usuario')->insert([
                'organizacion_id' => $organizacion->id,
                'user_id' => $user->id,
                'rol' => $esPrimera ? 'propietario' : 'propietario',
                'predeterminada' => $esPrimera,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $organizacion;
        });

        OrganizacionActual::set((int) $organizacion->id);

        if (! $tieneMembresia) {
            return redirect()->route('dashboard')->with('status', '¡Empresa registrada correctamente! Ya puedes empezar a usar tu inventario.');
        }

        return redirect()->route('dashboard')->with('status', 'Empresa agregada correctamente.');
    }

    public function seleccionar(int $organizacion): RedirectResponse
    {
        $user = Auth::user();

        $esMiembro = DB::table('organizacion_usuario')
            ->where('user_id', $user->id)
            ->where('organizacion_id', $organizacion)
            ->exists();

        if (! $esMiembro) {
            abort(403, 'No tienes acceso a esta organización.');
        }

        DB::table('organizacion_usuario')
            ->where('user_id', $user->id)
            ->update(['predeterminada' => false]);

        DB::table('organizacion_usuario')
            ->where('user_id', $user->id)
            ->where('organizacion_id', $organizacion)
            ->update(['predeterminada' => true]);

        OrganizacionActual::set($organizacion);

        return redirect()->back()->with('status', 'Organización cambiada correctamente.');
    }

    public function editar(): View
    {
        $organizacionId = OrganizacionActual::id();
        $organizacion = Organizacion::findOrFail($organizacionId);

        return view('organizaciones.editar', [
            'organizacion' => $organizacion,
        ]);
    }

    public function actualizar(Request $request): RedirectResponse
    {
        $organizacionId = OrganizacionActual::id();
        $organizacion = Organizacion::findOrFail($organizacionId);

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'razon_social' => ['nullable', 'string', 'max:150'],
            'rut' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $organizacion->update($validated);

        return redirect()->route('organizaciones.editar')->with('status', 'Datos de la empresa actualizados correctamente.');
    }
}
