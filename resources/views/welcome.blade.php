@extends('layouts.app')

@section('title', 'Bienvenido - Comics App')

@section('content')
<!-- Hero Section -->
<div class="hero-section text-center py-5 mb-5">
    <div class="hero-content">
        <h1 class="display-3 fw-bold mb-4 text-gradient">
            <i class="fas fa-book-open me-3"></i>
            Comics App
        </h1>
        <p class="lead mb-4 text-light">
            Descubre, crea y comparte increíbles historias en formato cómic
        </p>
        
        @guest
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-gradient btn-lg px-4">
                    <i class="fas fa-user-plus me-2"></i>Comenzar Ahora
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                </a>
            </div>
        @else
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-gradient btn-lg px-4">
                    <i class="fas fa-tachometer-alt me-2"></i>Mi Dashboard
                </a>
            </div>
        @endguest
    </div>
</div>

<!-- Comics Section -->
<div class="row">
    <div class="col-12">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold text-gradient">
                <i class="fas fa-fire me-2"></i>Comics Destacados
            </h2>
            <p class="text-muted">Los mejores comics de nuestra comunidad</p>
        </div>
    </div>
</div>

@if($comics->count() > 0)
    <div class="row g-4">
        @foreach($comics as $comic)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="comic-card" onclick="goToComic({{ $comic->id }})">
                    <div class="comic-image">
                        <div class="comic-placeholder">
                            <i class="fas fa-book fa-3x"></i>
                        </div>
                        <div class="comic-overlay">
                            <i class="fas fa-play-circle fa-2x"></i>
                        </div>
                    </div>
                    <div class="comic-info">
                        <h5 class="comic-title">{{ Str::limit($comic->title, 25) }}</h5>
                        <p class="comic-author">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ $comic->author }}
                        </p>
                        <p class="comic-synopsis">
                            {{ Str::limit($comic->synopsis, 60) }}
                        </p>
                        <div class="comic-meta">
                            <div class="comic-rating">
                                @if($comic->rating > 0)
                                    <i class="fas fa-star text-warning"></i>
                                    <span>{{ number_format($comic->rating, 1) }}</span>
                                @else
                                    <i class="fas fa-star-o text-muted"></i>
                                    <span class="text-muted">Sin calificar</span>
                                @endif
                            </div>
                            <div class="comic-language">
                                <span class="badge bg-info">{{ $comic->language->name }}</span>
                            </div>
                        </div>
                        <div class="comic-stats mt-2">
                            <small class="text-muted">
                                <i class="fas fa-heart me-1"></i>{{ $comic->favoritedBy->count() }} favoritos
                                <span class="mx-2">•</span>
                                <i class="fas fa-eye me-1"></i>{{ $comic->pages->count() }} páginas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Paginación -->
    @if($comics->hasPages())
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $comics->links() }}
            </div>
        </div>
    @endif
@else
    <!-- Mensaje cuando no hay comics -->
    <div class="row">
        <div class="col-12">
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-meh-rolling-eyes fa-5x text-muted"></i>
                </div>
                <h3 class="text-gradient mb-3">¡Oops! Aquí no hay nada todavía</h3>
                <p class="lead text-light mb-4">
                    Parece que nuestros artistas están tomando una siesta... 😴
                </p>
                <p class="text-muted mb-4">
                    Regresa más tarde a ver si ya hay algo interesante, o mejor aún...
                </p>
                @guest
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-gradient">
                            <i class="fas fa-pencil-alt me-2"></i>¡Sé el primero en crear!
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                    </div>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-gradient">
                        <i class="fas fa-plus me-2"></i>¡Crea el primer comic!
                    </a>
                @endguest
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
function goToComic(comicId) {
    // Por ahora solo muestra el ID, después conectaremos con la página de lectura
    alert('Próximamente: Leer comic #' + comicId);
    console.log('Navegando al comic:', comicId);
}
</script>
@endsection