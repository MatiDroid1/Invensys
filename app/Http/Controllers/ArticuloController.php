<?php

namespace App\Http\Controllers;

use App\AuditoriaService;
use App\Http\Requests\StoreArticuloRequest;
use App\Http\Requests\UpdateArticuloRequest;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticuloController extends Controller
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    public function index(Request $request)
    {
        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $articulos = Articulo::query()
            ->select('articulos.*')
            ->selectSub(
                Movimiento::selectRaw('
                    COALESCE(
                        SUM(
                            CASE
                                WHEN tipo IN (\'ENTRADA\', \'AJUSTE_POSITIVO\') THEN cantidad
                                WHEN tipo IN (\'SALIDA\', \'AJUSTE_NEGATIVO\') THEN -cantidad
                                ELSE 0
                            END
                        ),
                        0
                    )
                ')
                    ->whereColumn('movimientos.articulo_id', 'articulos.id'),
                'stock_calculado'
            )
            ->with(['categoria', 'unidadMedida'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $termino = trim((string) $request->input('q'));

                $query->where(function ($interno) use ($termino) {
                    $interno->where('codigo', 'like', "%{$termino}%")
                        ->orWhere('nombre', 'like', "%{$termino}%")
                        ->orWhere('descripcion', 'like', "%{$termino}%");
                });
            })
            ->when(
                $request->filled('categoria_id'),
                fn ($query) => $query->where('categoria_id', $request->integer('categoria_id'))
            )
            ->when($request->input('estado') === 'activos', fn ($query) => $query->where('activo', true))
            ->when($request->input('estado') === 'inactivos', fn ($query) => $query->where('activo', false))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('articulos.index', compact('articulos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $unidadesMedida = UnidadMedida::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('articulos.create', compact('categorias', 'unidadesMedida'));
    }

    public function store(StoreArticuloRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            $articulo = Articulo::create($data);

            $this->auditoriaService->registrar(
                modulo: 'articulos',
                accion: 'CREAR',
                modelo: $articulo,
                descripcion: 'Artículo creado.',
                datos: $data,
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('articulos.index')
            ->with('success', 'Artículo creado correctamente.');
    }

    public function show(Articulo $articulo)
    {
        $articulo->load(['categoria', 'unidadMedida']);

        $movimientos = $articulo->movimientos()
            ->with(['persona', 'usuario'])
            ->orderByDesc('fecha_movimiento')
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        $ultimaEntrada = $articulo->movimientos()
            ->whereIn('tipo', ['ENTRADA', 'AJUSTE_POSITIVO'])
            ->max('fecha_movimiento');

        $ultimaSalida = $articulo->movimientos()
            ->whereIn('tipo', ['SALIDA', 'AJUSTE_NEGATIVO'])
            ->max('fecha_movimiento');

        return view('articulos.show', compact(
            'articulo',
            'movimientos',
            'ultimaEntrada',
            'ultimaSalida'
        ));
    }

    public function edit(Articulo $articulo)
    {
        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $unidadesMedida = UnidadMedida::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('articulos.edit', compact(
            'articulo',
            'categorias',
            'unidadesMedida'
        ));
    }

    public function update(UpdateArticuloRequest $request, Articulo $articulo)
    {
        $data = $request->validated();

        $antes = $articulo->only(array_keys($data));

        DB::transaction(function () use ($articulo, $data, $antes, $request) {
            $articulo->update($data);

            $despues = $articulo->getChanges();

            unset($despues['updated_at']);

            $this->auditoriaService->registrar(
                modulo: 'articulos',
                accion: 'ACTUALIZAR',
                modelo: $articulo,
                descripcion: 'Artículo actualizado.',
                datos: [
                    'antes' => $antes,
                    'cambios' => $despues,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('articulos.index')
            ->with('success', 'Artículo actualizado correctamente.');
    }

    public function destroy(Articulo $articulo, Request $request)
    {
        DB::transaction(function () use ($articulo, $request) {
            $articulo->update([
                'activo' => false,
            ]);

            $this->auditoriaService->registrar(
                modulo: 'articulos',
                accion: 'DESACTIVAR',
                modelo: $articulo,
                descripcion: 'Artículo desactivado.',
                datos: [
                    'activo' => false,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('articulos.index')
            ->with('success', 'Artículo desactivado correctamente.');
    }
}
