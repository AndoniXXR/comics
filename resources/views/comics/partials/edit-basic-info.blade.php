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
                           value="{{ old('title', $comic->title) }}" 
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
                                    {{ old('language_id', $comic->language_id) == $language->id ? 'selected' : '' }}>
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
                   value="{{ old('author', $comic->author) }}" 
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
                      placeholder="Describe de qué trata tu comic...">{{ old('synopsis', $comic->synopsis) }}</textarea>
            <div class="input-icon">
                <i class="fas fa-align-left"></i>
            </div>
        </div>
        <div class="character-count">
            <span id="synopsisCount">{{ strlen($comic->synopsis) }}</span>/1000 caracteres
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
                       {{ old('status', $comic->status) === 'draft' ? 'checked' : '' }}>
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
                       {{ old('status', $comic->status) === 'published' ? 'checked' : '' }}>
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