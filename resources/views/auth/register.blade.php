@extends('layouts.app')

@section('title', 'Registrarse - Comics App')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="auth-card">
            <div class="auth-header text-center">
                <div class="auth-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h4 class="auth-title">Crear Cuenta</h4>
                <p class="auth-subtitle">Únete a nuestra comunidad de comics</p>
            </div>
            
            <div class="auth-body">
                <form method="POST" action="{{ route('register') }}" id="registerForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Foto de perfil -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-camera me-2"></i>Foto de Perfil (Opcional)
                        </label>
                        <div class="photo-upload-container">
                            <div class="photo-preview" id="photoPreview">
                                <div class="photo-placeholder">
                                    <i class="fas fa-user fa-3x"></i>
                                    <p>Sube tu foto</p>
                                </div>
                            </div>
                            <div class="photo-upload-controls">
                                <input type="file" id="profilePhoto" name="profile_photo" accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('profilePhoto').click()">
                                    <i class="fas fa-upload me-1"></i>Seleccionar Foto
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="removePhoto" style="display: none;" onclick="removePhoto()">
                                    <i class="fas fa-trash me-1"></i>Quitar
                                </button>
                            </div>
                            <small class="form-text">PNG, JPG, JPEG hasta 2MB. Los GIF no se pueden recortar.</small>
                        </div>
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
                                           value="{{ old('name') }}" 
                                           required 
                                           autocomplete="name"
                                           placeholder="Tu nombre genial">
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                @error('name')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        @if(str_contains($message, 'unique'))
                                            ¡Ese nombre ya está en uso! Intenta con otro 😊
                                        @else
                                            {{ $message }}
                                        @endif
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
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="email"
                                           placeholder="tu@email.com">
                                    <div class="input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                @error('email')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        @if(str_contains($message, 'unique'))
                                            ¡Ese email ya tiene cuenta! ¿Quizás quieras <a href="{{ route('login') }}">iniciar sesión</a>? 🤔
                                        @else
                                            {{ $message }}
                                        @endif
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Contraseña *
                        </label>
                        <div class="input-wrapper password-wrapper">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required
                                   placeholder="Crea una contraseña segura"
                                   oninput="checkPasswordStrength()">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="passwordEye"></i>
                            </button>
                        </div>
                        
                        <!-- Barra de seguridad de contraseña -->
                        <div class="password-strength" id="passwordStrength" style="display: none;">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="strength-text" id="strengthText"></div>
                        </div>
                        
                        <div class="password-requirements" id="passwordRequirements">
                            <div class="requirement" id="req-length">
                                <i class="fas fa-times text-danger"></i>
                                <span>Mínimo 8 caracteres</span>
                            </div>
                            <div class="requirement" id="req-number">
                                <i class="fas fa-times text-danger"></i>
                                <span>Al menos un número</span>
                            </div>
                            <div class="requirement" id="req-special">
                                <i class="fas fa-times text-danger"></i>
                                <span>Al menos un carácter especial</span>
                            </div>
                        </div>
                        
                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock me-2"></i>Confirmar Contraseña *
                        </label>
                        <div class="input-wrapper password-wrapper">
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   placeholder="Repite tu contraseña"
                                   oninput="checkPasswordMatch()">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmationEye"></i>
                            </button>
                        </div>
                        <div class="password-match" id="passwordMatch" style="display: none;"></div>
                    </div>

                    <!-- Botón de envío -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-auth btn-primary" id="registerBtn">
                            <span class="btn-text">
                                <i class="fas fa-user-plus me-2"></i>Crear Mi Cuenta
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Creando cuenta...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="auth-footer text-center">
                <p class="auth-link">
                    ¿Ya tienes una cuenta? 
                    <a href="{{ route('login') }}" class="link-primary">
                        <i class="fas fa-sign-in-alt me-1"></i>Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal para recortar imagen -->
<div class="modal fade" id="cropModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar Foto de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="crop-container">
                    <img id="cropImage" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="applyCrop()">Aplicar Recorte</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<script>
let cropper = null;
let currentFile = null;

// Toggle password visibility
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

