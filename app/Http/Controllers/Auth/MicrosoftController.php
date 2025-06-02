<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class MicrosoftController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de Microsoft.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    /**
     * Obtiene la información del usuario de Microsoft después de la autenticación.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();

            //Validar que el usuarios exista en la base de datos
            $user = User::where('email', $microsoftUser->email)->first();
            if (!$user) {
                return redirect('/')->with('error', 'Error al iniciar sesión con Microsoft: Usuario no encontrado');
            }

            // Buscar usuario existente o crear uno nuevo
            $user = User::where('email', $microsoftUser->email)->first();
            
            if (!$user) {
                // Crear nuevo usuario
                $user = User::create([
                    'name' => $microsoftUser->name,
                    'email' => $microsoftUser->email,
                    'password' =>  Hash::make($microsoftUser->email), // Hash::make(Str::random(16))  Contraseña aleatoria
                    'microsoft_id' => $microsoftUser->id,
                    'microsoft_token' => $microsoftUser->token,
                    'microsoft_refresh_token' => $microsoftUser->refreshToken,
                    'microsoft_token_expires_at' => now()->addSeconds($microsoftUser->expiresIn),
                ]);
            } else {
                // Actualizar información de Microsoft para usuario existente
                $user->update([
                    'microsoft_id' => $microsoftUser->id,
                    'microsoft_token' => $microsoftUser->token,
                    'microsoft_refresh_token' => $microsoftUser->refreshToken,
                    'microsoft_token_expires_at' => now()->addSeconds($microsoftUser->expiresIn),
                ]);
            }
            
            // Iniciar sesión
            Auth::login($user);
            
            // Redirigir al dashboard o a la página principal
            return redirect('/Dashboard');
            
        } catch (Exception $e) {
            Log::error('UserController@get - error: ' . $e->getMessage());
            // Manejar errores
            return redirect('/')->with('error', 'Error al iniciar sesión con Microsoft: ' . $e->getMessage());
        }
    }
} 