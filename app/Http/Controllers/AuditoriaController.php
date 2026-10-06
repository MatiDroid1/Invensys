<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportaCsv;
use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    use ExportaCsv;

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
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        $acciones = Auditoria::query()
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

    public function indexCsv(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $usuarioId = $request->input('usuario_id');
        $modulo = $request->input('modulo');
        $accion = $request->input('accion');

        $query = Auditoria::with('usuario')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }

        if ($usuarioId) {
            $query->where('usuario_id', $usuarioId);
        }

        if ($modulo) {
            $query->where('modulo', $modulo);
        }

        if ($accion) {
            $query->where('accion', $accion);
        }

        $auditorias = $query->get();

        $nombreArchivo = 'auditoria_'.now()->format('Y-m-d_H-i-s').'.csv';

        $lineas = [
            [
                'Fecha',
                'Hora',
                'Usuario',
                'Módulo',
                'Acción',
                'Modelo',
                'Modelo ID',
                'Descripción',
                'Datos',
            ],
        ];

        foreach ($auditorias as $auditoria) {
            $fecha = $auditoria->created_at;

            $lineas[] = [
                $fecha?->format('d/m/Y'),
                $fecha?->format('H:i:s'),
                $auditoria->usuario?->name ?? 'Sistema',
                $auditoria->modulo,
                $auditoria->accion,
                $auditoria->modelo,
                $auditoria->modelo_id,
                $auditoria->descripcion,
                $auditoria->datos ? json_encode($auditoria->datos, JSON_UNESCAPED_UNICODE) : null,
            ];
        }

        return $this->respuestaCsv($lineas, $nombreArchivo);
    }
}