// Check password strength
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
    let feedback = '';
    
    // Length check
    if (password.length >= 8) score += 25;
    if (password.length >= 12) score += 15;
    
    // Character variety
    if (/[a-z]/.test(password)) score += 10;
    if (/[A-Z]/.test(password)) score += 10;
    if (/[0-9]/.test(password)) score += 15;
    if (/[^A-Za-z0-9]/.test(password)) score += 25;
    
    // Update visual indicator
    strengthFill.style.width = score + '%';
    
    if (score < 30) {
        strengthFill.className = 'strength-fill weak';
        feedback = '🔴 Muy débil';
    } else if (score < 60) {
        strengthFill.className = 'strength-fill medium';
        feedback = '🟡 Débil';
    } else if (score < 80) {
        strengthFill.className = 'strength-fill good';
        feedback = '🟠 Buena';
    } else {
        strengthFill.className = 'strength-fill strong';
        feedback = '🟢 ¡Excelente!';
    }
    
    strengthText.textContent = feedback;
    
    // Update requirements
    updateRequirement('req-length', password.length >= 8);
    updateRequirement('req-number', /[0-9]/.test(password));
    updateRequirement('req-special', /[^A-Za-z0-9]/.test(password));
}

function updateRequirement(reqId, met) {
    const req = document.getElementById(reqId);
    const icon = req.querySelector('i');
    
    if (met) {
        icon.className = 'fas fa-check text-success';
        req.classList.add('met');
    } else {
        icon.className = 'fas fa-times text-danger';
        req.classList.remove('met');
    }
}

// Check password match
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

// Photo upload handling
document.getElementById('profilePhoto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Check file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        alert('La imagen debe ser menor a 2MB');
        return;
    }
    
    currentFile = file;
    
    // Check if it's a GIF
    if (file.type === 'image/gif') {
        // For GIFs, just show preview without cropping
        const reader = new FileReader();
        reader.onload = function(e) {
            showPhotoPreview(e.target.result, true);
        };
        reader.readAsDataURL(file);
    } else {
        // For other images, show crop modal
        const reader = new FileReader();
        reader.onload = function(e) {
            showCropModal(e.target.result);
        };
        reader.readAsDataURL(file);
    }
});

function showCropModal(imageSrc) {
    const cropImage = document.getElementById('cropImage');
    cropImage.src = imageSrc;
    
    const modal = new bootstrap.Modal(document.getElementById('cropModal'));
    modal.show();
    
    // Initialize cropper when modal is shown
    document.getElementById('cropModal').addEventListener('shown.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
        }
        
        cropper = new Cropper(cropImage, {
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
    }, { once: true });
}

function applyCrop() {
    if (!cropper) return;
    
    const canvas = cropper.getCroppedCanvas({
        width: 300,
        height: 300,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });
    
    const croppedImageDataURL = canvas.toDataURL('image/jpeg', 0.8);
    showPhotoPreview(croppedImageDataURL, false);
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('cropModal'));
    modal.hide();
    
    // Convert to blob and create new file for form submission
    canvas.toBlob(function(blob) {
        const croppedFile = new File([blob], currentFile.name, {
            type: 'image/jpeg',
            lastModified: Date.now()
        });
        
        // Create new FileList and assign to input
        const dt = new DataTransfer();
        dt.items.add(croppedFile);
        document.getElementById('profilePhoto').files = dt.files;
    }, 'image/jpeg', 0.8);
}

function showPhotoPreview(imageSrc, isGif) {
    const preview = document.getElementById('photoPreview');
    const removeBtn = document.getElementById('removePhoto');
    
    preview.innerHTML = `<img src="${imageSrc}" alt="Preview" class="preview-image">`;
    removeBtn.style.display = 'inline-block';
    
    if (isGif) {
        preview.innerHTML += '<div class="gif-badge">GIF</div>';
    }
}

function removePhoto() {
    const preview = document.getElementById('photoPreview');
    const removeBtn = document.getElementById('removePhoto');
    const fileInput = document.getElementById('profilePhoto');
    
    preview.innerHTML = `
        <div class="photo-placeholder">
            <i class="fas fa-user fa-3x"></i>
            <p>Sube tu foto</p>
        </div>
    `;
    removeBtn.style.display = 'none';
    fileInput.value = '';
    currentFile = null;
}

// Form submission
document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('registerBtn');
    const btnText = btn.querySelector('.btn-text');
    const btnLoader = btn.querySelector('.btn-loader');
    
    btn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline';
});

// Input animations
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.classList.remove('focused');
        }
    });
    
    if (input.value) {
        input.parentElement.classList.add('focused');
    }
});
</script>
@endsection