@php
    $modoEdicion = $modoEdicion ?? false;
    $idAdulto = $adulto->id_adulto ?? null;
    $activeTab = session('active_tab', 'historia');
@endphp

@extends('layouts.main')

@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $modoEdicion ? 'Editar' : 'Registrar' }} Historia Clínica</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/main-historia-clinica.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/tabs/historiaclinica.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/tabs/examencomplementario.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .examenes-form-section {
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
        }
        .examenes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .examenes-grid label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .examenes-grid input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .medicamentos-table-container {
            margin-top: 30px;
            overflow-x: auto;
        }
        .medicamentos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .medicamentos-table th,
        .medicamentos-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .medicamentos-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .medicamentos-table input[type="text"] {
            width: calc(100% - 16px);
            padding: 4px;
            border: 1px solid #eee;
            border-radius: 3px;
        }
        .add-row-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
        .remove-row-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
        }
        .nav-tabs {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
            border-bottom: 2px solid #ddd;
            display: flex;
        }
        .nav-item {
            margin-right: 10px;
        }
        .nav-item .nav-link {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #555;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
            transition: background-color 0.3s ease;
        }
        .nav-item .nav-link:hover,
        .nav-item .nav-link:focus {
            background-color: #e0e0e0;
        }
        .nav-item .nav-link.active {
            background-color: #fff;
            border-color: #ddd;
            border-bottom: 2px solid #fff;
            color: #333;
            font-weight: bold;
        }
        .nav-item .nav-link.disabled {
            background-color: #f8f9fa;
            color: #b0b0b0;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .tab-content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            background-color: #fff;
        }
        .tab-content > .tab-pane {
            display: none;
        }
        .tab-content > .tab-pane.active {
            display: block;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-info {
            background-color: #17a2b8;
            color: white;
        }
        .btn-info:hover {
            background-color: #138496;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            text-align: center;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-warning {
            color: #8a6d3b;
            background-color: #fcf8e3;
            border-color: #faebcc;
        }
        .text-center {
            text-align: center;
        }
        .ms-2 {
            margin-left: 0.5rem;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>

    <h1 class="page-title">{{ $modoEdicion ? 'Editar' : 'Registrar' }} Historia Clínica para: {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</h1>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: '<p>{{ session('error') }}</p>',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Datos del Adulto Mayor</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Nombre Completo:</strong> {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}
                        </div>
                        <div class="col-md-4">
                            <strong>CI:</strong> {{ optional($adulto->persona)->ci }}
                        </div>
                        <div class="col-md-4">
                            <strong>Edad:</strong> {{ optional($adulto->persona)->edad }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul id="formTabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab == 'historia' ? 'active' : '' }}" id="historia-tab" href="{{ $modoEdicion ? route('responsable.enfermeria.medico.historia_clinica.edit', ['id_historia' => $historiaClinica->id_historia, 'active_tab' => 'historia']) : route('responsable.enfermeria.medico.historia_clinica.register', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'historia']) }}">
                                1. Historia Clínica
                            </a>
                        </li>
                        <li class="nav-item">
                            @if (($modoEdicion == false && isset($historiaClinica->id_historia)) || ($modoEdicion == true && isset($historiaClinica->id_historia)))
                                <a class="nav-link {{ $activeTab == 'examenes' ? 'active' : '' }}" id="examenes-tab" href="{{ route('responsable.enfermeria.medico.historia_clinica.edit', ['id_historia' => $historiaClinica->id_historia, 'active_tab' => 'examenes']) }}">
                                    2. Exámenes Complementarios
                                </a>
                            @else
                                <button class="nav-link disabled" id="examenes-tab" type="button" disabled>
                                    2. Exámenes Complementarios
                                </button>
                            @endif
                        </li>
                    </ul>

                    <div class="tab-content" id="historiaClinicaTabsContent">
                        <div class="tab-pane {{ $activeTab == 'historia' ? 'active' : '' }}" id="historia" role="tabpanel" aria-labelledby="historia-tab">
                            <form id="historia-form" action="{{ $modoEdicion ? route('responsable.enfermeria.medico.historia_clinica.updateHistoria', ['id_historia' => $historiaClinica->id_historia]) : route('responsable.enfermeria.medico.historia_clinica.storeHistoria', ['id_adulto' => $adulto->id_adulto]) }}" method="POST">
                                @csrf
                                @if($modoEdicion)
                                    @method('PUT')
                                @endif
                                <input type="hidden" name="active_tab_on_submit" value="historia">
                                @include('Medico.tabs.historiaclinica', compact('adulto', 'historiaClinica'))
                                <div class="mt-4 text-center">
                                    <button type="submit" class="btn btn-primary">Siguiente →</button>
                                    <a href="{{ route('responsable.enfermeria.medico.historia_clinica.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane {{ $activeTab == 'examenes' ? 'active' : '' }}" id="examenes" role="tabpanel" aria-labelledby="examenes-tab">
                            @if ($modoEdicion && isset($historiaClinica->id_historia))
                                <form id="examenes-form" action="{{ route('responsable.enfermeria.medico.historia_clinica.storeExamenes', ['id_historia' => $historiaClinica->id_historia]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="active_tab_on_submit" value="examenes">
                                    @include('Medico.tabs.examencomplementario', compact('examenesComplementarios', 'medicamentosRecetados'))
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('responsable.enfermeria.medico.historia_clinica.edit', ['id_historia' => $historiaClinica->id_historia, 'active_tab' => 'historia']) }}" class="btn btn-info ms-2">← Anterior</a>
                                        <button type="submit" class="btn btn-success">Guardar y Finalizar</button>
                                        <a href="{{ route('responsable.enfermeria.medico.historia_clinica.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-warning text-center" role="alert">
                                    Debes registrar y guardar la "Historia Clínica" primero para poder acceder a los "Exámenes Complementarios".
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Script de registrarHistoriaClinica.blade.php inicializado.');

        // Inicializar Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Validaciones con SweetAlert2
        const forms = document.querySelectorAll('#historia-form, #examenes-form');
        forms.forEach(form => {
            form.addEventListener('submit', function (event) {
                let errors = [];
                const activeTab = form.querySelector('input[name="active_tab_on_submit"]').value;

                // Validaciones para Pestaña 1: Historia Clínica
                if (activeTab === 'historia') {
                    // Campos obligatorios
                    const requiredFields = [
                        { id: 'municipio_nombre', label: 'Municipio', maxLength: 255 },
                        { id: 'establecimiento', label: 'Establecimiento', maxLength: 255 },
                        { id: 'antecedentes_personales', label: 'Antecedentes Personales', maxLength: 1000 },
                        { id: 'estado_actual', label: 'Estado Actual', maxLength: 1000 }
                    ];
                    requiredFields.forEach(field => {
                        const input = document.getElementById(field.id);
                        const value = input.value.trim();
                        if (value !== '' && value !== '-') {
                            if (!/^[a-zA-Z0-9\s-]+$/.test(value)) {
                                errors.push(`El campo "${field.label}" solo puede contener letras, números, espacios y guiones.`);
                            }
                            if (value.length > field.maxLength) {
                                errors.push(`El campo "${field.label}" no debe exceder los ${field.maxLength} caracteres.`);
                            }
                        }
                    });

                    // Tipo de Consulta (radio button)
                    const tipoConsulta = form.querySelector('input[name="tipo_consulta"]:checked');
                    if (!tipoConsulta && !form.querySelector('input[name="tipo_consulta"][value="-"]')) {
                        errors.push('Debe seleccionar un "Tipo de Consulta" (N o R) o usar "-".');
                    }

                    // Campos opcionales con límites
                    const optionalFields = [
                        { id: 'ocupacion', label: 'Ocupación', maxLength: 255 },
                        { id: 'domicilio_actual', label: 'Domicilio Actual', maxLength: 255 },
                        { id: 'grado_instruccion', label: 'Grado de Instrucción', maxLength: 100 },
                        { id: 'lugar_nacimiento_provincia', label: 'Lugar de Nacimiento (Provincia)', maxLength: 100 },
                        { id: 'lugar_nacimiento_departamento', label: 'Lugar de Nacimiento (Departamento)', maxLength: 100 },
                        { id: 'antecedentes_familiares', label: 'Antecedentes Familiares', maxLength: 1000 }
                    ];
                    optionalFields.forEach(field => {
                        const input = document.getElementById(field.id);
                        const value = input.value.trim();
                        if (value !== '' && value !== '-') {
                            if (!/^[a-zA-Z0-9\s-]+$/.test(value)) {
                                errors.push(`El campo "${field.label}" solo puede contener letras, números, espacios y guiones.`);
                            }
                            if (value.length > field.maxLength) {
                                errors.push(`El campo "${field.label}" no debe exceder los ${field.maxLength} caracteres.`);
                            }
                        }
                    });
                }

                // Validaciones para Pestaña 2: Exámenes Complementarios
                if (activeTab === 'examenes') {
                    // Campos opcionales con formato
                    const examFields = [
                        { id: 'presion_arterial', label: 'Presión Arterial', regex: /^\d{1,3}\/\d{1,3}$/, example: '120/80', maxLength: 255 },
                        { id: 'temperatura', label: 'Temperatura', regex: /^\d{1,2}(\.\d{1,2})?$/, example: '36.5', maxLength: 255 },
                        { id: 'peso_corporal', label: 'Peso Corporal', regex: /^\d{1,3}(\.\d{1,2})?$/, example: '70.5', maxLength: 255 },
                        { id: 'resultado_prueba', label: 'Resultado de la Prueba', maxLength: 255 },
                        { id: 'diagnostico', label: 'Diagnóstico', maxLength: 255 }
                    ];
                    examFields.forEach(field => {
                        const input = document.getElementById(field.id);
                        const value = input.value.trim();
                        if (value !== '' && value !== '-') {
                            if (field.regex && !field.regex.test(value)) {
                                errors.push(`El campo "${field.label}" debe tener el formato correcto (ejemplo: ${field.example}).`);
                            }
                            if (field.maxLength && value.length > field.maxLength) {
                                errors.push(`El campo "${field.label}" no debe exceder los ${field.maxLength} caracteres.`);
                            }
                        } else if (value === '-' && field.regex) {
                            // Permitir "-" sin validar formato
                        }
                    });

                    // Validar tabla de medicamentos
                    const medicamentosRows = document.querySelectorAll('#medicamentosTable tbody .medicamento-row');
                    if (medicamentosRows.length > 0) {
                        medicamentosRows.forEach((row, index) => {
                            const fields = [
                                { name: `medicamentos[${index}][nombre_medicamento]`, label: 'Nombre del Medicamento', maxLength: 255 },
                                { name: `medicamentos[${index}][cantidad_recetada]`, label: 'Cantidad Recetada', isNumber: true, maxLength: 255 },
                                { name: `medicamentos[${index}][cantidad_dispensada]`, label: 'Cantidad Dispensada', isNumber: true, maxLength: 255 },
                                { name: `medicamentos[${index}][valor_unitario]`, label: 'Valor Unitario', isNumber: true, isFloat: true, maxLength: 255 },
                                { name: `medicamentos[${index}][total]`, label: 'Total', isNumber: true, isFloat: true, maxLength: 255 }
                            ];
                            let hasAnyData = false;
                            fields.forEach(field => {
                                const input = row.querySelector(`input[name="${field.name}"]`);
                                if (input.value.trim() && input.value.trim() !== '-') hasAnyData = true;
                            });
                            if (hasAnyData) {
                                fields.forEach(field => {
                                    const input = row.querySelector(`input[name="${field.name}"]`);
                                    const value = input.value.trim();
                                    if (value !== '' && value !== '-') {
                                        if (field.isNumber) {
                                            const numValue = parseFloat(value);
                                            if (isNaN(numValue) || numValue < 0) {
                                                errors.push(`El campo "${field.label}" del Medicamento #${index + 1} debe ser un número positivo.`);
                                            } else if (field.isFloat && !/^\d+(\.\d{1,2})?$/.test(value)) {
                                                errors.push(`El campo "${field.label}" del Medicamento #${index + 1} debe tener hasta dos decimales.`);
                                            }
                                        }
                                        if (field.maxLength && value.length > field.maxLength) {
                                            errors.push(`El campo "${field.label}" del Medicamento #${index + 1} no debe exceder los ${field.maxLength} caracteres.`);
                                        }
                                    } else if (value === '' && !fields.every(f => row.querySelector(`input[name="${f.name}"]`).value.trim() === '-')) {
                                        errors.push(`El campo "${field.label}" del Medicamento #${index + 1} es obligatorio si hay datos en la fila.`);
                                    }
                                });
                            }
                        });
                    } else {
                        errors.push('Debe registrar al menos un medicamento o una fila vacía/ con "-".');
                    }
                }

                // Mostrar errores con SweetAlert2
                if (errors.length > 0) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores en el formulario',
                        html: '<ul style="text-align: left;">' + errors.map(error => `<li>${error}</li>`).join('') + '</ul>',
                        confirmButtonText: 'Corregir'
                    });
                }
            });
        });

        // Lógica existente para la tabla de medicamentos
        const medicamentosTableBody = document.querySelector('#medicamentosTable tbody');
        const addMedicamentoButton = document.getElementById('add-medicamento-button');

        let initialRowCount = medicamentosTableBody.querySelectorAll('.medicamento-row').length;
        let medicamentoRowIndex = initialRowCount;

        function reindexMedicamentoRows() {
            medicamentosTableBody.querySelectorAll('.medicamento-row').forEach((row, newIndex) => {
                row.querySelectorAll('input').forEach(element => {
                    if (element.name) {
                        element.name = element.name.replace(/medicamentos\[\d+\]/, `medicamentos[${newIndex}]`);
                    }
                });
            });
            console.log('Filas re-indexadas. Número actual de filas:', medicamentosTableBody.querySelectorAll('.medicamento-row').length);
        }

        if (addMedicamentoButton) {
            addMedicamentoButton.addEventListener('click', function() {
                let currentTotalRows = medicamentosTableBody.querySelectorAll('.medicamento-row').length;
                let newRowIndex = currentTotalRows;

                const newRow = document.createElement('tr');
                newRow.classList.add('medicamento-row');
                newRow.innerHTML = `
                    <td>
                        <input type="hidden" name="medicamentos[${newRowIndex}][id_medicamento_recetado]" value="">
                        <input type="text" name="medicamentos[${newRowIndex}][nombre_medicamento]" class="table-input" value="">
                    </td>
                    <td><input type="text" name="medicamentos[${newRowIndex}][cantidad_recetada]" class="table-input" value=""></td>
                    <td><input type="text" name="medicamentos[${newRowIndex}][cantidad_dispensada]" class="table-input" value=""></td>
                    <td><input type="text" name="medicamentos[${newRowIndex}][valor_unitario]" class="table-input" value=""></td>
                    <td><input type="text" name="medicamentos[${newRowIndex}][total]" class="table-input" value=""></td>
                    <td><button type="button" class="btn-remove-row">Eliminar</button></td>
                `;
                medicamentosTableBody.appendChild(newRow);
                console.log('Fila añadida. Nuevo índice para la próxima fila:', medicamentosTableBody.querySelectorAll('.medicamento-row').length);
                reindexMedicamentoRows();
            });
        }

        medicamentosTableBody?.addEventListener('click', function(event) {
            if (event.target.classList.contains('btn-remove-row')) {
                const row = event.target.closest('.medicamento-row');
                const inputs = row.querySelectorAll('input[type="text"], input[type="number"]');
                const idMedicamentoInput = row.querySelector('input[name*="[id_medicamento_recetado]"]');

                const hasData = Array.from(inputs).some(input => input.value.trim() !== '') || (idMedicamentoInput && idMedicamentoInput.value !== '');

                if (medicamentosTableBody.querySelectorAll('.medicamento-row').length === 1 && !hasData) {
                    row.querySelectorAll('input').forEach(input => {
                        input.value = '';
                        if (input.name.includes('[id_medicamento_recetado]')) {
                            input.removeAttribute('value');
                        }
                    });
                } else {
                    row.remove();
                }

                reindexMedicamentoRows();
                console.log('Fila eliminada. Número actual de filas:', medicamentosTableBody.querySelectorAll('.medicamento-row').length);
            }
        });

        reindexMedicamentoRows();
    });
</script>
</body>