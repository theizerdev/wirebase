<?php

namespace App\Services\Search;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductSearchService
{
    /**
     * Realizar una búsqueda avanzada de productos
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(array $filters = [])
    {
        $query = Producto::query()->with(['categoria', 'marca', 'images']);

        // Búsqueda por texto
        if (!empty($filters['query'])) {
            $query = $this->applyTextSearch($query, $filters['query']);
        }

        // Filtrar por categoría
        if (!empty($filters['category'])) {
            $query->whereHas('categoria', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        // Filtrar por marca
        if (!empty($filters['brand'])) {
            $query->whereHas('marca', function ($q) use ($filters) {
                $q->where('slug', $filters['brand']);
            });
        }

        // Filtrar por rango de precios
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Filtrar por disponibilidad
        if (!empty($filters['availability'])) {
            $query = $this->applyAvailabilityFilter($query, $filters['availability']);
        }

        // Solo productos activos
        $query->where('status', true);

        // Ordenar resultados
        $query = $this->applySorting($query, $filters['sort'] ?? 'relevance');

        // Paginar resultados
        return $query->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Aplicar búsqueda de texto
     *
     * @param Builder $query
     * @param string $searchTerm
     * @return Builder
     */
    protected function applyTextSearch(Builder $query, string $searchTerm): Builder
    {
        $terms = explode(' ', trim($searchTerm));
        
        return $query->where(function ($q) use ($terms) {
            foreach ($terms as $term) {
                $q->where(function ($subQuery) use ($term) {
                    $subQuery->where('name', 'like', '%' . $term . '%')
                             ->orWhere('description', 'like', '%' . $term . '%')
                             ->orWhereHas('categoria', function ($catQuery) use ($term) {
                                 $catQuery->where('nombre', 'like', '%' . $term . '%')
                                          ->orWhere('slug', 'like', '%' . $term . '%');
                             })
                             ->orWhereHas('marca', function ($brandQuery) use ($term) {
                                 $brandQuery->where('nombre', 'like', '%' . $term . '%')
                                            ->orWhere('slug', 'like', '%' . $term . '%');
                             });
                });
            }
        });
    }

    /**
     * Aplicar filtro de disponibilidad
     *
     * @param Builder $query
     * @param string $availability
     * @return Builder
     */
    protected function applyAvailabilityFilter(Builder $query, string $availability): Builder
    {
        switch ($availability) {
            case 'in_stock':
                return $query->where('quantity', '>', 0);
                
            case 'low_stock':
                return $query->where('quantity', '>', 0)->where('quantity', '<=', 5);
                
            case 'out_of_stock':
                return $query->where('quantity', 0);
                
            default:
                return $query;
        }
    }

    /**
     * Aplicar ordenamiento
     *
     * @param Builder $query
     * @param string $sort
     * @return Builder
     */
    protected function applySorting(Builder $query, string $sort): Builder
    {
        switch ($sort) {
            case 'price_asc':
                return $query->orderBy('price', 'asc');
                
            case 'price_desc':
                return $query->orderBy('price', 'desc');
                
            case 'name_asc':
                return $query->orderBy('name', 'asc');
                
            case 'name_desc':
                return $query->orderBy('name', 'desc');
                
            case 'newest':
                return $query->orderBy('created_at', 'desc');
                
            case 'popularity':
                // En una implementación real, esto se basaría en ventas o vistas
                return $query->orderBy('quantity', 'desc');
                
            case 'relevance':
            default:
                // Para búsqueda por texto, ordenar por relevancia (simulada)
                return $query->orderBy('quantity', 'desc')->orderBy('price', 'asc');
        }
    }

    /**
     * Obtener sugerencias de búsqueda
     *
     * @param string $term
     * @param int $limit
     * @return array
     */
    public function getSuggestions(string $term, int $limit = 8): array
    {
        if (strlen($term) < 2) {
            return [
                'products' => collect(),
                'categories' => collect(),
                'brands' => collect()
            ];
        }

        // Buscar productos que coincidan
        $products = Producto::where('name', 'like', '%' . $term . '%')
            ->orWhere('description', 'like', '%' . $term . '%')
            ->where('status', true)
            ->limit($limit)
            ->get(['id', 'name', 'price', 'image_path']);

        // Buscar categorías que coincidan
        $categories = \App\Models\Categoria::where('nombre', 'like', '%' . $term . '%')
            ->orWhere('slug', 'like', '%' . $term . '%')
            ->where('activo', true)
            ->limit($limit/2)
            ->get(['id', 'nombre', 'slug']);

        // Buscar marcas que coincidan
        $brands = \App\Models\Marca::where('nombre', 'like', '%' . $term . '%')
            ->orWhere('slug', 'like', '%' . $term . '%')
            ->where('activo', true)
            ->limit($limit/2)
            ->get(['id', 'nombre', 'slug']);

        return [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands
        ];
    }

    /**
     * Obtener facetas para refinamiento de búsqueda
     *
     * @param array $filters
     * @return array
     */
    public function getFacets(array $filters = []): array
    {
        $query = Producto::query()->where('status', true);

        // Aplicar filtros existentes excepto los de las facetas que vamos a calcular
        if (!empty($filters['query'])) {
            $query = $this->applyTextSearch($query, $filters['query']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Obtener categorías disponibles
        $categories = $query->clone()
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select('categorias.nombre', 'categorias.slug')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('categorias.id', 'categorias.nombre', 'categorias.slug')
            ->orderBy('count', 'desc')
            ->get();

        // Obtener marcas disponibles
        $brands = $query->clone()
            ->join('marcas', 'productos.marca_id', '=', 'marcas.id')
            ->select('marcas.nombre', 'marcas.slug')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('marcas.id', 'marcas.nombre', 'marcas.slug')
            ->orderBy('count', 'desc')
            ->get();

        // Obtener rangos de precios
        $priceStats = $query->clone()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'categories' => $categories,
            'brands' => $brands,
            'price_range' => [
                'min' => $priceStats->min_price ?? 0,
                'max' => $priceStats->max_price ?? 0
            ]
        ];
    }
}