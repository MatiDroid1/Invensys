<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">Acerca de Invensys</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="md-card p-6 sm:p-8">
                <div class="flex items-start gap-4">
                    <span class="md-icon-btn shrink-0 bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                        <x-application-logo class="h-6 w-6 fill-current" />
                    </span>

                    <div class="min-w-0">
                        <h3 class="md-section-title">Qué es Invensys</h3>

                        <p class="mt-3 leading-relaxed text-gray-600 dark:text-gray-300">
                            Invensys es el sistema de control de inventario de la empresa. Nació de una
                            necesidad concreta: llevar el control de artículos, entradas, salidas y
                            ajustes en un solo lugar, saber siempre cuánto hay de cada cosa y
                            responder <em>quién</em> hizo cada cambio y <em>cuándo</em>.
                        </p>

                        <p class="mt-3 leading-relaxed text-gray-600 dark:text-gray-300">
                            Antes esto se anotaba en una planilla. La planilla no sabe si dos personas
                            están editando la misma fila al mismo tiempo, no avisa cuando el stock
                            quedó en cero y no deja rastro de quién cambió un número. Invensys existe
                            justamente para cerrar esas tres brechas.
                        </p>
                    </div>
                </div>
            </section>

            <section class="md-card p-6 sm:p-8">
                <h3 class="md-section-title">Qué resuelve</h3>

                <dl class="mt-5 space-y-5">
                    <div class="flex gap-4">
                        <dt class="md-icon-btn shrink-0 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                            1
                        </dt>

                        <dd class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-gray-100">Una sola cifra de stock, siempre al día</p>

                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                El stock no se edita a mano. Cambia únicamente cuando alguien registra una
                                entrada, una salida o un ajuste, y esos movimientos quedan anotados uno a uno.
                                No hay dos versiones de la verdad.
                            </p>
                        </dd>
                    </div>

                    <div class="flex gap-4">
                        <dt class="md-icon-btn shrink-0 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                            2
                        </dt>

                        <dd class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-gray-100">Trazabilidad de cada cambio</p>

                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                Todo movimiento registra el artículo, la cantidad, la fecha, el tipo y la
                                persona responsable. La auditoría guarda además qué se editó y qué antes.
                                Si hay que reconstruir un número, está.
                            </p>
                        </dd>
                    </div>

                    <div class="flex gap-4">
                        <dt class="md-icon-btn shrink-0 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                            3
                        </dt>

                        <dd class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-gray-100">Historial por artículo (kardex)</p>

                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                El kardex muestra, para un artículo, la línea de tiempo de entradas,
                                salidas y saldos. Es la respuesta directa a "¿en qué se me fue eso?".
                            </p>
                        </dd>
                    </div>

                    <div class="flex gap-4">
                        <dt class="md-icon-btn shrink-0 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                            4
                        </dt>

                        <dd class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-gray-100">Alertas y comunicación interna</p>

                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                El sistema avisa cuando un artículo queda bajo su stock mínimo, y los
                                usuarios pueden escribirse entre sí sin salir de la aplicación.
                            </p>
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="md-card p-6 sm:p-8">
                <h3 class="md-section-title">Cómo está construido</h3>

                <p class="mt-3 leading-relaxed text-gray-600 dark:text-gray-400">
                    Invensys sigue el patrón <strong class="text-gray-900 dark:text-gray-100">MVC</strong>:
                    los modelos guardan los datos y las reglas de negocio, los controladores atienden
                    las peticiones web y las vistas pintan el resultado. Sobre ese esquema hay una capa
                    de servicios para las operaciones críticas — registrar un movimiento no es un
                    simple <code class="rounded bg-gray-100 px-1 py-0.5 text-sm dark:bg-gray-700">INSERT</code>, ajusta existencias y anota la
                    auditoría — y FormRequests para validar la entrada antes de que llegue al modelo.
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-700/70">
                        <p class="font-medium text-gray-900 dark:text-gray-100">Laravel 12</p>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            PHP 8, Eloquent y Blade. MySQL como base de datos.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-700/70">
                        <p class="font-medium text-gray-900 dark:text-gray-100">Tailwind + Alpine</p>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Interfaz sin recargas, con modo claro y oscuro.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-700/70">
                        <p class="font-medium text-gray-900 dark:text-gray-100">Roles y auditoría</p>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Administradores gestionan usuarios y configuración; el resto opera el inventario.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-700/70">
                        <p class="font-medium text-gray-900 dark:text-gray-100">Pruebas automáticas</p>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            La suite verifica inventario, mensajería, permisos y vistas antes de cada entrega.
                        </p>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-amber-200 bg-amber-50 p-6 dark:border-amber-800/70 dark:bg-amber-500/10 sm:p-8">
                <h3 class="md-section-title text-amber-900 dark:text-amber-200">Alcance y límites</h3>

                <p class="mt-3 leading-relaxed text-amber-800 dark:text-amber-300">
                    Invensys cubre inventario y comunicación interna.
                    <strong class="font-semibold">No es un sistema contable ni financiero</strong>:
                    no emite facturas, no calcula impuestos ni reemplaza la contabilidad de la
                    empresa. Tampoco es un ERP ni gestiona compras a proveedores; el kardex y los
                    reportes son operacionales.
                </p>

                <p class="mt-3 leading-relaxed text-amber-800 dark:text-amber-300">
                    Las cuentas se crean desde el módulo de administración: no hay registro público,
                    quien entra es alguien dado de alta por un administrador.
                </p>
            </section>

            <section class="md-card p-6 sm:p-8">
                <h3 class="md-section-title">Equipo</h3>

                <p class="mt-3 leading-relaxed text-gray-600 dark:text-gray-400">
                    Invensys es mantenido internamente por el equipo de soporte. Las incidencias,
                    solicitudes de mejora y dudas sobre el uso del sistema se canalizan por el
                    formulario de
                    <a href="{{ route('contacto.create') }}" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">contacto</a>.
                </p>
            </section>

            <div class="flex justify-end">
                <a href="{{ route('dashboard') }}" class="md-btn md-btn-md md-btn-filled">
                    Volver al panel
                </a>
            </div>
        </div>
    </div>
</x-app-layout>