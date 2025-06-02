<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Exception;

use Illuminate\Support\Facades\Log;

class PublicController extends Controller
{
    public function index()
    {
        return view('public.login');
    }

    public function login(Request $request)
    {
        Log::info('PublicController@login');

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {

                $user = Auth::user();

                if ($user->activo == 0) {
                    Auth::logout();

                    Log::warning('PublicController@login: usuario desactivado', ['email' => $request->email]);

                    $result['status'] = false;
                    $result['data'] = [];
                    $result['message'] = 'El usuario está desactivado.<br>Contacte al administrador del sistema.';

                    return $result;
                }

                $request->session()->regenerate();

                Log::info('PublicController@login: usuario autenticado', ['email' => $request->email]);

                $result['status'] = true;
                $result['data'] = [];
                $result['message'] = 'Inicio de sesión exitoso.';
                $result['redirect'] = route('dashboard');

            } else {
                Log::warning('PublicController@login: intento fallido', ['email' => $request->email]);

                $result['status'] = false;
                $result['data'] = [];
                $result['message'] = 'Usuario o contraseña incorrectos.';
            }
        } catch (Exception $e) {
            Log::error('PublicController@login: error al autenticar - ' . $e->getMessage());

            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'Hubo un error. Por favor, intente nuevamente.';
        }

        return $result;
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');

    }
}
