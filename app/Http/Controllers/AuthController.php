<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Procesar registro de usuario
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users|regex:/^[a-zA-Z0-9_\-\.]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'El nombre de usuario es obligatorio',
            'name.unique' => 'Este nombre de usuario ya está en uso',
            'name.regex' => 'El nombre solo puede contener letras, números, guiones y puntos',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Por favor ingresa un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya tiene una cuenta registrada',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial',
            'profile_photo.image' => 'El archivo debe ser una imagen',
            'profile_photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif',
            'profile_photo.max' => 'La imagen no debe superar los 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Crear el usuario
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        // Manejar la foto de perfil si se subió
        if ($request->hasFile('profile_photo')) {
            try {
                $photo = $request->file('profile_photo');
                $photoName = 'profile_' . time() . '_' . $request->name . '.' . $photo->getClientOriginalExtension();
                
                // Crear directorio si no existe
                $profilePicsPath = 'C:\Users\Temporal\Desktop\comics\contents\profilepics';
                if (!file_exists($profilePicsPath)) {
                    mkdir($profilePicsPath, 0755, true);
                }
                
                // Mover archivo a la carpeta de fotos de perfil
                $photo->move($profilePicsPath, $photoName);
                
                // Solo guardamos el nombre del archivo en la BD
                $userData['photo'] = $photoName;
                
            } catch (\Exception $e) {
                return redirect()->back()
                               ->withErrors(['profile_photo' => 'Error al subir la foto. Intenta de nuevo.'])
                               ->withInput();
            }
        }

        $user = User::create($userData);

        // Autenticar automáticamente después del registro
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '¡Bienvenido a Comics App! Tu cuenta se creó exitosamente 🎉');
    }

    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar login de usuario
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'))->with('success', 'Bienvenido de vuelta!');
        }

        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Mostrar perfil de usuario
     */
    public function profile()
    {
        $user = Auth::user();
        $userComics = $user->comics()->withCount(['ratings', 'favoritedBy'])->get();
        $favoriteComics = $user->favoriteComics()->with('language')->get();
        
        return view('auth.profile', compact('user', 'userComics', 'favoriteComics'));
    }

    /**
     * Actualizar perfil de usuario
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users,name,' . $user->id . '|regex:/^[a-zA-Z0-9_\-\.]+$/',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_photo' => 'nullable|boolean',
        ], [
            'name.required' => 'El nombre de usuario es obligatorio',
            'name.unique' => 'Este nombre de usuario ya está en uso',
            'name.regex' => 'El nombre solo puede contener letras, números, guiones y puntos',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Por favor ingresa un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial',
            'profile_photo.image' => 'El archivo debe ser una imagen',
            'profile_photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif',
            'profile_photo.max' => 'La imagen no debe superar los 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Verificar contraseña actual si se quiere cambiar
        if ($request->filled('password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                               ->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])
                               ->withInput();
            }
        }

        // Actualizar datos básicos
        $user->name = $request->name;
        $user->email = $request->email;

        // Actualizar contraseña si se proporcionó
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Manejar foto de perfil
        if ($request->boolean('remove_photo')) {
            // Eliminar foto actual si existe
            if ($user->photo) {
                $photoPath = 'C:\Users\Temporal\Desktop\comics\contents\profilepics\\' . $user->photo;
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
                $user->photo = null;
            }
        } elseif ($request->hasFile('profile_photo')) {
            try {
                // Eliminar foto anterior si existe
                if ($user->photo) {
                    $oldPhotoPath = 'C:\Users\Temporal\Desktop\comics\contents\profilepics\\' . $user->photo;
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }

                $photo = $request->file('profile_photo');
                $photoName = 'profile_' . time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
                
                // Crear directorio si no existe
                $profilePicsPath = 'C:\Users\Temporal\Desktop\comics\contents\profilepics';
                if (!file_exists($profilePicsPath)) {
                    mkdir($profilePicsPath, 0755, true);
                }
                
                // Mover archivo a la carpeta de fotos de perfil
                $photo->move($profilePicsPath, $photoName);
                
                $user->photo = $photoName;
                
            } catch (\Exception $e) {
                return redirect()->back()
                               ->withErrors(['profile_photo' => 'Error al subir la foto. Intenta de nuevo.'])
                               ->withInput();
            }
        }

        $user->save();

        return redirect()->route('profile')->with('success', '¡Perfil actualizado correctamente! 🎉');
    }

    /**
     * API: Registro de usuario (para uso con API)
     */
    public function apiRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * API: Login de usuario (para uso con API)
     */
    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciales incorrectas'
        ], 401);
    }

    /**
     * API: Logout de usuario (para uso con API)
     */
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ]);
    }
}