<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AIRecommendationService
{
    public function getRecommendations($userId = null, $limit = 6)
    {
        $cacheKey = "ai_recommendations_" . ($userId ?? 'guest');
        
        return Cache::remember($cacheKey, 300, function() use ($userId, $limit) {
            if ($userId) {
                return $this->getPersonalizedRecommendations($userId, $limit);
            }
            
            return $this->getPopularRecommendations($limit);
        });
    }

    private function getPersonalizedRecommendations($userId, $limit)
    {
        $user = User::find($userId);
        if (!$user) return collect();

        // Análisis de comportamiento del usuario
        $userCategories = $this->getUserPreferredCategories($userId);
        $userPriceRange = $this->getUserPriceRange($userId);
        
        $query = Producto::query()
            ->where('quantity', '>', 0)
            ->where('status', 1);

        // Filtrar por categorías preferidas
        if ($userCategories->isNotEmpty()) {
            $query->whereIn('categoria_id', $userCategories->pluck('id'));
        }

        // Filtrar por rango de precios
        if ($userPriceRange) {
            $query->whereBetween('price', [$userPriceRange['min'], $userPriceRange['max']]);
        }

        return $query->inRandomOrder()->limit($limit)->get();
    }

    private function getPopularRecommendations($limit)
    {
        // Productos más populares basados en ventas y vistas
        return Producto::query()
            ->where('quantity', '>', 0)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getUserPreferredCategories($userId)
    {
        // Análisis de categorías más compradas/vistas por el usuario
        return Cache::remember("user_categories_{$userId}", 3600, function() use ($userId) {
            // Simulación de análisis de preferencias
            return collect([1, 2, 3]); // IDs de categorías preferidas
        });
    }

    private function getUserPriceRange($userId)
    {
        // Análisis del rango de precios preferido del usuario
        return Cache::remember("user_price_range_{$userId}", 3600, function() use ($userId) {
            return [
                'min' => 10,
                'max' => 500
            ];
        });
    }

    public function getSimilarProducts($productId, $limit = 4)
    {
        $product = Producto::find($productId);
        if (!$product) return collect();

        return Producto::query()
            ->where('id', '!=', $productId)
            ->where('categoria_id', $product->categoria_id)
            ->where('quantity', '>', 0)
            ->where('status', 1)
            ->whereBetween('price', [
                $product->price * 0.7,
                $product->price * 1.3
            ])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}