<?php

namespace App\Http\Controllers\Cliente\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Contrato;
use App\Models\PlanPago;
use App\Models\ActiveSession;
use Carbon\Carbon;

class MobileController extends Controller
{
    public function contracts(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->cliente_id) {
            return response()->json(['data' => []]);
        }

        $contracts = Contrato::with(['unidad.moto'])
            ->where('cliente_id', $user->cliente_id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'numero' => $c->numero_contrato,
                    'estado' => $c->estado,
                    'fecha_inicio' => optional($c->fecha_inicio)->toDateString(),
                    'fecha_fin_estimada' => optional($c->fecha_fin_estimada)->toDateString(),
                    'saldo_pendiente' => (float) $c->saldo_pendiente,
                    'plazo_semanas' => $c->plazo_semanas,
                    'plazo_meses' => $c->plazo_meses,
                    'frecuencia_pago' => $c->frecuencia_pago,
                ];
            });

        return response()->json(['data' => $contracts]);
    }

    public function contractShow(Request $request, int $id)
    {
        $user = $request->user();
        if (!$user || !$user->cliente_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $contrato = Contrato::with(['planPagos' => function ($q) {
            $q->orderBy('numero_cuota');
        }])->where('cliente_id', $user->cliente_id)->findOrFail($id);

        $data = [
            'id' => $contrato->id,
            'numero' => $contrato->numero_contrato,
            'estado' => $contrato->estado,
            'fecha_inicio' => optional($contrato->fecha_inicio)->toDateString(),
            'fecha_fin_estimada' => optional($contrato->fecha_fin_estimada)->toDateString(),
            'saldo_pendiente' => (float) $contrato->saldo_pendiente,
            'plazo_semanas' => $contrato->plazo_semanas,
            'plazo_meses' => $contrato->plazo_meses,
            'frecuencia_pago' => $contrato->frecuencia_pago,
            'plan_pagos' => $contrato->planPagos->map(function ($p) {
                return [
                    'id' => $p->id,
                    'n' => $p->numero_cuota,
                    'fecha_vencimiento' => optional($p->fecha_vencimiento)->toDateString(),
                    'monto_total' => (float) $p->monto_total,
                    'monto_pagado' => (float) $p->monto_pagado,
                    'estado' => $p->estado,
                ];
            }),
        ];

        return response()->json(['data' => $data]);
    }

    public function timeline(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->cliente_id) {
            return response()->json(['data' => []]);
        }

        $from = $request->query('from') ? Carbon::parse($request->query('from')) : Carbon::today();
        $to = $request->query('to') ? Carbon::parse($request->query('to')) : Carbon::today()->addDays(14);

        $cuotas = PlanPago::whereHas('contrato', function ($q) use ($user) {
                $q->where('cliente_id', $user->cliente_id);
            })
            ->whereBetween('fecha_vencimiento', [$from->toDateString(), $to->toDateString()])
            ->orderBy('fecha_vencimiento')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'contrato_id' => $p->contrato_id,
                    'numero_cuota' => $p->numero_cuota,
                    'fecha_vencimiento' => optional($p->fecha_vencimiento)->toDateString(),
                    'monto_total' => (float) $p->monto_total,
                    'monto_pagado' => (float) $p->monto_pagado,
                    'estado' => $p->estado,
                ];
            });

        return response()->json(['data' => $cuotas]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $cliente = $user->cliente;

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'created_at' => $user->created_at?->format('d/m/Y'),
                ],
                'cliente' => $cliente ? [
                    'nombre' => $cliente->nombre,
                    'apellido' => $cliente->apellido,
                    'documento' => $cliente->documento,
                    'tipo_documento' => $cliente->tipo_documento,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'direccion' => $cliente->direccion,
                ] : null,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($user->cliente) {
            $user->cliente->update([
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
            ]);
        }

        return response()->json(['message' => 'Perfil actualizado correctamente']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Contraseña actualizada correctamente']);
    }

    public function sessions(Request $request)
    {
        $sessions = ActiveSession::where('user_id', $request->user()->id)
            ->orderByDesc('login_at')
            ->limit(10)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'login_at' => optional($session->login_at)->format('d/m/Y H:i'),
                    'location' => $session->location,
                    'is_active' => $session->is_active,
                    'is_current' => $session->is_current,
                ];
            });

        return response()->json(['data' => $sessions]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true]);
    }
}
