<?php

namespace App\Http\Controllers;

use App\Models\AdultoMayor;
use App\Models\Orientacion; // Asegúrate de que el modelo Orientacion esté importado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrarFichaController extends Controller
{
    /**
     * Muestra la lista de adultos mayores.
     */
    public function index()
    {
        // CAMBIO: Eager load la relación 'latestOrientacion' para cada adulto
        $adultos = AdultoMayor::with('persona', 'latestOrientacion')->paginate(10);
        return view('Orientacion.indexOri', compact('adultos'));
    }

    /**
     * Muestra el formulario para registrar una nueva FICHA DE ORIENTACIÓN.
     * Es para un Adulto Mayor existente.
     */
    public function registerOrientacion($id_adulto)
    {
        $adulto = AdultoMayor::with('persona')->findOrFail($id_adulto);
        $modoEdicion = false; // Estamos en modo registro
        $orientacion = null; // No hay ficha de orientación precargada
        return view('Orientacion.registrarFicha', compact('adulto', 'modoEdicion', 'orientacion'));
    }

    /**
     * Almacena una nueva orientación en la base de datos.
     */
    public function storeOrientacion(Request $request)
    {
        $request->validate([
            'id_adulto' => 'required|exists:adulto_mayor,id_adulto',
            'fecha_ingreso' => 'required|date',
            'tipo_orientacion' => 'required|string|in:psicologica,social,legal',
            'motivo_orientacion' => 'required|string',
            'resultado_obtenido' => 'nullable|string',
        ]);

        $orientacion = new Orientacion();
        $orientacion->id_adulto = $request->id_adulto;
        $orientacion->fecha_ingreso = $request->fecha_ingreso;
        $orientacion->tipo_orientacion = $request->tipo_orientacion;
        $orientacion->motivo_orientacion = $request->motivo_orientacion;
        $orientacion->resultado_obtenido = $request->resultado_obtenido;
        $orientacion->id_usuario = Auth::id();
        $orientacion->save();

        return redirect()->route('legal.orientacion.index')->with('success', 'Orientación registrada exitosamente.');
    }

    /**
     * Muestra el formulario para EDITAR una ficha de orientación existente.
     * Buscará la última ficha de orientación asociada al adulto mayor.
     * Se redirige a la misma vista 'registrarFicha' pero en modo edición.
     *
     * @param  int  $id_adulto  El ID del adulto mayor.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id_adulto)
    {
        $adulto = AdultoMayor::with('persona')->findOrFail($id_adulto);

        // Intentar encontrar la última ficha de orientación para este adulto mayor
        // Si hay varias fichas y quieres editar una específica, la lógica aquí debería cambiar
        // para seleccionar la ficha por su 'cod_or'. Por ahora, asumimos la más reciente.
        $orientacion = Orientacion::where('id_adulto', $id_adulto)
                                 ->orderByDesc('fecha_ingreso') // O 'created_at' si es más relevante
                                 ->first();

        $modoEdicion = true; // Estamos en modo edición

        // Si no se encuentra ninguna ficha de orientación, se puede redirigir a crear una nueva
        // o mostrar un mensaje. Por la naturaleza del botón "Editar Ficha", asumimos que existirá.
        if (!$orientacion) {
            return redirect()->route('legal.orientacion.register', ['id_adulto' => $id_adulto])
                             ->with('info', 'No se encontró una ficha de orientación existente para editar. Por favor, registre una nueva.');
        }

        return view('Orientacion.registrarFicha', compact('adulto', 'modoEdicion', 'orientacion'));
    }

    /**
     * Actualiza una ficha de orientación existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cod_or El ID de la ficha de orientación a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrientacion(Request $request, $cod_or)
    {
        $request->validate([
            'id_adulto' => 'required|exists:adulto_mayor,id_adulto', // Aunque no se usa directamente para buscar la ficha, es buena práctica validarlo
            'fecha_ingreso' => 'required|date',
            'tipo_orientacion' => 'required|string|in:psicologica,social,legal',
            'motivo_orientacion' => 'required|string',
            'resultado_obtenido' => 'nullable|string',
        ]);

        $orientacion = Orientacion::findOrFail($cod_or); // Encuentra la ficha por su ID único
        $orientacion->fecha_ingreso = $request->fecha_ingreso;
        $orientacion->tipo_orientacion = $request->tipo_orientacion;
        $orientacion->motivo_orientacion = $request->motivo_orientacion;
        $orientacion->resultado_obtenido = $request->resultado_obtenido;
        // El id_usuario no debería cambiar al editar, pero puedes actualizarlo si es el caso
        // $orientacion->id_usuario = Auth::id();
        $orientacion->save();

        return redirect()->route('legal.orientacion.index')->with('success', 'Ficha de orientación actualizada exitosamente.');
    }

    public function showOrientacionDetail($cod_or)
    {
        // Carga la ficha de orientación junto con las relaciones adulto y persona
        $orientacion = Orientacion::with('adulto.persona')->findOrFail($cod_or);
        // El adulto mayor ya viene con la orientación, no es necesario buscarlo aparte
        $adulto = $orientacion->adulto;

        return view('Orientacion.verDetalleFicha', compact('orientacion', 'adulto'));
    }
}
