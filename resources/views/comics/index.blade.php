@extends('layouts.app')

@section('title', 'Mis Comics - Comics App')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-gradient mb-0">
                    <i class="fas fa-book me-2"></i>
                    Mis Comics
                </h2>
                <p class="text-muted mb-0">Gestiona tus creaciones</p>
            </div>
            <div>
                <a href="{{ route('comics.create') }}" class="btn btn-gradient">
                    <i class="fas fa-plus me-2"></i>
                    Crear Nuevo Comic
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y búsqueda -->
<div class="row mb-4">
    <div class="col-12">
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="Buscar por título..." id="searchInput">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="published">Publicados</option>
                        <option value="draft">Borradores</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="activeFilter">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-light w-100" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@if($userComics->count() > 0)
    <!-- Grid de Comics -->
    <div class="row g-4" id="comicsGrid">
        @foreach($userComics as $comic)
            <div class="col-lg-4 col-md-6 comic-item" 
                 data-title="{{ strtolower($comic->title) }}" 
                 data-status="{{ $comic->status }}" 
                 data-active="{{ $comic->is_active ? '1' : '0' }}">
                <div class="my-comic-card">
                    <!-- Header con estado -->
                    <div class="comic-card-header">
                        <div class="status-badges">
                            <span class="badge badge-{{ $comic->status }}">
                                {{ $comic->status === 'published' ? 'Publicado' : 'Borrador' }}
                            </span>
                            @if(!$comic->is_active)
                                <span class="badge badge-inactive">Inactivo</span>
                            @endif
                        </div>
                        <div class="comic-actions dropdown">
                            <button class="btn btn-sm btn-outline-light" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('comics.show', $comic->id) }}">
                                        <i class="fas fa-eye me-2"></i>Ver Comic
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('comics.edit', $comic->id) }}">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item" onclick="toggleActiveStatus({{ $comic->id }}, {{ $comic->is_active ? 'true' : 'false' }})">
                                        <i class="fas fa-{{ $comic->is_active ? 'eye-slash' : 'eye' }} me-2"></i>
                                        {{ $comic->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="confirmDeleteComic({{ $comic->id }}, '{{ $comic->title }}')">
                                        <i class="fas fa-trash me-2"></i>Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Portada -->
                    <div class="comic-cover-section">
                        @if($comic->cover_image && $comic->cover_url)
                            <img src="{{ $comic->cover_url }}" 
                                 alt="{{ $comic->title }}" 
                                 class="comic-cover-img"
                                 loading="lazy"
                                 onerror="this.parentElement.innerHTML='<div class=\'cover-placeholder-small\'><i class=\'fas fa-image fa-2x\'></i></div>'">
                        @else
                            <div class="cover-placeholder-small">
                                <i class="fas fa-book fa-2x"></i>
                            </div>
                        @endif
                        
                        <!-- Overlay con acciones rápidas -->
                        <div class="comic-overlay">
                            <a href="{{ route('comics.show', $comic->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('comics.edit', $comic->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Información del comic -->
                    <div class="comic-card-body">
                        <h5 class="comic-title">{{ $comic->title }}</h5>
                        <p class="comic-author text-muted">
                            <i class="fas fa-user me-1"></i>{{ $comic->author }}
                        </p>
                        <p class="comic-synopsis">{{ Str::limit($comic->synopsis, 80) }}</p>
                        
                        <!-- Estadísticas -->
                        <div class="comic-stats-row">
                            <div class="stat-item">
                                <i class="fas fa-star text-warning"></i>
                                <span>{{ $comic->rating > 0 ? number_format($comic->rating, 1) : 'N/A' }}</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-heart text-danger"></i>
                                <span>{{ $comic->favorited_by_count }}</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-file-image text-info"></i>
                                <span>{{ $comic->pages_count }}</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-star-half-alt text-secondary"></i>
                                <span>{{ $comic->ratings_count }}</span>
                            </div>
                        </div>

                        <!-- Metadatos -->
                        <div class="comic-meta">
                            <small class="text-muted">
                                <i class="fas fa-globe me-1"></i>{{ $comic->language->name ?? 'N/A' }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-calendar me-1"></i>{{ $comic->created_at->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>

                    <!-- Footer con acciones -->
                    <div class="comic-card-footer">
                        <a href="{{ route('comics.show', $comic->id) }}" class="btn btn-outline-primary btn-sm flex-fill me-2">
                            <i class="fas fa-eye me-1"></i>Ver
                        </a>
                        <a href="{{ route('comics.edit', $comic->id) }}" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Paginación -->
    @if($userComics->hasPages())
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $userComics->links() }}
            </div>
        </div>
    @endif

    <!-- Mensaje cuando no hay resultados de búsqueda -->
    <div class="row" id="noResultsMessage" style="display: none;">
        <div class="col-12">
            <div class="empty-state text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-light">No se encontraron comics</h4>
                <p class="text-muted">Intenta con otros términos de búsqueda o filtros</p>
                <button class="btn btn-outline-light" onclick="clearFilters()">
                    <i class="fas fa-times me-1"></i>Limpiar filtros
                </button>
            </div>
        </div>
    </div>

@else
    <!-- Estado vacío -->
    <div class="row">
        <div class="col-12">
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-book-open fa-5x text-muted"></i>
                </div>
                <h3 class="text-gradient mb-3">¡Tu biblioteca está vacía!</h3>
                <p class="lead text-light mb-4">
                    Parece que aún no has creado ningún comic. ¡Es hora de dar rienda suelta a tu creatividad!
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('comics.create') }}" class="btn btn-gradient btn-lg">
                        <i class="fas fa-plus me-2"></i>Crear Mi Primer Comic
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-home me-2"></i>Explorar Comics
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el comic <strong id="comicToDeleteTitle"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Esta acción no se puede deshacer.</strong> Se eliminarán todas las páginas, calificaciones y favoritos asociados.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="deleteComic()">
                    <i class="fas fa-trash me-1"></i>Eliminar Permanentemente
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.filter-section {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.search-box {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
    z-index: 2;
}

.search-box input {
    padding-left: 35px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
}

.search-box input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.search-box input:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25);
}

