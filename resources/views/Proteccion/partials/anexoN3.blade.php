{{-- Proteccion/partials/anexoN3.blade.php --}}
{{-- El section-header se gestiona ahora desde el padre (verDetalleCaso.blade.php) --}}
<div class="detail-group">
    {{-- Verifica si la colección de Anexos N3 no está vacía --}}
    @if($adulto->anexoN3->isNotEmpty())
        <ul class="item-list">
            @foreach($adulto->anexoN3 as $anexo3Item)
                <li>
                    {{-- Título de la sub-sección con el nombre de la persona natural asociada --}}
                    <div class="sub-section-title">
                        Persona Relacionada: 
                        {{ optional($anexo3Item->personaNatural)->nombres ?? 'N/A' }} 
                        {{ optional($anexo3Item->personaNatural)->primer_apellido ?? '' }}
                        {{ optional($anexo3Item->personaNatural)->segundo_apellido ?? '' }}
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">CI:</span> 
                        <span class="detail-value">{{ optional($anexo3Item->personaNatural)->ci ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sexo:</span> 
                        <span class="detail-value">
                            @if(optional($anexo3Item)->sexo == 'M')
                                Masculino
                            @elseif(optional($anexo3Item)->sexo == 'F')
                                Femenino
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Edad:</span> 
                        <span class="detail-value">{{ optional($anexo3Item->personaNatural)->edad ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Teléfono:</span> 
                        <span class="detail-value">{{ optional($anexo3Item->personaNatural)->telefono ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Dirección:</span> 
                        <span class="detail-value">{{ optional($anexo3Item->personaNatural)->direccion_domicilio ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Parentesco:</span> 
                        <span class="detail-value">{{ optional($anexo3Item->personaNatural)->relacion_parentesco ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Dirección de Trabajo:</span> 
                        <span class="detail-value">{{ optional($anexo3Item->personaNatural)->direccion_de_trabajo ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ocupación:</span> 
                        <span class="detail-value">{{ optional($anexo3Item->personaNatural)->ocupacion ?? 'N/A' }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="no-data-message">No se han registrado personas en Anexo N3.</div>
    @endif
</div>
