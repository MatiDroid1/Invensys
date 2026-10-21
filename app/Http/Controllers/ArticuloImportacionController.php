<?php

namespace App\Http\Controllers;

use App\Enums\Permiso;
use App\Services\AuditoriaService;
use App\Services\ImportacionArticulosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ArticuloImportacionController extends Controller
{
    /**
     * Carpeta de trabajo donde se guardan los archivos a la espera de
     * confirmación.
     */
    private const CARPETA = 'importaciones';

    public function __construct(
        private ImportacionArticulosService $importador,
        private AuditoriaService $auditoriaService
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
        $contenido = $this->contenido($request);

        try {
            $resultado = $this->importador->importar($contenido, $this->permiteCostos());
        } catch (Throwable $e) {
            // La importación es una transacción: si algo falla, no quedó nada
            // a medias. Se avisa y se deja el archivo como estaba.
            report($e);

            return redirect()
                ->route('articulos.importar.create')
                ->with('error', 'No se pudo completar la importación. No se modificó ningún artículo. Revisa el archivo e inténtalo de nuevo.');
        }

        $this->auditar($resultado);

        session()->flash('importacion', $resultado);

        return redirect()->route('articulos.importar.create');
    }

    /**
     * Analiza el archivo y muestra qué se va a crear y qué se va a actualizar,
     * sin tocar el catálogo todavía.
     *
     * Importar en bloque es una operación que puede tocar cientos de
     * artículos: conviene ver el efecto antes de aplicarlo.
     */
    public function previsualizar(Request $request)
    {
        $contenido = $this->contenido($request);

        $plan = $this->importador->analizar($contenido, $this->permiteCostos());

        // El archivo queda guardado hasta que se confirme o se abandone.
        $token = $this->guardarArchivo($contenido);

        $request->session()->put('importacion_previa', $token);

        return view('articulos.importar', [
            'plan' => $plan,
            'token' => $token,
        ]);
    }

    /**
     * Aplica la importación previamente previsualizada.
     */
    public function confirmar(Request $request)
    {
        $datos = $request->validate([
            'token' => ['required', 'string', 'max:64'],
        ]);

        $token = (string) $request->session()->get('importacion_previa');

        if ($token === '' || ! hash_equals($token, (string) $datos['token'])) {
            return redirect()
                ->route('articulos.importar.create')
                ->with('error', 'La previsualización caducó. Vuelve a cargar el archivo.');
        }

        $ruta = self::CARPETA.'/'.$token.'.csv';

        if (! Storage::disk('local')->exists($ruta)) {
            $request->session()->forget('importacion_previa');

            return redirect()
                ->route('articulos.importar.create')
                ->with('error', 'El archivo de la previsualización ya no está disponible. Vuelve a cargarlo.');
        }

        $contenido = Storage::disk('local')->get($ruta);

        try {
            $resultado = $this->importador->importar($contenido, $this->permiteCostos());
        } catch (Throwable $e) {
            // La importación es una transacción: si algo falla, no quedó nada
            // a medias. El archivo se conserva para poder reintentar.
            report($e);

            return redirect()
                ->route('articulos.importar.create')
                ->with('error', 'No se pudo completar la importación. No se modificó ningún artículo. Vuelve a intentarlo.');
        }

        Storage::disk('local')->delete($ruta);
        $request->session()->forget('importacion_previa');

        $this->auditar($resultado);

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

    /**
     * Valida el archivo subido y devuelve su contenido.
     */
    private function contenido(Request $request): string
    {
        $request->validate([
            'archivo' => [
                'required',
                'file',
                'mimes:csv,txt',
                'extensions:csv,txt',
                'max:1024',
            ],
        ], [], [
            'archivo' => 'archivo',
        ]);

        return (string) file_get_contents(
            $request->file('archivo')->getRealPath()
        );
    }

    /**
     * Guarda el archivo a la espera de confirmación y devuelve su token.
     */
    private function guardarArchivo(string $contenido): string
    {
        $token = Str::random(40);

        Storage::disk('local')->put(self::CARPETA.'/'.$token.'.csv', $contenido);

        return $token;
    }

    /**
     * ¿El usuario puede cargar precios de venta?
     */
    private function permiteCostos(): bool
    {
        return auth()->user()?->puede(Permiso::CostosEditar) ?? false;
    }

    /**
     * @param  array{creados: int, actualizados: int, errores: array<int, string>}  $resultado
     */
    private function auditar(array $resultado): void
    {
        $this->auditoriaService->registrar(
            'articulos',
            'IMPORTAR_CSV',
            null,
            'Importación masiva de artículos: '
                .$resultado['creados'].' creados, '
                .$resultado['actualizados'].' actualizados, '
                .count($resultado['errores']).' filas con error.',
            $resultado
        );
    }
}
