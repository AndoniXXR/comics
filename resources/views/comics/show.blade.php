@extends('layouts.app')

@section('title', $comic->title . ' - Comics App')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('home') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                </a>
                @auth
                    @if($comic->user_id === Auth::id())
                        <a href="{{ route('comics.edit', $comic->id) }}" class="btn btn-outline-warning me-2">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                    @endif
                @endauth
            </div>
            
            <div class="comic-actions">
                @auth
                    <!-- Botón de Favorito -->
                    <button class="btn btn-outline-danger me-2" onclick="toggleFavorite({{ $comic->id }})" id="favoriteBtn">
                        <i class="fas fa-heart me-1" id="favoriteIcon"></i>
                        <span id="favoriteText">{{ $isFavorite ? 'Quitar de Favoritos' : 'Agregar a Favoritos' }}</span>
                    </button>
                    
                    <!-- Botón de Calificación -->
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#ratingModal">
                        <i class="fas fa-star me-1"></i>
                        {{ $userRating ? 'Mi Calificación: ' . $userRating->rating : 'Calificar' }}
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt me-1"></i>Inicia sesión para calificar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Información del Comic -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-4 mb-4">
        <div class="comic-cover-container">
            <div class="comic-cover">
                @if($comic->cover_image)
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($comic->cover_image_url)) }}" 
                         alt="{{ $comic->title }}" 
                         class="cover-image">
                @else
                    <div class="cover-placeholder">
                        <i class="fas fa-book fa-4x"></i>
                    </div>
                @endif
            </div>
            
            <!-- Stats del Comic -->
            <div class="comic-stats mt-3">
                <div class="stat-item">
                    <i class="fas fa-star text-warning"></i>
                    <span class="stat-value">{{ $comic->rating > 0 ? number_format($comic->rating, 1) : 'Sin calificar' }}</span>
                    <small class="stat-label">({{ $comic->ratings_count }} calificaciones)</small>
                </div>
                <div class="stat-item">
                    <i class="fas fa-heart text-danger"></i>
                    <span class="stat-value">{{ $comic->favorited_by_count }}</span>
                    <small class="stat-label">favoritos</small>
                </div>
                <div class="stat-item">
                    <i class="fas fa-file-image text-info"></i>
                    <span class="stat-value">{{ $comic->pages->count() }}</span>
                    <small class="stat-label">páginas</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-9 col-md-8">
        <div class="comic-info-card">
            <div class="comic-header">
                <h1 class="comic-title">{{ $comic->title }}</h1>
                <div class="comic-meta">
                    <span class="author">
                        <i class="fas fa-user-edit me-1"></i>
                        Por: <strong>{{ $comic->author }}</strong>
                    </span>
                    <span class="language">
                        <i class="fas fa-globe me-1"></i>
                        {{ $comic->language->name }}
                    </span>
                    <span class="status">
                        <i class="fas fa-circle me-1 status-indicator status-{{ $comic->status }}"></i>
                        {{ ucfirst($comic->status) }}
                    </span>
                    <span class="date">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $comic->created_at->format('d/m/Y') }}
                    </span>
                </div>
            </div>
            
            <div class="comic-synopsis">
                <h5><i class="fas fa-align-left me-2"></i>Sinopsis</h5>
                <p>{{ $comic->synopsis }}</p>
            </div>
            
            @if($comic->pages->count() > 0)
                <div class="reading-controls">
                    <button class="btn btn-primary btn-lg" onclick="startReading()">
                        <i class="fas fa-play me-2"></i>Comenzar a Leer
                    </button>
                    <button class="btn btn-outline-primary" onclick="showPageSelector()">
                        <i class="fas fa-list me-2"></i>Seleccionar Página
                    </button>
                </div>
            @else
                <div class="no-pages-message">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Este comic aún no tiene páginas disponibles.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Visor de Comic -->
