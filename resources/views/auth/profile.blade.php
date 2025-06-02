@extends('layouts.app')

@section('title', 'Mi Perfil - Comics App')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-gradient">
                <i class="fas fa-user-edit me-2"></i>Mi Perfil
            </h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Información del perfil -->
    <div class="col-lg-4 mb-4">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    @if($user->hasProfilePhoto())
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($user->photo_url)) }}" 
                             alt="Foto de perfil" 
                             class="avatar-image">
                    @else
                        <div class="avatar-placeholder">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    @endif
                </div>
                <h4 class="profile-name">{{ $user->name }}</h4>
                <p class="profile-email">{{ $user->email }}</p>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ $userComics->count() }}</span>
                        <span class="stat-label">Comics</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $favoriteComics->count() }}</span>
                        <span class="stat-label">Favoritos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $user->ratings->count() }}</span>
                        <span class="stat-label">Calificaciones</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="info-card mt-4">
            <div class="info-header">
                <h6><i class="fas fa-info-circle me-2"></i>Información de la Cuenta</h6>
            </div>
            <div class="info-body">
                <div class="info-item">
                    <span class="info-label">Miembro desde:</span>
                    <span class="info-value">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Última actualización:</span>
                    <span class="info-value">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estado:</span>
                    <span class="badge bg-success">Activo</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de edición -->
    <div class="col-lg-8">
        <div class="edit-card">
            <div class="edit-header">
                <h5><i class="fas fa-edit me-2"></i>Editar Información</h5>
            </div>
            <div class="edit-body">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('PUT')

                    <!-- Foto de perfil -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-camera me-2"></i>Cambiar Foto de Perfil
                        </label>
                        <div class="photo-upload-container">
                            <div class="current-photo">
                                @if($user->hasProfilePhoto())
                                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($user->photo_url)) }}" 
                                         alt="Foto actual" 
                                         class="current-photo-img" 
                                         id="currentPhotoPreview">
                                @else
                                    <div class="no-photo" id="currentPhotoPreview">
                                        <i class="fas fa-user fa-3x"></i>
                                        <p>Sin foto</p>
                                    </div>
                                @endif
                            </div>
                            <div class="photo-upload-controls">
                                <input type="file" id="newProfilePhoto" name="profile_photo" accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('newProfilePhoto').click()">
                                    <i class="fas fa-upload me-1"></i>
                                    @if($user->hasProfilePhoto())
                                        Cambiar Foto
                                    @else
                                        Subir Foto
                                    @endif
                                </button>
                                @if($user->hasProfilePhoto())
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCurrentPhoto()">
                                        <i class="fas fa-trash me-1"></i>Quitar Foto
                                    </button>
                                @endif
                            </div>
                            <small class="form-text">PNG, JPG, JPEG hasta 2MB. Los GIF no se pueden recortar.</small>
                        </div>
                        <input type="hidden" name="remove_photo" id="removePhotoFlag" value="0">
                    </div>

                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nombre de Usuario *
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                @error('name')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Correo Electrónico *
                                </label>
                                <div class="input-wrapper">
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    <div class="input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                @error('email')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Cambiar contraseña (opcional) -->
                    <div class="password-section">
                        <h6 class="section-title">
                            <i class="fas fa-lock me-2"></i>Cambiar Contraseña (Opcional)
                        </h6>
                        
                        <!-- Contraseña actual -->
                        <div class="form-group">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <div class="input-wrapper password-wrapper">
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Tu contraseña actual">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_passwordEye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Nueva contraseña -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <div class="input-wrapper password-wrapper">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Nueva contraseña"
                                               oninput="checkPasswordStrength()">
                                        <div class="input-icon">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="passwordEye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirmar nueva contraseña -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                    <div class="input-wrapper password-wrapper">
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               placeholder="Confirmar nueva contraseña"
                                               oninput="checkPasswordMatch()">
                                        <div class="input-icon">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye" id="password_confirmationEye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Indicadores de contraseña -->
                        <div class="password-strength" id="passwordStrength" style="display: none;">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="strength-text" id="strengthText"></div>
                        </div>
                        
                        <div class="password-match" id="passwordMatch" style="display: none;"></div>
                    </div>

                    <!-- Botones -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="updateBtn">
                            <span class="btn-text">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Guardando...
                            </span>
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mis Comics -->
@if($userComics->count() > 0)
<div class="row mt-5">
    <div class="col-12">
        <div class="comics-section">
            <h5 class="section-title">
                <i class="fas fa-book me-2"></i>Mis Comics ({{ $userComics->count() }})
            </h5>
            <div class="row">
                @foreach($userComics->take(6) as $comic)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                        <div class="mini-comic-card">
                            <div class="mini-comic-image">
                                <i class="fas fa-book fa-2x"></i>
                            </div>
                            <div class="mini-comic-info">
                                <h6 class="mini-comic-title">{{ Str::limit($comic->title, 15) }}</h6>
                                <div class="mini-comic-stats">
                                    <small>
                                        <i class="fas fa-star text-warning"></i> 
                                        {{ $comic->ratings_count ?? 0 }}
                                    </small>
                                    <small>
                                        <i class="fas fa-heart text-danger"></i> 
                                        {{ $comic->favorited_by_count ?? 0 }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
<!-- Modal para recortar imagen -->
<div class="modal fade" id="profileCropModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar Foto de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="crop-container">
                    <img id="profileCropImage" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="applyProfileCrop()">Aplicar Recorte</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<script>
let profileCropper = null;
let currentPhotoFile = null;

// Esperar a que se cargue completamente el DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando eventos');
    
    // Verificar que el elemento existe
    const photoInput = document.getElementById('newProfilePhoto');
    if (!photoInput) {
        console.error('No se encontró el elemento newProfilePhoto');
        return;
    }
    
    console.log('Elemento newProfilePhoto encontrado, agregando evento');
    
    // Manejo de foto nueva con recorte
    photoInput.addEventListener('change', function(e) {
        console.log('Archivo seleccionado:', e.target.files[0]);
        
        const file = e.target.files[0];
        if (!file) {
            console.log('No hay archivo');
            return;
        }
        
        // Check file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('La imagen debe ser menor a 2MB');
            this.value = '';
            return;
        }
        
        console.log('Archivo válido, tipo:', file.type);
        currentPhotoFile = file;
        
        // Check if it's a GIF
        if (file.type === 'image/gif') {
            console.log('Es un GIF, mostrando preview directo');
            // For GIFs, just show preview without cropping
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log('GIF cargado, actualizando preview');
                updatePhotoPreview(e.target.result, true);
            };
            reader.readAsDataURL(file);
        } else {
            console.log('No es GIF, mostrando modal de recorte');
            // For other images, show crop modal
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log('Imagen cargada, abriendo modal');
                showProfileCropModal(e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Initialize button states on page load
    const hasCurrentPhoto = document.querySelector('#currentPhotoPreview img') !== null;
    updatePhotoButtons(hasCurrentPhoto);
    console.log('Eventos inicializados correctamente');
});

// Reutilizar funciones del registro
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(inputId + 'Eye');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthContainer = document.getElementById('passwordStrength');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    if (password.length === 0) {
        strengthContainer.style.display = 'none';
        return;
    }
    
    strengthContainer.style.display = 'block';
    
    let score = 0;
    if (password.length >= 8) score += 25;
    if (password.length >= 12) score += 15;
    if (/[a-z]/.test(password)) score += 10;
    if (/[A-Z]/.test(password)) score += 10;
    if (/[0-9]/.test(password)) score += 15;
    if (/[^A-Za-z0-9]/.test(password)) score += 25;
    
    strengthFill.style.width = score + '%';
    
    if (score < 30) {
        strengthFill.className = 'strength-fill weak';
        strengthText.textContent = '🔴 Muy débil';
    } else if (score < 60) {
        strengthFill.className = 'strength-fill medium';
        strengthText.textContent = '🟡 Débil';
    } else if (score < 80) {
        strengthFill.className = 'strength-fill good';
        strengthText.textContent = '🟠 Buena';
    } else {
        strengthFill.className = 'strength-fill strong';
        strengthText.textContent = '🟢 ¡Excelente!';
    }
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const matchDiv = document.getElementById('passwordMatch');
    
    if (confirmation.length === 0) {
        matchDiv.style.display = 'none';
        return;
    }
    
    matchDiv.style.display = 'block';
    
    if (password === confirmation) {
        matchDiv.innerHTML = '<i class="fas fa-check text-success me-1"></i>¡Las contraseñas coinciden! 👍';
        matchDiv.className = 'password-match success';
    } else {
        matchDiv.innerHTML = '<i class="fas fa-times text-danger me-1"></i>Las contraseñas no coinciden 😕';
        matchDiv.className = 'password-match error';
    }
}

