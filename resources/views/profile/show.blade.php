@extends('layouts.main')

@section('content')
<div class="container">
    <div class="main-container">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <h1 class="page-title">Mi Perfil</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Perfil</li>
                </ol>
            </div>
        </div>
        <!-- PAGE-HEADER END -->

        <!-- ROW-1 -->
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información del Usuario</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Nombre:</strong>
                            <p>{{ $user->name }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong>
                            <p>{{ $user->email }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Rol:</strong>
                            <p><span class="badge bg-primary fs-14">{{ $user->getRoleNames()->first() }}</span></p>
                        </div>
                        {{-- Puedes agregar más información del perfil aquí si lo necesitas. --}}
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ROW-1 END -->
    </div>
</div>
@endsection
