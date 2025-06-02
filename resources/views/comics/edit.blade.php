@extends('layouts.app')

@section('title', 'Editar Comic - Comics App')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-gradient">
                <i class="fas fa-edit me-2"></i>Editar Comic
            </h2>
            <div>
                <a href="{{ route('comics.show', $comic->id) }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-eye me-2"></i>Ver Comic
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="comic-create-card">
            <div class="comic-create-header">
                <h5><i class="fas fa-book-open me-2"></i>Editando: {{ $comic->title }}</h5>
                <p class="text-muted mb-0">Modifica la información de tu comic</p>
            </div>
            
            <div class="comic-create-body">
                <form method="POST" action="{{ route('comics.update', $comic->id) }}" enctype="multipart/form-data" id="editComicForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-lg-8">
                            @include('comics.partials.edit-basic-info')
                        </div>

                        <!-- Portada -->
                        <div class="col-lg-4">
                            @include('comics.partials.edit-cover')
                        </div>
                    </div>

                    <!-- Gestión de páginas -->
                    @include('comics.partials.edit-pages')

                    <!-- Botones de acción -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg" id="updateBtn">
                            <span class="btn-text">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Guardando...
                            </span>
                        </button>
                        <a href="{{ route('comics.show', $comic->id) }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        
                        <button type="button" class="btn btn-outline-danger btn-lg ms-auto" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Eliminar Comic
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('comics.partials.edit-modals')
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/comic-edit.js') }}"></script>
@endsection