.my-comic-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.my-comic-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
}

.comic-card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-badges .badge {
    margin-right: 0.5rem;
    font-size: 0.75rem;
}

.badge-published {
    background: #28a745;
    color: white;
}

.badge-draft {
    background: #ffc107;
    color: #212529;
}

.badge-inactive {
    background: #6c757d;
    color: white;
}

.comic-cover-section {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.comic-cover-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.cover-placeholder-small {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.comic-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.my-comic-card:hover .comic-overlay {
    opacity: 1;
}

.my-comic-card:hover .comic-cover-img {
    transform: scale(1.1);
}

.comic-card-body {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.comic-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.comic-author {
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
}

.comic-synopsis {
    font-size: 0.9rem;
    color: #666;
    flex-grow: 1;
    margin-bottom: 1rem;
}

.comic-stats-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.85rem;
    font-weight: 500;
}

.comic-meta {
    margin-bottom: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e9ecef;
}

.comic-card-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    opacity: 0.5;
    margin-bottom: 2rem;
}

/* Dropdown customizations */
.dropdown-menu {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.dropdown-item {
    color: #2c3e50;
    font-size: 0.9rem;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
}

.dropdown-item.text-danger:hover {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .comic-stats-row {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .stat-item {
        flex: 1;
        min-width: calc(50% - 0.25rem);
        justify-content: center;
    }
}
</style>
@endsection

@section('scripts')
<script>
let comicToDelete = null;

// Filtrado y búsqueda
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const activeFilter = document.getElementById('activeFilter');

    // Event listeners para filtros
    searchInput.addEventListener('input', filterComics);
    statusFilter.addEventListener('change', filterComics);
    activeFilter.addEventListener('change', filterComics);
});

function filterComics() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const activeFilter = document.getElementById('activeFilter').value;
    
    const comicItems = document.querySelectorAll('.comic-item');
    const noResultsMessage = document.getElementById('noResultsMessage');
    let visibleCount = 0;

    comicItems.forEach(item => {
        const title = item.dataset.title;
        const status = item.dataset.status;
        const active = item.dataset.active;

        const matchesSearch = title.includes(searchTerm);
        const matchesStatus = statusFilter === '' || status === statusFilter;
        const matchesActive = activeFilter === '' || active === activeFilter;

        if (matchesSearch && matchesStatus && matchesActive) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Mostrar mensaje de "no hay resultados" si es necesario
    if (visibleCount === 0 && comicItems.length > 0) {
        noResultsMessage.style.display = 'block';
    } else {
        noResultsMessage.style.display = 'none';
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('activeFilter').value = '';
    filterComics();
}

// Gestión de comics
function toggleActiveStatus(comicId, currentStatus) {
    const action = currentStatus ? 'desactivar' : 'activar';
    
    if (!confirm(`¿Estás seguro de que deseas ${action} este comic?`)) {
        return;
    }

    fetch(`/comics/${comicId}/toggle-active`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al cambiar el estado del comic');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar el estado del comic');
    });
}

function confirmDeleteComic(comicId, comicTitle) {
    comicToDelete = comicId;
    document.getElementById('comicToDeleteTitle').textContent = comicTitle;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteComic() {
    if (!comicToDelete) return;

    fetch(`/comics/${comicToDelete}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Error al eliminar el comic');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el comic');
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
    });
}
</script>
@endsection