@extends('layouts.app')

@section('title', 'Dashboard - Comics App')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </h2>
            <span class="badge bg-primary fs-6">
                Bienvenido, {{ Auth::user()->name }}!
            </span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Estadísticas rápidas -->
    <div class="col-md-3 mb-4">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <i class="fas fa-book fa-2x mb-2"></i>
                <h4 class="card-title">{{ Auth::user()->comics()->count() }}</h4>
                <p class="card-text">Mis Comics</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <i class="fas fa-heart fa-2x mb-2"></i>
                <h4 class="card-title">{{ Auth::user()->favorites()->count() }}</h4>
                <p class="card-text">Favoritos</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <i class="fas fa-star fa-2x mb-2"></i>
                <h4 class="card-title">{{ Auth::user()->ratings()->count() }}</h4>
                <p class="card-text">Calificaciones</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-center bg-warning text-white">
            <div class="card-body">
                <i class="fas fa-eye fa-2x mb-2"></i>
                <h4 class="card-title">
                    {{ Auth::user()->comics()->where('status', 'published')->count() }}
                </h4>
                <p class="card-text">Publicados</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Acciones rápidas -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('comics.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Nuevo Comic
                    </a>
                    <a href="{{ route('comics.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-book me-2"></i>Mis Comics
                    </a>
                    <a href="{{ route('profile') }}" class="btn btn-outline-info">
                        <i class="fas fa-user-edit me-2"></i>Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actividad reciente -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                @if(Auth::user()->comics()->exists())
                    <div class="list-group list-group-flush">
                        @foreach(Auth::user()->comics()->latest()->take(3)->get() as $comic)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1">{{ $comic->title }}</h6>
                                    <small class="text-muted">{{ $comic->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $comic->status === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($comic->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-book fa-3x mb-3 opacity-50"></i>
                        <p>Aún no has creado ningún comic.</p>
                        <p class="small">¡Crea tu primer comic para empezar!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Comics favoritos recientes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-heart me-2"></i>Mis Favoritos Recientes
                </h5>
            </div>
            <div class="card-body">
                @if(Auth::user()->favoriteComics()->exists())
                    <div class="row">
                        @foreach(Auth::user()->favoriteComics()->latest('user_favorites.created_at')->take(4)->get() as $comic)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-book fa-2x text-primary mb-2"></i>
                                        <h6 class="card-title">{{ Str::limit($comic->title, 20) }}</h6>
                                        <p class="card-text small text-muted">
                                            Por: {{ $comic->author }}
                                        </p>
                                        <div class="mt-2">
                                            @if($comic->rating > 0)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-star"></i> {{ number_format($comic->rating, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-heart fa-3x mb-3 opacity-50"></i>
                        <p>No tienes comics favoritos aún.</p>
                        <p class="small">Explora y marca tus comics favoritos.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection