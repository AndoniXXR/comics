<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Comics Platform') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .hero-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 2rem 0;
            padding: 3rem 2rem;
            text-align: center;
            color: white;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            font-weight: 300;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .auth-buttons {
            gap: 1rem;
        }
        
        .btn-custom {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .btn-primary-custom {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.4);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 90, 36, 0.6);
            color: white;
        }
        
        .btn-outline-custom {
            color: white;
            border-color: white;
            background: transparent;
        }
        
        .btn-outline-custom:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
        }
        
        .comics-section {
            background: white;
            border-radius: 20px;
            margin: 2rem 0;
            padding: 3rem 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            color: #2c3e50;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border-radius: 2px;
        }
        
        .comic-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
            border: none;
        }
        
        .comic-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .comic-cover-container {
            position: relative;
            width: 100%;
            height: 280px;
            overflow: hidden;
        }
        
        .comic-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .comic-card:hover .comic-cover {
            transform: scale(1.05);
        }
        
        .comic-cover-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }
        
        .comic-info {
            padding: 1.5rem;
        }
        
        .comic-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            line-height: 1.4;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .comic-author {
            color: #7f8c8d;
            font-size: 0.95rem;
            margin-bottom: 0.8rem;
        }
        
        .comic-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .comic-rating {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: #f39c12;
            font-weight: 500;
        }
        
        .comic-language {
            background: #ecf0f1;
            color: #34495e;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .btn-read {
            width: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 0.7rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-read:hover {
            background: linear-gradient(45deg, #5a67d8, #6b46c1);
            transform: translateY(-1px);
            color: white;
        }
        
        .no-comics {
            text-align: center;
            padding: 4rem 2rem;
            color: #7f8c8d;
        }
        
        .no-comics i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .stats-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 2rem 0;
            padding: 2rem;
            color: white;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #667eea !important;
        }
        
        .comic-status-badges {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }
        
        .comic-status-badges .badge {
            margin-left: 0.25rem;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-book-open me-2"></i>
                ComiCs
            </a>
            
            <div class="navbar-nav ms-auto">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                    <a href="{{ route('comics.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-book me-1"></i>Mis Comics
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-user-plus me-1"></i>Registrarse
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">
                <i class="fas fa-book-open me-3"></i>
                Bienvenido a Comics Platform
            </h1>
            <p class="hero-subtitle">
                Descubre, comparte y disfruta de increíbles historias visuales creadas por nuestra comunidad
            </p>
            
            @guest
                <div class="d-flex justify-content-center auth-buttons">
                    <a href="{{ route('register') }}" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-user-plus me-2"></i>
                        Únete Ahora
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-custom btn-custom">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Iniciar Sesión
                    </a>
                </div>
            @else
                <div class="d-flex justify-content-center auth-buttons">
                    <a href="{{ route('comics.create') }}" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-plus me-2"></i>
                        Crear Comic
                    </a>
                    <a href="{{ route('comics.index') }}" class="btn btn-outline-custom btn-custom">
                        <i class="fas fa-book me-2"></i>
                        Mis Comics
                    </a>
                </div>
            @endguest
        </div>

        <!-- Stats Section -->
        @if(isset($totalComics) && $totalComics > 0)
        <div class="stats-section">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">{{ $totalComics ?? 0 }}</div>
                        <div class="stat-label">Comics Disponibles</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">{{ $totalAuthors ?? 0 }}</div>
                        <div class="stat-label">Autores</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">{{ $totalPages ?? 0 }}</div>
                        <div class="stat-label">Páginas Totales</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">{{ $totalLanguages ?? 0 }}</div>
                        <div class="stat-label">Idiomas</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Comics Section -->
        <div class="comics-section">
            <h2 class="section-title">
                <i class="fas fa-star me-3"></i>
                Comics Destacados
            </h2>
            
            @if($comics->count() > 0)
                <div class="row g-4">
                    @foreach($comics as $comic)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card comic-card" onclick="goToComic({{ $comic->id }})">
                                <!-- Portada del Comic -->
                                <div class="comic-cover-container">
                                    @if($comic->cover_image && $comic->cover_url)
                                        <img src="{{ $comic->cover_url }}" 
                                             alt="{{ $comic->title }}" 
                                             class="comic-cover"
                                             loading="lazy"
                                             onerror="this.parentElement.innerHTML='<div class=\'comic-cover-placeholder\'><i class=\'fas fa-image fa-4x\'></i></div>'">
                                    @else
                                        <div class="comic-cover-placeholder">
                                            <i class="fas fa-book fa-4x"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Badges de estado (solo si estás autenticado y es tu comic) -->
                                    @auth
                                        @if($comic->user_id === auth()->id())
                                            <div class="comic-status-badges">
                                                @if($comic->status === 'draft')
                                                    <span class="badge bg-warning">Borrador</span>
                                                @endif
                                                @if(!$comic->is_active)
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                                
                                <div class="comic-info">
                                    <h5 class="comic-title">{{ $comic->title }}</h5>
                                    <p class="comic-author">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $comic->author }}
                                    </p>
                                    
                                    <div class="comic-meta">
                                        <div class="comic-rating">
                                            @if($comic->rating > 0)
                                                <i class="fas fa-star"></i>
                                                <span>{{ number_format($comic->rating, 1) }}</span>
                                            @else
                                                <i class="fas fa-star text-muted"></i>
                                                <span class="text-muted">Sin calificar</span>
                                            @endif
                                        </div>
                                        <span class="comic-language">
                                            {{ $comic->language->name ?? 'N/A' }}
                                        </span>
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
                
                <!-- Pagination -->
                @if($comics->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $comics->links() }}
                    </div>
                @endif
            @else
                <div class="no-comics">
                    <i class="fas fa-book-open"></i>
                    <h4>No hay comics disponibles</h4>
                    <p>¡Sé el primero en crear un comic increíble!</p>
                    @auth
                        <a href="{{ route('comics.create') }}" class="btn btn-primary-custom btn-custom mt-3">
                            <i class="fas fa-plus me-2"></i>
                            Crear Mi Primer Comic
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function goToComic(comicId) {
        // Redirigir a la página del comic
        window.location.href = "{{ route('comics.show', '') }}/" + comicId;
    }
    </script>
</body>
</html>