<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;

class Cliente extends Model
{
    use HasFactory, SoftDeletes, Multitenantable;

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'tipo_documento',
        'email',
        'telefono',
        'telefono_alternativo',
        'direccion',
        'ciudad',
        'estado_region',
        'activo',
        'ocupacion',
        'empresa_trabajo',
        'ingreso_mensual_estimado',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ingreso_mensual_estimado' => 'decimal:2'
    ];

    // Relaciones
    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }
    
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Elimina los contratos asociados cuando se elimina un cliente
     */
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($cliente) {
            // Eliminar todos los contratos del cliente (soft delete)
            $cliente->contratos()->each(function ($contrato) {
                $contrato->delete();
            });
        });
    }

    public static function createWithUser(array $data): self
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $cliente = static::create($data);
            $username = User::generateUniqueUsername($cliente->nombre, $cliente->apellido);
            $email = $cliente->email ?: 'cliente' . $cliente->documento . '@clientes.local';
            $user = User::create([
                'name' => $cliente->nombre . ' ' . $cliente->apellido,
                'username' => $username,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make($cliente->documento),
                'empresa_id' => $cliente->empresa_id,
                'sucursal_id' => $cliente->sucursal_id,
                'cliente_id' => $cliente->id,
                'status' => true,
            ]);
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $user->assignRole('Cliente');
            }
            if (!empty($cliente->telefono)) {
                try {
                    $empresa = $cliente->empresa;
                    if ($empresa && ($empresa->whatsapp_active ?? true)) {
                        $wa = $empresa->getWhatsAppService();
                        $msg = "¡Bienvenido a Inversiones Danger 3000 C.A!\n"
                            . "Tu acceso fue creado.\n"
                            . "Usuario: {$username}\n"
                            . "Contraseña inicial: {$cliente->documento}\n"
                            . "Por seguridad, cámbiala al ingresar.";
                        $wa->send($cliente->telefono, $msg);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('No se pudo enviar credenciales por WhatsApp', [
                        'cliente_id' => $cliente->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            return $cliente;
        });
    }
}