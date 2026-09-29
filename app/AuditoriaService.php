<?php

namespace App;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;

class AuditoriaService
{
    public function registrar(
        string $modulo,
        string $accion,
        ?Model $modelo = null,
        ?string $descripcion = null,
        ?array $datos = null,
        ?int $usuarioId = null
    ): Auditoria {
        return Auditoria::create([
            'usuario_id' => $usuarioId ?? auth()->id(),
            'modulo' => $modulo,
            'accion' => $accion,
            'modelo' => $modelo ? $modelo::class : null,
            'modelo_id' => $modelo?->getKey(),
            'descripcion' => $descripcion,
            'datos' => $datos,
        ]);
    }
}
