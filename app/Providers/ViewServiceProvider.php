<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\AdultoMayor;
use App\Models\Enfermeria;
use App\Models\Fisioterapia;
use App\Models\Kinesiologia;
use App\Models\HistoriaClinica;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('pages.responsable.dashboard', function ($view) {
            $user = Auth::user();

            if (!$user) {
                // Si no hay usuario, se envían variables vacías para evitar errores en la vista.
                $view->with(['shortcuts' => [], 'chartData' => ['labels' => [], 'data' => []]]);
                return;
            }

            // --- INICIO DE LÓGICA DEL DASHBOARD ---

            // 1. Datos para las tarjetas de estadísticas y array de atajos.
            $data = [
                'totalPacientes'         => AdultoMayor::count(),
                'totalHistoriasClinicas' => HistoriaClinica::count(),
                'shortcuts'              => [],
            ];
            
            // 2. Array para los datos del gráfico.
            $chartData = [
                'labels' => [],
                'data' => [],
            ];

            // 3. Llenar los datos y atajos según la especialidad.
            if ($user->area_especialidad == 'Enfermeria') {
                $data['atencionesEnfermeria'] = Enfermeria::count();
                $data['shortcuts'] = [ /* ... Atajos de enfermeria ... */ ];

                // Datos para el gráfico de Enfermería
                $chartData['labels'] = ['Historias Clínicas', 'Atenciones Enfermería'];
                $chartData['data'] = [$data['totalHistoriasClinicas'], $data['atencionesEnfermeria']];
            }
            elseif ($user->area_especialidad == 'Fisioterapia-Kinesiologia') {
                $fichasFisio = Fisioterapia::count();
                $fichasKine = Kinesiologia::count();
                $data['fichasFisioKine'] = $fichasFisio + $fichasKine;
                $data['shortcuts'] = [ /* ... Atajos de fisioterapia ... */ ];
                
                // Datos para el gráfico de Fisioterapia/Kinesiología
                $chartData['labels'] = ['Fichas Fisioterapia', 'Fichas Kinesiología'];
                $chartData['data'] = [$fichasFisio, $fichasKine];
            }

            // 4. Pasar todas las variables a la vista.
            $view->with($data)->with('chartData', $chartData);
        });
    }
}