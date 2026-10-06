<?php

namespace App\Http\Controllers;

use App\AuditoriaService;
use App\Services\ImportacionArticulosService;
use Illuminate\Http\Request;

class ArticuloImportacionController extends Controller
{
    public function __construct(
        private ImportacionArticulosService $importador
    ) {}

    /**
     * Formulario de importación masiva.
     */
    public function create()
    {
        return view('articulos.importar');
    }

    /**
     * Procesa el archivo CSV subido.
     */
    public function store(Request $request)
    {
        $request->validate([
            'archivo' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:1024',
            ],
        ]);

        $contenido = file_get_contents(
            $request->file('archivo')->getRealPath()
        );

        $resultado = $this->importador->importar($contenido);

        app(AuditoriaService::class)->registrar(
            'articulos',
            'IMPORTAR_CSV',
            null,
            'Importación masiva de artículos: '
                .$resultado['creados'].' creados, '
                .$resultado['actualizados'].' actualizados, '
                .count($resultado['errores']).' filas con error.',
            $resultado
        );

        session()->flash('importacion', $resultado);

        return redirect()->route('articulos.importar.create');
    }

    /**
     * Descarga la plantilla CSV en blanco.
     */
    public function plantilla()
    {
        $nombreArchivo = 'plantilla_articulos_'.now()->format('Y-m-d').'.csv';

        return response($this->importador->plantilla(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ]);
    }
}