@if($comic->pages->count() > 0)
    <div class="comic-reader reader-hidden" id="comicReader">
        <div class="reader-header">
            <div class="reader-controls">
                <button class="btn btn-outline-light" onclick="closeReader()" id="closeReaderBtn">
                    <i class="fas fa-times"></i>
                    <span class="d-none d-md-inline ms-2">Cerrar</span>
                </button>
                <div class="page-info">
                    <span id="currentPageNum">1</span> / {{ $comic->pages->count() }}
                </div>
                <div class="reader-actions">
                    <button class="btn btn-outline-light" onclick="previousPage()" id="prevBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-outline-light" onclick="nextPage()" id="nextBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="reader-content">
            <div class="page-container" id="pageContainer">
                @foreach($comic->pages as $page)
                    <div class="comic-page" data-page="{{ $page->page_number }}" style="display: {{ $loop->first ? 'flex' : 'none' }}">
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($page->image_url)) }}" 
                             alt="Página {{ $page->page_number }}" 
                             class="page-image"
                             onclick="nextPage()"
                             onerror="console.error('Error cargando imagen:', this.src)">
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Navegación inferior -->
        <div class="reader-footer">
            <div class="page-navigator">
                @foreach($comic->pages as $page)
                    <button class="page-thumb {{ $loop->first ? 'active' : '' }}" 
                            onclick="goToPage({{ $page->page_number }})"
                            data-page="{{ $page->page_number }}">
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($page->image_url)) }}" 
                             alt="Página {{ $page->page_number }}">
                        <span>{{ $page->page_number }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Selector de Páginas Modal -->
<div class="modal fade" id="pageSelectorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Página</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="pages-grid">
                    @foreach($comic->pages as $page)
                        <div class="page-selector-item" onclick="goToPageAndCloseModal({{ $page->page_number }})">
                            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($page->image_url)) }}" 
                                 alt="Página {{ $page->page_number }}" 
                                 class="page-selector-thumb">
                            <div class="page-number-overlay">{{ $page->page_number }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Calificación -->
@auth
<div class="modal fade" id="ratingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Calificar Comic</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="rating-container">
                    <p class="text-center mb-3">¿Qué te pareció "{{ $comic->title }}"?</p>
                    <div class="star-rating" id="starRating">
                        @for($i = 1; $i <= 10; $i++)
                            <span class="star" data-rating="{{ $i }}" onclick="setRating({{ $i }})">
                                <i class="fas fa-star"></i>
                            </span>
                        @endfor
                    </div>
                    <div class="rating-text text-center mt-2">
                        <span id="ratingText">Selecciona una calificación</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submitRating()" id="submitRatingBtn" disabled>
                    Calificar
                </button>
            </div>
        </div>
    </div>
</div>
@endauth

<!-- Comentarios y Calificaciones -->
<div class="row mt-5">
    <div class="col-12">
        <div class="reviews-section">
            <h4><i class="fas fa-comments me-2"></i>Calificaciones de la Comunidad</h4>
            
            @if($comic->ratings->count() > 0)
                <div class="reviews-list">
                    @foreach($comic->ratings->take(5) as $rating)
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <strong>{{ $rating->user->name }}</strong>
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 10; $i++)
                                            <i class="fas fa-star {{ $i <= $rating->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <span class="rating-value">({{ $rating->rating }}/10)</span>
                                    </div>
                                </div>
                                <small class="review-date">{{ $rating->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($comic->ratings->count() > 5)
                        <div class="text-center mt-3">
                            <button class="btn btn-outline-primary btn-sm">
                                Ver todas las calificaciones ({{ $comic->ratings->count() }})
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="no-reviews">
                    <i class="fas fa-star-half-alt fa-3x text-muted mb-3"></i>
                    <p>Este comic aún no tiene calificaciones.</p>
                    @auth
                        <p>¡Sé el primero en calificarlo!</p>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1;
const totalPages = {{ $comic->pages->count() }};
let selectedRating = {{ $userRating ? $userRating->rating : 0 }};
let isFavorite = {{ $isFavorite ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    updateFavoriteButton();
    updateRatingStars();
    
    // Agregar evento al botón de cerrar
    const closeBtn = document.getElementById('closeReaderBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeReader();
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const reader = document.getElementById('comicReader');
        if (reader && reader.style.display !== 'none') {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                previousPage();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                nextPage();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                closeReader();
            }
        }
    });
    
    // Click outside to close (opcional)
    document.addEventListener('click', function(e) {
        const reader = document.getElementById('comicReader');
        if (reader && reader.style.display !== 'none') {
            const readerContent = reader.querySelector('.reader-content');
            const pageImage = reader.querySelector('.page-image');
            
            // Si el click es en el área negra (fuera de la imagen) cerrar el lector
            if (e.target === readerContent && !pageImage.contains(e.target)) {
                closeReader();
            }
        }
    });
});

// Comic Reader Functions
function closeReader() {
    console.log('Cerrando lector...');
    const reader = document.getElementById('comicReader');
    if (reader) {
        reader.classList.remove('reader-active');
        reader.classList.add('reader-hidden');
        document.body.style.overflow = 'auto';
        document.body.classList.remove('reader-open');
        console.log('Lector cerrado exitosamente');
    } else {
        console.error('No se encontró el elemento comicReader');
    }
}

