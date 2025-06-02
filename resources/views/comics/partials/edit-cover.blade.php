<div class="form-section">
    <h6 class="section-title">
        <i class="fas fa-image me-2"></i>Portada del Comic
    </h6>
    
    <!-- Portada actual -->
    <div class="current-cover-section mb-3">
        <label class="form-label">Portada Actual</label>
        <div class="current-cover-preview">
            @if($comic->cover_image)
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($comic->cover_image_url)) }}" 
                     alt="Portada actual" 
                     class="current-cover-image">
                <div class="cover-info">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Archivo: {{ $comic->cover_image }}
                    </small>
                </div>
            @else
                <div class="no-cover">
                    <i class="fas fa-image fa-3x"></i>
                    <p>Sin portada</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Nueva portada (opcional) -->
    <div class="cover-upload-container">
        <label class="form-label">
            <i class="fas fa-upload me-2"></i>Nueva Portada (Opcional)
        </label>
        <div class="cover-preview" id="coverPreview">
            <div class="cover-placeholder">
                <i class="fas fa-image fa-3x"></i>
                <h6>Cambiar Portada</h6>
                <p>JPG, PNG hasta 5MB</p>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('cover_image').click()">
                    <i class="fas fa-upload me-1"></i>Seleccionar
                </button>
            </div>
        </div>
        <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/jpg" style="display: none;">
        
        <div class="cover-controls" id="coverControls" style="display: none;">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('cover_image').click()">
                <i class="fas fa-edit me-1"></i>Cambiar
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCover()">
                <i class="fas fa-trash me-1"></i>Quitar
            </button>
        </div>
        
        <small class="form-text">
            <i class="fas fa-info-circle me-1"></i>
            Solo sube una nueva imagen si quieres cambiar la portada actual.
        </small>
    </div>
    @error('cover_image')
        <div class="error-message">
            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
        </div>
    @enderror
</div>