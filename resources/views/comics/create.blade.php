@extends('layouts.app')

@section('title', 'Crear Comic - Comics App')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-gradient">
                <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Comic
            </h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="comic-create-card">
            <div class="comic-create-header">
                <h5><i class="fas fa-book-open me-2"></i>Información del Comic</h5>
                <p class="text-muted mb-0">Completa todos los campos para crear tu comic</p>
            </div>
            
            <div class="comic-create-body">
                <form method="POST" action="{{ route('comics.store') }}" enctype="multipart/form-data" id="comicForm">
                    @csrf

                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-lg-8">
                            <div class="form-section">
                                <h6 class="section-title">
                                    <i class="fas fa-info-circle me-2"></i>Información Básica
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="title" class="form-label">
                                                <i class="fas fa-heading me-2"></i>Título del Comic *
                                            </label>
                                            <div class="input-wrapper">
                                                <input type="text" 
                                                       class="form-control @error('title') is-invalid @enderror" 
                                                       id="title" 
                                                       name="title" 
                                                       value="{{ old('title') }}" 
                                                       required
                                                       maxlength="255"
                                                       placeholder="El nombre épico de tu comic">
                                                <div class="input-icon">
                                                    <i class="fas fa-heading"></i>
                                                </div>
                                            </div>
                                            @error('title')
                                                <div class="error-message">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="language_id" class="form-label">
                                                <i class="fas fa-globe me-2"></i>Idioma *
                                            </label>
                                            <div class="input-wrapper">
                                                <select class="form-control @error('language_id') is-invalid @enderror" 
                                                        id="language_id" 
                                                        name="language_id" 
                                                        required>
                                                    <option value="">Seleccionar idioma</option>
                                                    @foreach($languages as $language)
                                                        <option value="{{ $language->id }}" 
                                                                {{ old('language_id') == $language->id ? 'selected' : '' }}>
                                                            {{ $language->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-icon">
                                                    <i class="fas fa-globe"></i>
                                                </div>
                                            </div>
                                            @error('language_id')
                                                <div class="error-message">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="author" class="form-label">
                                        <i class="fas fa-user-edit me-2"></i>Autor *
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" 
                                               class="form-control @error('author') is-invalid @enderror" 
                                               id="author" 
                                               name="author" 
                                               value="{{ old('author', Auth::user()->name) }}" 
                                               required
                                               maxlength="255"
                                               placeholder="Tu nombre o seudónimo">
                                        <div class="input-icon">
                                            <i class="fas fa-user-edit"></i>
                                        </div>
                                    </div>
                                    @error('author')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="synopsis" class="form-label">
                                        <i class="fas fa-align-left me-2"></i>Sinopsis *
                                    </label>
                                    <div class="input-wrapper">
                                        <textarea class="form-control @error('synopsis') is-invalid @enderror" 
                                                  id="synopsis" 
                                                  name="synopsis" 
                                                  rows="4" 
                                                  required
                                                  maxlength="1000"
                                                  placeholder="Describe de qué trata tu comic... ¡Haz que suene emocionante!">{{ old('synopsis') }}</textarea>
                                        <div class="input-icon">
                                            <i class="fas fa-align-left"></i>
                                        </div>
                                    </div>
                                    <div class="character-count">
                                        <span id="synopsisCount">0</span>/1000 caracteres
                                    </div>
                                    @error('synopsis')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status" class="form-label">
                                        <i class="fas fa-toggle-on me-2"></i>Estado de Publicación *
                                    </label>
                                    <div class="status-options">
                                        <div class="status-option">
                                            <input type="radio" id="draft" name="status" value="draft" 
                                                   {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}>
                                            <label for="draft" class="status-label">
                                                <div class="status-card">
                                                    <i class="fas fa-edit fa-2x"></i>
                                                    <h6>Borrador</h6>
                                                    <p>Solo tú puedes verlo</p>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="status-option">
                                            <input type="radio" id="published" name="status" value="published"
                                                   {{ old('status') === 'published' ? 'checked' : '' }}>
                                            <label for="published" class="status-label">
                                                <div class="status-card">
                                                    <i class="fas fa-globe fa-2x"></i>
                                                    <h6>Publicado</h6>
                                                    <p>Visible para todos</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @error('status')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Portada -->
                        <div class="col-lg-4">
                            <div class="form-section">
                                <h6 class="section-title">
                                    <i class="fas fa-image me-2"></i>Portada del Comic
                                </h6>
                                
                                <div class="cover-upload-container">
                                    <div class="cover-preview" id="coverPreview">
                                        <div class="cover-placeholder">
                                            <i class="fas fa-image fa-3x"></i>
                                            <h6>Sube la Portada</h6>
                                            <p>JPG, PNG hasta 5MB</p>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('cover_image').click()">
                                                <i class="fas fa-upload me-1"></i>Seleccionar
                                            </button>
                                        </div>
                                    </div>
                                    <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/jpg" style="display: none;" required>
                                    
                                    <div class="cover-controls" id="coverControls" style="display: none;">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('cover_image').click()">
                                            <i class="fas fa-edit me-1"></i>Cambiar
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCover()">
                                            <i class="fas fa-trash me-1"></i>Quitar
                                        </button>
                                    </div>
                                </div>
                                @error('cover_image')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Páginas del comic -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-file-image me-2"></i>Páginas del Comic
                        </h6>
                        
                        <div class="pages-upload-container">
                            <div class="pages-dropzone" id="pagesDropzone">
                                <div class="dropzone-content">
                                    <i class="fas fa-cloud-upload-alt fa-3x"></i>
                                    <h5>Arrastra y suelta las páginas aquí</h5>
                                    <p>O haz click para seleccionar archivos</p>
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('pages').click()">
                                        <i class="fas fa-plus me-2"></i>Agregar Páginas
                                    </button>
                                    <small class="text-muted d-block mt-2">JPG, PNG hasta 10MB por página</small>
                                </div>
                            </div>
                            <input type="file" id="pages" name="pages[]" accept="image/jpeg,image/png,image/jpg" multiple style="display: none;" required>
                            
                            <div class="pages-preview" id="pagesPreview" style="display: none;">
                                <div class="pages-header">
                                    <h6><i class="fas fa-images me-2"></i>Páginas Subidas (<span id="pageCount">0</span>)</h6>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('pages').click()">
                                        <i class="fas fa-plus me-1"></i>Agregar Más
                                    </button>
                                </div>
                                <div class="pages-grid" id="pagesGrid">
                                    <!-- Las páginas se mostrarán aquí -->
                                </div>
                                <p class="text-muted"><i class="fas fa-arrows-alt me-1"></i>Arrastra las páginas para reordenarlas</p>
                            </div>
                        </div>
                        @error('pages')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        @error('pages.*')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg" id="createBtn">
                            <span class="btn-text">
                                <i class="fas fa-save me-2"></i>Crear Comic
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Creando...
                            </span>
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
let selectedPages = [];
let draggedFiles = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando formulario de comic');
    
    // Character counter para sinopsis
    const synopsis = document.getElementById('synopsis');
    const synopsisCount = document.getElementById('synopsisCount');
    
    synopsis.addEventListener('input', function() {
        synopsisCount.textContent = this.value.length;
    });
    
    // Update count on page load
    synopsisCount.textContent = synopsis.value.length;
    
    // Portada upload
    document.getElementById('cover_image').addEventListener('change', handleCoverUpload);
    
    // Páginas upload
    document.getElementById('pages').addEventListener('change', handlePagesUpload);
    
    // Drag and drop para páginas
    setupPagesDragDrop();
    
    // Form submission
    document.getElementById('comicForm').addEventListener('submit', function() {
        const btn = document.getElementById('createBtn');
        const btnText = btn.querySelector('.btn-text');
        const btnLoader = btn.querySelector('.btn-loader');
        
        btn.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';
    });
});

function handleCoverUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    if (!file.type.match(/^image\/(jpeg|png|jpg)$/)) {
        alert('La portada debe ser JPG, PNG o JPEG');
        e.target.value = '';
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        alert('La portada no debe superar los 5MB');
        e.target.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        showCoverPreview(e.target.result);
    };
    reader.readAsDataURL(file);
}

function showCoverPreview(src) {
    const preview = document.getElementById('coverPreview');
    const controls = document.getElementById('coverControls');
    
    preview.innerHTML = `<img src="${src}" alt="Portada" class="cover-image">`;
    controls.style.display = 'flex';
}

function removeCover() {
    const preview = document.getElementById('coverPreview');
    const controls = document.getElementById('coverControls');
    const input = document.getElementById('cover_image');
    
    preview.innerHTML = `
        <div class="cover-placeholder">
            <i class="fas fa-image fa-3x"></i>
            <h6>Sube la Portada</h6>
            <p>JPG, PNG hasta 5MB</p>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('cover_image').click()">
                <i class="fas fa-upload me-1"></i>Seleccionar
            </button>
        </div>
    `;
    controls.style.display = 'none';
    input.value = '';
}

function handlePagesUpload(e) {
    const files = Array.from(e.target.files);
    if (files.length === 0) return;
    
    // Validar archivos
    for (let file of files) {
        if (!file.type.match(/^image\/(jpeg|png|jpg)$/)) {
            alert('Todas las páginas deben ser JPG, PNG o JPEG');
            e.target.value = '';
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            alert('Cada página no debe superar los 10MB');
            e.target.value = '';
            return;
        }
    }
    
    // Agregar a la lista de páginas
    files.forEach(file => {
        if (!selectedPages.find(p => p.name === file.name && p.size === file.size)) {
            selectedPages.push(file);
        }
    });
    
    updatePagesPreview();
}

