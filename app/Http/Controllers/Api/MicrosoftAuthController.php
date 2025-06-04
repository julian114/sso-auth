<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftAuthController extends Controller
{
    /**
     * Inicia el proceso de autenticación con Microsoft
     * Retorna la URL de autenticación
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUrl(Request $request)
{

    
    try {
        $redirectUrl = $request->input('callback_url');
        
        if (!$redirectUrl) {
            return response()->json([
                'status' => false,
                'message' => 'La URL de callback es requerida',
            ], 400);
        }
        
        // Generar estado cifrado (contiene callback_url)
        $state = encrypt([
            'callback_url' => $redirectUrl,
            'csrf' => Str::random(40), // Opcional para seguridad
        ]);
        
        
        $authUrl = Socialite::driver('microsoft')
            ->with(['state' => $state])
            ->redirect()
            ->getTargetUrl();
            
        return response()->json([
            'status' => true,
            'auth_url' => $authUrl
        ]);
    } catch (Exception $e) {
        Log::error('Error en getAuthUrl: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Error al generar URL de autenticación'
        ], 500);
    }
}

    /**
     * Procesa el callback de Microsoft y retorna los datos del usuario
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function handleCallback(Request $request)
{


    try {
        // 1. Verificar estado cifrado
        if (!$request->state) {
            throw new Exception('Parámetro state faltante');
        }
        
        // 2. Descifrar estado
        $state = decrypt($request->state);
        $callbackUrl = $state['callback_url'];
        
        // 3. Obtener usuario (modo stateless)
        $microsoftUser = Socialite::driver('microsoft')->stateless()->user();
        
        // 4. Buscar usuario en BD para el futuro
        //$user = User::where('email', $microsoftUser->email)->first();
        
        // 5. Preparar respuesta
        $responseData = [
            'status' => true,
            'user' => [
                'id' => $microsoftUser->id,
                'name' => $microsoftUser->name,
                'email' => $microsoftUser->email,
                'token' => $microsoftUser->token,
                'refresh_token' => $microsoftUser->refreshToken,
                'token_expires_at' => now()->addSeconds($microsoftUser->expiresIn),
                //'user_exists' => (bool)$user,
            ]
        ];
        
        // 6. Redirigir a callback_url con datos (vía query params)
        $encodedData = base64_encode(json_encode($responseData));
        $separator = str_contains($callbackUrl, '?') ? '&' : '?';
        
        return redirect($callbackUrl . $separator . 'auth_data=' . urlencode($encodedData));
        
    } catch (Exception $e) {
        Log::error('Error en handleCallback: ' . $e->getMessage());
        
        // Manejar error para callback_url
        if (isset($callbackUrl)) {
            $errorData = base64_encode(json_encode([
                'status' => false,
                'message' => 'Error de autenticación: ' . $e->getMessage()
            ]));
            $separator = str_contains($callbackUrl, '?') ? '&' : '?';
            return redirect($callbackUrl . $separator . 'auth_error=' . urlencode($errorData));
        }
        
        return response()->json([
            'status' => false,
            'message' => 'Error en autenticación'
        ], 401);
    }
}
    
    /**
     * Endpoint para verificar el estado de autenticación
     * Utilizado para verificar la validez de un token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /*public function verifyToken(Request $request)
    {
        try {
            $token = $request->input('token');
            
            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'El token es requerido'
                ], 400);
            }
            
            // Aquí se podría implementar la verificación del token con Microsoft Graph API
            // Por ahora solo verificamos que exista un usuario con ese token
            $user = User::where('microsoft_token', $token)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token inválido o expirado'
                ], 401);
            }
            
            // Verificar si el token ha expirado
            if ($user->microsoft_token_expires_at && now()->gt($user->microsoft_token_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token expirado',
                    'expired' => true
                ], 401);
            }
            
            return response()->json([
                'status' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('MicrosoftAuthController@verifyToken - error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al verificar token: ' . $e->getMessage()
            ], 500);
        }
    }*/
}