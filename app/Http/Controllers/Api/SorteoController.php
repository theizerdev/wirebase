<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Sorteo;
use App\Models\SorteoAuditoria;
use App\Models\SorteoContratoGanador;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SorteoController extends Controller
{
    public function ejecutar(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'nullable|string|max:255',
        ]);

        $empresaId = $request->empresa_id;

        // Get IDs of contracts already won
        $ganadoresIds = SorteoContratoGanador::where('empresa_id', $empresaId)
            ->pluck('contrato_id')
            ->toArray();

        // Get eligible contracts
        $elegibles = Contrato::where('empresa_id', $empresaId)
            ->whereIn('estado', ['activo', 'mora'])
            ->whereNotIn('id', $ganadoresIds)
            ->get();

        if ($elegibles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay contratos elegibles para el sorteo.',
            ], 422);
        }

        // Cryptographically secure random selection
        $indice = random_int(0, $elegibles->count() - 1);
        $contratoGanador = $elegibles[$indice];

        $timestamp = now();
        $randomBytes = bin2hex(random_bytes(16));
        $hashData = $contratoGanador->numero_contrato . '|' . $timestamp->toISOString() . '|' . $randomBytes;
        $hashValidacion = hash('sha256', $hashData);

        // Create sorteo record
        $sorteo = Sorteo::create([
            'nombre' => $request->nombre ?? 'Sorteo ' . $timestamp->format('d/m/Y H:i'),
            'fecha_sorteo' => $timestamp,
            'numero_contrato_ganador' => $contratoGanador->numero_contrato,
            'hash_validacion' => $hashValidacion,
            'total_contratos_elegibles' => $elegibles->count(),
            'ejecutado_por' => $request->user()?->id,
            'empresa_id' => $empresaId,
            'estado' => 'completado',
        ]);

        // Register winner (permanent exclusion)
        SorteoContratoGanador::create([
            'sorteo_id' => $sorteo->id,
            'contrato_id' => $contratoGanador->id,
            'numero_contrato' => $contratoGanador->numero_contrato,
            'fecha_ganador' => $timestamp,
            'hash_verificacion' => $hashValidacion,
            'empresa_id' => $empresaId,
        ]);

        // Audit trail
        SorteoAuditoria::create([
            'sorteo_id' => $sorteo->id,
            'accion' => 'sorteo_ejecutado',
            'detalle' => [
                'total_elegibles' => $elegibles->count(),
                'indice_seleccionado' => $indice,
                'contrato_ganador' => $contratoGanador->numero_contrato,
                'random_bytes' => $randomBytes,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'ejecutado_por' => $request->user()?->id,
            'empresa_id' => $empresaId,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'sorteo_id' => $sorteo->id,
                'numero_contrato_ganador' => $contratoGanador->numero_contrato,
                'fecha_sorteo' => $timestamp->toISOString(),
                'hash_validacion' => $hashValidacion,
                'total_elegibles' => $elegibles->count(),
                'cliente' => [
                    'nombre' => $contratoGanador->cliente?->nombre ?? '',
                    'apellido' => $contratoGanador->cliente?->apellido ?? '',
                ],
            ],
        ]);
    }

    public function historial(Request $request)
    {
        $request->validate(['empresa_id' => 'required|exists:empresas,id']);

        $sorteos = Sorteo::where('empresa_id', $request->empresa_id)
            ->with('ganador', 'ejecutadoPor')
            ->orderByDesc('fecha_sorteo')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $sorteos]);
    }

    public function verificar(Request $request, $id)
    {
        $sorteo = Sorteo::with('ganador', 'auditorias', 'ejecutadoPor')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $sorteo,
        ]);
    }

    public function contratosElegibles(Request $request)
    {
        $request->validate(['empresa_id' => 'required|exists:empresas,id']);

        $empresaId = $request->empresa_id;

        $ganadoresIds = SorteoContratoGanador::where('empresa_id', $empresaId)
            ->pluck('contrato_id')
            ->toArray();

        $elegibles = Contrato::where('empresa_id', $empresaId)
            ->whereIn('estado', ['activo', 'mora'])
            ->whereNotIn('id', $ganadoresIds)
            ->select('id', 'numero_contrato', 'estado')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $elegibles->count(),
                'contratos' => $elegibles,
            ],
        ]);
    }
}
