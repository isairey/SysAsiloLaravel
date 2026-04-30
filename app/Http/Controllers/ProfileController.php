<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Crea una nueva instancia del controlador.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el perfil del usuario autenticado.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show()
    {
        // Obtiene el usuario actualmente autenticado
        $user = Auth::user();
        // Retorna la vista del perfil, pasando los datos del usuario
        return view('profile.show', compact('user'));
    }
}
