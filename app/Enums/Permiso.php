<?php

namespace App\Enums;

/**
 * Vocabulario de permisos del sistema.
 *
 * Cada operación protegida del backend se asocia a uno de estos permisos.
 * Los permisos son granulares y atómicos: la idea es poder decidir quién hace
 * qué sin depender de un único chequeo de "es administrador".
 *
 * El mapa por defecto de cada rol vive en `App\Services\Permisos`. En la
 * Fase 3 ese mapa pasa a depender de la organización activa y de las
 * anomalías guardadas en `organizacion_permisos`.
 */
enum Permiso: string
{
    // Catálogo
    case ArticulosVer = 'articulos.ver';
    case ArticulosCrear = 'articulos.crear';
    case ArticulosEditar = 'articulos.editar';
    case ArticulosEliminar = 'articulos.eliminar';

    // Movimientos
    case MovimientosVer = 'movimientos.ver';
    case MovimientosEntrada = 'movimientos.entrada';
    case MovimientosSalida = 'movimientos.salida';
    case MovimientosAjuste = 'movimientos.ajuste';
    case MovimientosAnular = 'movimientos.anular';

    // Personas (destinatarios de entregas)
    case PersonasVer = 'personas.ver';
    case PersonasCrear = 'personas.crear';
    case PersonasEditar = 'personas.editar';
    case PersonasEliminar = 'personas.eliminar';

    // Catálogos auxiliares
    case CategoriasGestionar = 'categorias.gestionar';
    case UnidadesGestionar = 'unidades.gestionar';

    // Compras y proveedores
    case ProveedoresGestionar = 'proveedores.gestionar';
    case ComprasGestionar = 'compras.gestionar';

    // Costos y valorización
    case CostosVer = 'costos.ver';
    case CostosEditar = 'costos.editar';

    // Administración
    case UsuariosGestionar = 'usuarios.gestionar';
    case ConfiguracionVer = 'configuracion.ver';
    case ConfiguracionEditar = 'configuracion.editar';
    case AuditoriaVer = 'auditoria.ver';

    // Transferencia de datos
    case Exportar = 'exportar';
    case Importar = 'importar';

    // Mensajería interna
    case MensajeriaUsar = 'mensajeria.usar';

    /**
     * Etiqueta legible para la interfaz.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::ArticulosVer => 'Ver artículos',
            self::ArticulosCrear => 'Crear artículos',
            self::ArticulosEditar => 'Editar artículos',
            self::ArticulosEliminar => 'Desactivar artículos',
            self::MovimientosVer => 'Ver movimientos',
            self::MovimientosEntrada => 'Registrar entradas',
            self::MovimientosSalida => 'Registrar salidas',
            self::MovimientosAjuste => 'Registrar ajustes',
            self::MovimientosAnular => 'Anular movimientos',
            self::PersonasVer => 'Ver personas',
            self::PersonasCrear => 'Crear personas',
            self::PersonasEditar => 'Editar personas',
            self::PersonasEliminar => 'Desactivar personas',
            self::CategoriasGestionar => 'Gestionar categorías',
            self::UnidadesGestionar => 'Gestionar unidades de medida',
            self::ProveedoresGestionar => 'Gestionar proveedores',
            self::ComprasGestionar => 'Gestionar compras',
            self::CostosVer => 'Consultar costos',
            self::CostosEditar => 'Modificar costos',
            self::UsuariosGestionar => 'Administrar usuarios',
            self::ConfiguracionVer => 'Ver configuración',
            self::ConfiguracionEditar => 'Modificar configuración',
            self::AuditoriaVer => 'Consultar auditoría',
            self::Exportar => 'Exportar información',
            self::Importar => 'Importar información',
            self::MensajeriaUsar => 'Usar la mensajería',
        };
    }
}