function startReading() {
    console.log('Iniciando lectura...');
    const reader = document.getElementById('comicReader');
    if (reader) {
        reader.classList.remove('reader-hidden');
        reader.classList.add('reader-active');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('reader-open');
        currentPage = 1;
        updatePageDisplay();
        console.log('Lector iniciado exitosamente');
    } else {
        console.error('No se encontró el elemento comicReader');
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        updatePageDisplay();
    }
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        updatePageDisplay();
    }
}

function goToPage(pageNumber) {
    currentPage = pageNumber;
    updatePageDisplay();
}

function goToPageAndCloseModal(pageNumber) {
    goToPage(pageNumber);
    startReading();
    const modal = bootstrap.Modal.getInstance(document.getElementById('pageSelectorModal'));
    modal.hide();
}

function updatePageDisplay() {
    console.log('Actualizando a página:', currentPage);
    
    // Hide all pages
    document.querySelectorAll('.comic-page').forEach(page => {
        page.style.display = 'none';
    });
    
    // Show current page
    const currentPageElement = document.querySelector(`[data-page="${currentPage}"]`);
    console.log('Elemento de página encontrado:', currentPageElement);
    
    if (currentPageElement) {
        currentPageElement.style.display = 'flex';
        
        // Verificar que la imagen se carga
        const img = currentPageElement.querySelector('img');
        if (img) {
            console.log('Imagen encontrada:', img.src.substring(0, 50) + '...');
            img.onload = function() {
                console.log('Imagen cargada exitosamente');
            };
            img.onerror = function() {
                console.error('Error al cargar imagen');
            };
        }
    } else {
        console.error('No se encontró la página:', currentPage);
    }
    
    // Update page counter
    document.getElementById('currentPageNum').textContent = currentPage;
    
    // Update navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) prevBtn.disabled = currentPage === 1;
    if (nextBtn) nextBtn.disabled = currentPage === totalPages;
    
    // Update thumbnails
    document.querySelectorAll('.page-thumb').forEach(thumb => {
        thumb.classList.remove('active');
    });
    
    const activeThumb = document.querySelector(`.page-thumb[data-page="${currentPage}"]`);
    if (activeThumb) {
        activeThumb.classList.add('active');
        activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function showPageSelector() {
    const modal = new bootstrap.Modal(document.getElementById('pageSelectorModal'));
    modal.show();
}

// Rating Functions
function setRating(rating) {
    selectedRating = rating;
    updateRatingStars();
    document.getElementById('submitRatingBtn').disabled = false;
    
    const ratingTexts = {
        1: '1/10 - Terrible',
        2: '2/10 - Muy malo', 
        3: '3/10 - Malo',
        4: '4/10 - Regular',
        5: '5/10 - Normal',
        6: '6/10 - Bien',
        7: '7/10 - Muy bien',
        8: '8/10 - Excelente',
        9: '9/10 - Increíble',
        10: '10/10 - Perfecto'
    };
    
    document.getElementById('ratingText').textContent = ratingTexts[rating];
}

function updateRatingStars() {
    document.querySelectorAll('#starRating .star').forEach((star, index) => {
        const starIcon = star.querySelector('i');
        if (index < selectedRating) {
            starIcon.className = 'fas fa-star text-warning';
        } else {
            starIcon.className = 'far fa-star text-muted';
        }
    });
}

function submitRating() {
    if (selectedRating === 0) return;
    
    fetch(`/comics/{{ $comic->id }}/rate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            rating: selectedRating
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al enviar la calificación');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al enviar la calificación');
    });
}

// Favorite Functions
function toggleFavorite(comicId) {
    const url = isFavorite ? `/comics/${comicId}/unfavorite` : `/comics/${comicId}/favorite`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            isFavorite = !isFavorite;
            updateFavoriteButton();
        } else {
            alert('Error al actualizar favoritos');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar favoritos');
    });
}

function updateFavoriteButton() {
    const btn = document.getElementById('favoriteBtn');
    const icon = document.getElementById('favoriteIcon');
    const text = document.getElementById('favoriteText');
    
    if (isFavorite) {
        btn.className = 'btn btn-danger me-2';
        icon.className = 'fas fa-heart me-1';
        text.textContent = 'Quitar de Favoritos';
    } else {
        btn.className = 'btn btn-outline-danger me-2';
        icon.className = 'far fa-heart me-1';
        text.textContent = 'Agregar a Favoritos';
    }
}
</script>
@endsection