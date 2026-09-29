<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $usuarioId = $request->input('usuario_id');
        $modulo = $request->input('modulo');
        $accion = $request->input('accion');

        $usuarios = User::orderBy('name')->get();

        $query = Auditoria::with('usuario')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($fechaDesde) {
            $query->whereDate(
                'created_at',
                '>=',
                $fechaDesde
            );
        }

        if ($fechaHasta) {
            $query->whereDate(
                'created_at',
                '<=',
                $fechaHasta
            );
        }

        if ($usuarioId) {
            $query->where(
                'usuario_id',
                $usuarioId
            );
        }

        if ($modulo) {
            $query->where(
                'modulo',
                $modulo
            );
        }

        if ($accion) {
            $query->where(
                'accion',
                $accion
            );
        }

        $auditorias = $query->get();

        $modulos = Auditoria::query()
            ->select('modulo')
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        $acciones = Auditoria::query()
            ->select('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');

        return view('auditorias.index', compact(
            'auditorias',
            'usuarios',
            'modulos',
            'acciones',
            'fechaDesde',
            'fechaHasta',
            'usuarioId',
            'modulo',
            'accion'
        ));
    }
}