function showProfileCropModal(imageSrc) {
    console.log('Abriendo modal de recorte');
    const cropImage = document.getElementById('profileCropImage');
    cropImage.src = imageSrc;
    
    const modalElement = document.getElementById('profileCropModal');
    if (!modalElement) {
        console.error('No se encontró el modal profileCropModal');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    console.log('Modal mostrado, inicializando cropper');
    
    // Initialize cropper when modal is shown
    modalElement.addEventListener('shown.bs.modal', function() {
        console.log('Modal completamente mostrado');
        if (profileCropper) {
            profileCropper.destroy();
        }
        
        profileCropper = new Cropper(cropImage, {
            aspectRatio: 1,
            viewMode: 2,
            dragMode: 'move',
            autoCropArea: 0.8,
            restore: false,
            guides: false,
            center: false,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
        console.log('Cropper inicializado');
    }, { once: true });
}

function applyProfileCrop() {
    console.log('Aplicando recorte');
    if (!profileCropper) {
        console.error('No hay cropper inicializado');
        return;
    }
    
    const canvas = profileCropper.getCroppedCanvas({
        width: 300,
        height: 300,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });
    
    console.log('Canvas creado');
    const croppedImageDataURL = canvas.toDataURL('image/jpeg', 0.8);
    updatePhotoPreview(croppedImageDataURL, false);
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('profileCropModal'));
    modal.hide();
    
    console.log('Convirtiendo a blob');
    // Convert to blob and create new file for form submission
    canvas.toBlob(function(blob) {
        console.log('Blob creado, tamaño:', blob.size);
        const croppedFile = new File([blob], currentPhotoFile.name, {
            type: 'image/jpeg',
            lastModified: Date.now()
        });
        
        // Create new FileList and assign to input
        const dt = new DataTransfer();
        dt.items.add(croppedFile);
        document.getElementById('newProfilePhoto').files = dt.files;
        console.log('Archivo asignado al input');
    }, 'image/jpeg', 0.8);
}

function updatePhotoPreview(imageSrc, isGif) {
    console.log('Actualizando preview, esGif:', isGif);
    const preview = document.getElementById('currentPhotoPreview');
    
    let content = `<img src="${imageSrc}" alt="Nueva foto" class="current-photo-img">`;
    if (isGif) {
        content += '<div class="gif-badge">GIF</div>';
    }
    
    preview.innerHTML = content;
    document.getElementById('removePhotoFlag').value = '0';
    
    // Update buttons
    updatePhotoButtons(true);
    console.log('Preview actualizado');
}

function removeCurrentPhoto() {
    const preview = document.getElementById('currentPhotoPreview');
    const fileInput = document.getElementById('newProfilePhoto');
    
    preview.innerHTML = `
        <div class="no-photo">
            <i class="fas fa-user fa-3x"></i>
            <p>Sin foto</p>
        </div>
    `;
    fileInput.value = '';
    document.getElementById('removePhotoFlag').value = '1';
    currentPhotoFile = null;
    
    // Update buttons
    updatePhotoButtons(false);
}

function updatePhotoButtons(hasNewPhoto) {
    const uploadBtn = document.querySelector('.photo-upload-controls .btn-outline-primary');
    const removeBtn = document.querySelector('.photo-upload-controls .btn-outline-danger');
    
    if (hasNewPhoto) {
        uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i>Cambiar Foto';
        if (!removeBtn) {
            const newRemoveBtn = document.createElement('button');
            newRemoveBtn.type = 'button';
            newRemoveBtn.className = 'btn btn-outline-danger btn-sm';
            newRemoveBtn.onclick = removeCurrentPhoto;
            newRemoveBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Quitar Foto';
            uploadBtn.parentNode.appendChild(newRemoveBtn);
        } else {
            removeBtn.style.display = 'inline-block';
        }
    } else {
        uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i>Subir Foto';
        if (removeBtn) {
            removeBtn.style.display = 'none';
        }
    }
}

// Loading state
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function() {
            const btn = document.getElementById('updateBtn');
            const btnText = btn.querySelector('.btn-text');
            const btnLoader = btn.querySelector('.btn-loader');
            
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline';
        });
    }
});
</script>