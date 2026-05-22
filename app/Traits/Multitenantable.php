<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Multitenantable {

    public static function bootMultitenantable() {

        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            static::creating(function ($model) {
                if (!$model->empresa_id) {
                    $model->empresa_id = auth()->user()->empresa_id;
                }
                
                // Si el modelo tiene sucursal_id y no está asignado, usar la del usuario
                if (property_exists($model, 'sucursal_id') || in_array('sucursal_id', $model->getFillable())) {
                    if (!$model->sucursal_id && auth()->user()->sucursal_id) {
                        $model->sucursal_id = auth()->user()->sucursal_id;
                    }
                }
            });
            
            static::addGlobalScope('multitenancy', function (Builder $builder) {
                $table = $builder->getModel()->getTable();
                
                // Aplicar filtro por empresa si el modelo tiene empresa_id
                if (in_array('empresa_id', $builder->getModel()->getFillable()) || 
                    $builder->getModel()->getConnection()->getSchemaBuilder()->hasColumn($table, 'empresa_id')) {
                    if (auth()->user()->empresa_id) {
                        $builder->where($table . '.empresa_id', auth()->user()->empresa_id);
                    }
                }
                
                // Aplicar filtro por sucursal si el modelo tiene sucursal_id
                if (in_array('sucursal_id', $builder->getModel()->getFillable()) || 
                    $builder->getModel()->getConnection()->getSchemaBuilder()->hasColumn($table, 'sucursal_id')) {
                    if (auth()->user()->sucursal_id) {
                        $builder->where($table . '.sucursal_id', auth()->user()->sucursal_id);
                    }
                }
            });
        }
    }
    
    /**
     * Scope para filtrar por las zonas del usuario autenticado.
     * Este scope se puede usar en modelos que tengan relación con zonas.
     */
    public function scopePorZonasUsuario(Builder $query, ?int $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return $query;
        }
        
        $user = auth()->user() ?? \App\Models\User::find($userId);
        
        if (!$user) {
            return $query;
        }
        
        // Si es Super Administrador, no aplicar filtro
        if ($user->hasRole('Super Administrador')) {
            return $query;
        }
        
        // Obtener las zonas del usuario
        $zonasIds = $user->zonas()->pluck('zonas.id')->toArray();
        
        if (empty($zonasIds)) {
            // Si no tiene zonas asignadas, retornar query vacía o sin resultados
            return $query->whereRaw('1 = 0');
        }
        
        // Si el modelo tiene zona_id directo
        if (in_array('zona_id', $this->getFillable()) || 
            $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'zona_id')) {
            return $query->whereIn('zona_id', $zonasIds);
        }
        
        // Si el modelo tiene una relación con zonas, se debe definir en el modelo específico
        return $query;
    }
}