function updatePagesPreview() {
    const dropzone = document.getElementById('pagesDropzone');
    const preview = document.getElementById('pagesPreview');
    const grid = document.getElementById('pagesGrid');
    const count = document.getElementById('pageCount');
    
    if (selectedPages.length > 0) {
        dropzone.style.display = 'none';
        preview.style.display = 'block';
        
        count.textContent = selectedPages.length;
        
        grid.innerHTML = '';
        selectedPages.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const pageDiv = document.createElement('div');
                pageDiv.className = 'page-item';
                pageDiv.innerHTML = `
                    <div class="page-number">${index + 1}</div>
                    <img src="${e.target.result}" alt="Página ${index + 1}" class="page-thumbnail">
                    <div class="page-info">
                        <small>${file.name}</small>
                        <small>${(file.size / 1024 / 1024).toFixed(1)}MB</small>
                    </div>
                    <button type="button" class="page-remove" onclick="removePage(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="page-drag-handle">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                `;
                grid.appendChild(pageDiv);
            };
            reader.readAsDataURL(file);
        });
        
        // Inicializar sortable después de que se carguen las imágenes
        setTimeout(() => {
            initSortable();
        }, 500);
    } else {
        dropzone.style.display = 'block';
        preview.style.display = 'none';
    }
}

function removePage(index) {
    selectedPages.splice(index, 1);
    updatePagesPreview();
    updateFileInput();
}

function updateFileInput() {
    const input = document.getElementById('pages');
    const dt = new DataTransfer();
    
    selectedPages.forEach(file => {
        dt.items.add(file);
    });
    
    input.files = dt.files;
}

function setupPagesDragDrop() {
    const dropzone = document.getElementById('pagesDropzone');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, unhighlight, false);
    });
    
    dropzone.addEventListener('drop', handleDrop, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    document.getElementById('pagesDropzone').classList.add('dragover');
}

function unhighlight(e) {
    document.getElementById('pagesDropzone').classList.remove('dragover');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = Array.from(dt.files);
    
    // Simular selección de archivos
    files.forEach(file => {
        if (!selectedPages.find(p => p.name === file.name && p.size === file.size)) {
            selectedPages.push(file);
        }
    });
    
    updatePagesPreview();
    updateFileInput();
}

function initSortable() {
    const grid = document.getElementById('pagesGrid');
    if (grid && selectedPages.length > 1) {
        new Sortable(grid, {
            animation: 150,
            handle: '.page-drag-handle',
            onEnd: function(evt) {
                // Reordenar array
                const item = selectedPages.splice(evt.oldIndex, 1)[0];
                selectedPages.splice(evt.newIndex, 0, item);
                
                // Actualizar números de página
                updatePageNumbers();
                updateFileInput();
            }
        });
    }
}

function updatePageNumbers() {
    const pageItems = document.querySelectorAll('.page-item');
    pageItems.forEach((item, index) => {
        const pageNumber = item.querySelector('.page-number');
        pageNumber.textContent = index + 1;
    });
}
</script>
@endsection