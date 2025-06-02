<div class="form-section">
    <h6 class="section-title">
        <i class="fas fa-file-image me-2"></i>Gestión de Páginas
        <span class="badge bg-primary ms-2">{{ $comic->pages->count() }} páginas</span>
    </h6>
    
    <div class="pages-management-container">
        <div class="pages-actions mb-3">
            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('newPages').click()">
                <i class="fas fa-plus me-2"></i>Agregar Páginas
            </button>
            <button type="button" class="btn btn-outline-info" onclick="toggleReorderMode()">
                <i class="fas fa-arrows-alt me-2"></i>Reordenar Páginas
            </button>
            <button type="button" class="btn btn-outline-warning" id="saveOrderBtn" onclick="savePageOrder()" style="display: none;">
                <i class="fas fa-save me-2"></i>Guardar Orden
            </button>
            <button type="button" class="btn btn-outline-secondary" id="cancelOrderBtn" onclick="cancelReorder()" style="display: none;">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
        </div>
        
        <!-- Input oculto para nuevas páginas -->
        <input type="file" id="newPages" name="new_pages[]" accept="image/jpeg,image/png,image/jpg" multiple style="display: none;">
        
        <!-- Páginas existentes con controles -->
        <div class="current-pages-grid" id="pagesContainer">
            @foreach($comic->pages as $page)
                <div class="page-management-item" data-page-id="{{ $page->id }}" data-page-number="{{ $page->page_number }}">
                    <div class="page-item-header">
                        <span class="page-number-badge">{{ $page->page_number }}</span>
                        <div class="page-controls">
                            <button type="button" class="btn-page-control btn-delete" onclick="deletePage({{ $page->id }})" title="Eliminar página">
                                <i class="fas fa-trash"></i>
                            </button>
                            <div class="drag-handle" title="Arrastra para reordenar" style="display: none;">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                        </div>
                    </div>
                    <div class="page-thumbnail-container">
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($page->image_url)) }}" 
                             alt="Página {{ $page->page_number }}" 
                             class="page-management-thumb">
                    </div>
                    <div class="page-info-small">
                        <small>{{ $page->image_path }}</small>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Nuevas páginas a agregar -->
        <div class="new-pages-container" id="newPagesContainer" style="display: none;">
            <h6 class="text-success mt-3">
                <i class="fas fa-plus-circle me-2"></i>Nuevas Páginas a Agregar
            </h6>
            <div class="new-pages-grid" id="newPagesGrid">
                <!-- Las nuevas páginas se mostrarán aquí -->
            </div>
        </div>
    </div>
</div>