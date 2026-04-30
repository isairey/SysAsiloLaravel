{{-- resources/views/Admin/gestionarAdultoMayor/partials/tabla-adultos.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped text-nowrap">
        <thead class="bg-success-light">
            <tr>
                <th>CI</th>
                <th>Nombre Completo</th>
                <th>Edad</th>
                <th>Sexo</th>
                <th>Teléfono</th>
                <th>Domicilio</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($adultosMayores as $adulto)
                <tr>
                    <td>{{ $adulto->ci }}</td>
                    <td>{{ $adulto->nombres }} {{ $adulto->primer_apellido }} {{ $adulto->segundo_apellido }}</td>
                    <td>{{ $adulto->edad }} años</td>
                    <td>
                        @if($adulto->sexo == 'M')
                            <span class="badge bg-primary">Masculino</span>
                        @elseif($adulto->sexo == 'F')
                            <span class="badge bg-pink">Femenino</span>
                        @else
                            <span class="badge bg-warning">Otro</span>
                        @endif
                    </td>
                    <td>{{ $adulto->telefono }}</td>
                    <td>{{ $adulto->domicilio }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group" aria-label="Acciones de registro">
                            <a href="{{ route('gestionar-adultomayor.editar', $adulto->ci) }}" class="btn btn-sm btn-primary btn-action" data-bs-toggle="tooltip" title="Editar">
                                <i class="fe fe-edit"></i>
                            </a>

                            {{-- ========================================================================= --}}
                            {{-- === INICIO: CAMBIO DE SEGURIDAD POR ROL === --}}
                            {{-- ========================================================================= --}}
                            {{-- El botón de eliminar solo se muestra si el usuario es 'admin' --}}
                            @if(optional(Auth::user()->rol)->nombre_rol === 'admin')
                                <button type="button" class="btn btn-sm btn-danger btn-action btn-eliminar" 
                                        data-ci="{{ $adulto->ci }}"
                                        data-nombre="{{ $adulto->nombres }} {{ $adulto->primer_apellido }}"
                                        data-bs-toggle="tooltip" title="Eliminar">
                                    <i class="fe fe-trash-2"></i>
                                </button>
                            @endif
                            {{-- ========================================================================= --}}
                            {{-- === FIN: CAMBIO DE SEGURIDAD POR ROL === --}}
                            {{-- ========================================================================= --}}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">
                        <i class="fe fe-info me-2"></i>No se encontraron registros de adultos mayores.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="d-flex justify-content-center mt-3">
    @if ($adultosMayores->hasPages())
        {{ $adultosMayores->links() }}
    @endif
</div>
