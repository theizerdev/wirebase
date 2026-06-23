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

        // Enviar notificación por WhatsApp si tiene teléfono registrado
        if (!empty($pastor->telefono_tlf) && $pastor->telefono_tlf !== '0') {
            try {
                $whatsappService = app(\App\Services\WhatsAppService::class, ['empresa' => $pastor->empresa_id]);
                if ($whatsappService->isConfigured()) {
                    // Formatear el número de teléfono (de 0... a 58...)
                    $cleanNumber = preg_replace('/[^0-9]/', '', $pastor->telefono_tlf);
                    if (substr($cleanNumber, 0, 1) === '0') {
                        $cleanNumber = '58' . substr($cleanNumber, 1);
                    }

                    $mensaje = "✅ *REGISTRO DE ASISTENCIA*\n\n" .
                               "Estimado(a) *{$pastor->nombres} {$pastor->apellidos}*,\n" .
                               "Confirmamos su asistencia al evento:\n\n" .
                               "📌 *{$actividad->nombre}*\n" .
                               "📅 *Fecha:* " . now()->format('d/m/Y') . "\n" .
                               "🕒 *Hora:* " . now()->format('h:i A') . "\n" .
                               "📍 *Lugar:* {$actividad->lugar}\n\n" .
                               "¡Gracias por su valiosa participación! 🙏";

                    $whatsappService->sendMessage($cleanNumber, $mensaje);
                }
            } catch (\Exception $e) {
                // Registrar el error en logs pero no interrumpir la respuesta de la API
                \Illuminate\Support\Facades\Log::error('Error enviando WhatsApp de asistencia:', [
                    'pastor_id' => $pastor->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

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

    public function registrarNuevoPastor(Request $request)
    {
        $request->validate([
            'actividad_id' => 'required|exists:actividades,id',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'documento' => 'required|string|max:50|unique:pastores,documento',
            'nivel_ministerial' => 'nullable|string|max:255',
            'zona' => 'nullable|string|max:255',
            'distrito' => 'nullable|string|max:255',
            'genero' => 'nullable|string|max:50',
            'edad' => 'nullable|integer|min:0',
            'fe_nacimiento' => 'nullable|date',
            'telefono_tlf' => 'nullable|string|max:255',
            'foto_base64' => 'nullable|string',
        ]);

        $actividad = Actividad::findOrFail($request->actividad_id);

        // 1. Decodificar y guardar foto tipo carnet
        $fotoFilename = null;
        if ($request->foto_base64) {
            try {
                $base64Image = $request->foto_base64;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]);
                    $imageData = base64_decode($imageData);
                    
                    if ($imageData !== false) {
                        $fotoFilename = 'pastor_' . time() . '_' . uniqid() . '.' . $type;
                        $targetPath = public_path('pastores/' . $fotoFilename);
                        
                        // Asegurar que existe la carpeta pastores
                        if (!file_exists(public_path('pastores'))) {
                            mkdir(public_path('pastores'), 0755, true);
                        }
                        
                        file_put_contents($targetPath, $imageData);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error guardando foto en registrarNuevoPastor: ' . $e->getMessage());
            }
        }

        // 2. Crear Pastor
        $empresaId = null;
        $empresa = \App\Models\Empresa::where('status', true)->first();
        if ($empresa) {
            $empresaId = $empresa->id;
        }

        $pastor = Pastor::create([
            'codigo' => 'TEMP',
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'documento' => $request->documento,
            'nivel_ministerial' => $request->nivel_ministerial,
            'zona' => $request->zona,
            'distrito' => $request->distrito,
            'genero' => $request->genero,
            'edad' => $request->edad,
            'fe_nacimiento' => $request->fe_nacimiento,
            'telefono_tlf' => $request->telefono_tlf,
            'foto' => $fotoFilename,
            'status' => true,
            'empresa_id' => $empresaId,
        ]);

        // Generar y actualizar código único del pastor
        $codigo = Pastor::generarCodigoPastor($pastor->id, $pastor->documento);
        $pastor->update(['codigo' => $codigo]);

        // 3. Registrar Asistencia
        Asistencia::create([
            'actividad_id' => $request->actividad_id,
            'pastor_id' => $pastor->id,
            'metodo' => 'Manual'
        ]);

        // 4. Enviar notificación por WhatsApp dual (bienvenida + asistencia)
        if (!empty($pastor->telefono_tlf) && $pastor->telefono_tlf !== '0') {
            try {
                $whatsappService = app(\App\Services\WhatsAppService::class, ['empresa' => $pastor->empresa_id]);
                if ($whatsappService->isConfigured()) {
                    $cleanNumber = preg_replace('/[^0-9]/', '', $pastor->telefono_tlf);
                    if (substr($cleanNumber, 0, 1) === '0') {
                        $cleanNumber = '58' . substr($cleanNumber, 1);
                    }

                    $mensaje = "🎉 *¡BIENVENIDO Y ASISTENCIA REGISTRADA!*\n\n" .
                               "Estimado(a) pastor *{$pastor->nombres} {$pastor->apellidos}*,\n" .
                               "Nos complace darle la bienvenida y confirmarle que ha sido registrado exitosamente en nuestro sistema de base de datos nacional.\n\n" .
                               "Asimismo, confirmamos el registro de su asistencia al evento:\n" .
                               "📌 *{$actividad->nombre}*\n" .
                               "📅 *Fecha:* " . now()->format('d/m/Y') . "\n" .
                               "🕒 *Hora:* " . now()->format('h:i A') . "\n" .
                               "📍 *Lugar:* {$actividad->lugar}\n\n" .
                               "¡Agradecemos su participación y bendecimos su valioso ministerio en el Señor! 🙏✨";

                    $whatsappService->sendMessage($cleanNumber, $mensaje);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error enviando WhatsApp en registrarNuevoPastor: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pastor registrado y asistencia tomada correctamente.',
            'pastor' => [
                'nombre' => $pastor->nombres . ' ' . $pastor->apellidos,
                'documento' => $pastor->documento,
                'foto' => $pastor->foto ? asset('pastores/' . str_replace(' ', '', $pastor->foto)) : null,
            ],
            'estadisticas' => $this->getEstadisticas($request->actividad_id)
        ]);
    }
}
