@extends('layouts.app')

@section('title', 'Iniciar Sesión - Comics App')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="auth-card">
            <div class="auth-header text-center">
                <div class="auth-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <h4 class="auth-title">Iniciar Sesión</h4>
                <p class="auth-subtitle">Accede a tu cuenta para continuar</p>
            </div>
            
            <div class="auth-body">
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Email -->
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
                                @if($message === 'Las credenciales proporcionadas no coinciden con nuestros registros.')
                                    Hmm, parece que ese email no está registrado o la contraseña no coincide 🤔
                                @else
                                    {{ $message }}
                                @endif
                            </div>
                        @enderror
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
                                   placeholder="Tu contraseña secreta">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="passwordEye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                ¡Ups! La contraseña es requerida para acceder 🔐
                            </div>
                        @enderror
                    </div>

                    <!-- Recordarme -->
                    <div class="form-group">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="remember" name="remember" class="checkbox-input">
                            <label for="remember" class="checkbox-label">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">Recordarme por 30 días</span>
                            </label>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-auth btn-primary" id="loginBtn">
                            <span class="btn-text">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Iniciando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="auth-footer text-center">
                <p class="auth-link">
                    ¿No tienes una cuenta? 
                    <a href="{{ route('register') }}" class="link-primary">
                        <i class="fas fa-user-plus me-1"></i>Regístrate aquí
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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

// Loading state para el botón
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    const btnText = btn.querySelector('.btn-text');
    const btnLoader = btn.querySelector('.btn-loader');
    
    btn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline';
});

// Animación de focus en inputs
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.classList.remove('focused');
        }
    });
    
    // Mantener el estado si ya tiene valor
    if (input.value) {
        input.parentElement.classList.add('focused');
    }
});
</script>
@endsection