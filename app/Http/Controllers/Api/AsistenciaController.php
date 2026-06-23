<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Actividad;
use App\Models\Pastor;
use App\Models\Asistencia;

class AsistenciaController extends Controller
{
    public function activas()
    {
        $actividades = Actividad::where('estado', 'Activo')
            ->orderBy('fecha_inicio', 'desc')
            ->get(['id', 'nombre', 'fecha_inicio', 'lugar']);
            
        return response()->json([
            'success' => true,
            'actividades' => $actividades
        ]);
    }

    private function getEstadisticas($actividad_id)
    {
        $total = Asistencia::where('actividad_id', $actividad_id)->count();
        $recientes = Asistencia::with('pastor:id,nombres,apellidos,documento,foto')
            ->where('actividad_id', $actividad_id)
            ->orderBy('fecha_hora', 'desc')
            ->take(5)
            ->get()
            ->map(function ($asis) {
                return [
                    'nombre' => $asis->pastor->nombres . ' ' . $asis->pastor->apellidos,
                    'documento' => $asis->pastor->documento,
                    'foto' => $asis->pastor->foto ? asset('pastores/' . str_replace(' ', '', $asis->pastor->foto)) : null,
                    'fecha_hora' => $asis->fecha_hora->format('h:i A')
                ];
            });

        return [
            'total' => $total,
            'recientes' => $recientes
        ];
    }

    public function estadisticas(Request $request)
    {
        $request->validate(['actividad_id' => 'required|exists:actividades,id']);
        
        return response()->json([
            'success' => true,
            'data' => $this->getEstadisticas($request->actividad_id)
        ]);
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'actividad_id' => 'required|exists:actividades,id',
            'metodo' => 'required|string',
            'qr_data' => 'nullable|string',
            'cedula' => 'nullable|string',
        ]);

        $actividad = Actividad::findOrFail($request->actividad_id);
        $pastor = null;

        if ($request->metodo === 'QR' && $request->qr_data) {
            // El QR contiene la URL del perfil del pastor: e.g. "http://dominio.com/admin/pastores/15"
            // Extraemos el ID numérico del final de la URL
            $parts = explode('/', rtrim($request->qr_data, '/'));
            $pastorId = end($parts);
            
            if (is_numeric($pastorId)) {
                $pastor = Pastor::find($pastorId);
            }
        } elseif ($request->metodo === 'Manual' && $request->cedula) {
            $pastor = Pastor::where('documento', $request->cedula)->first();
        }

        if (!$pastor) {
            return response()->json([
                'success' => false,
                'message' => 'Pastor no encontrado con los datos proporcionados.'
            ], 404);
        }

        // Verificar si ya tiene asistencia
        $asistencia = Asistencia::where('actividad_id', $request->actividad_id)
            ->where('pastor_id', $pastor->id)
            ->first();

        if ($asistencia) {
            return response()->json([
                'success' => false,
                'message' => 'El pastor ya está registrado en esta actividad.',
                'pastor' => [
                    'nombre' => $pastor->nombres . ' ' . $pastor->apellidos,
                    'documento' => $pastor->documento,
                    'foto' => $pastor->foto ? asset('pastores/' . str_replace(' ', '', $pastor->foto)) : null,
                ]
            ], 400);
        }

        // Registrar asistencia
        Asistencia::create([
            'actividad_id' => $request->actividad_id,
            'pastor_id' => $pastor->id,
            'metodo' => $request->metodo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asistencia registrada correctamente.',
            'pastor' => [
                'nombre' => $pastor->nombres . ' ' . $pastor->apellidos,
                'documento' => $pastor->documento,
                'foto' => $pastor->foto ? asset('pastores/' . str_replace(' ', '', $pastor->foto)) : null,
            ],
            'estadisticas' => $this->getEstadisticas($request->actividad_id)
        ]);
    }
}
