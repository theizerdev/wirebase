<?php

namespace App\Services\Search;

use App\Services\Logging\LoggingService;
use App\Services\Cache\CacheService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Exception;

class SearchService
{
    protected LoggingService $loggingService;
    protected CacheService $cacheService;
    protected array $operators = [
        'eq' => '=',
        'ne' => '!=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'like' => 'like',
        'not_like' => 'not like',
        'in' => 'in',
        'not_in' => 'not in',
        'between' => 'between',
        'is_null' => 'is_null',
        'is_not_null' => 'is_not_null',
        'json_contains' => 'json_contains',
        'json_length' => 'json_length',
    ];

    protected array $searchTypes = [
        'simple',
        'advanced',
        'fuzzy',
        'semantic',
        'fulltext',
        'regex',
    ];

    public function __construct(LoggingService $loggingService, CacheService $cacheService)
    {
        $this->loggingService = $loggingService;
        $this->cacheService = $cacheService;
    }

    public function search(string $modelClass, array $parameters = []): array
    {
        try {
            $searchId = md5(json_encode([$modelClass, $parameters]));
            $cacheKey = "search:{$searchId}";

            return $this->cacheService->remember($cacheKey, 300, function () use ($modelClass, $parameters) {
                $query = $modelClass::query();

                $this->applyFilters($query, $parameters['filters'] ?? []);
                $this->applySearch($query, $parameters['search'] ?? []);
                $this->applySorting($query, $parameters['sort'] ?? []);
                $this->applyScopes($query, $parameters['scopes'] ?? []);
                $this->applyMultitenancy($query, $parameters['multitenancy'] ?? []);

                $total = $query->count();

                $this->applyPagination($query, $parameters['pagination'] ?? []);

                $results = $query->get();

                $this->applyPostProcessing($results, $parameters['post_processing'] ?? []);

                $this->loggingService->logBusinessEvent('search_performed', [
                    'model' => $modelClass,
                    'parameters' => $parameters,
                    'total_results' => $total,
                    'search_id' => $searchId,
                ]);

                return [
                    'success' => true,
                    'search_id' => $searchId,
                    'total' => $total,
                    'results' => $results->toArray(),
                    'metadata' => [
                        'model' => $modelClass,
                        'parameters' => $parameters,
                        'executed_at' => now()->toIso8601String(),
                    ],
                ];
            });
        } catch (Exception $e) {
            $this->loggingService->logBusinessError($e, [
                'search' => $modelClass,
                'parameters' => $parameters,
            ]);
            return [
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage(),
                'results' => [],
                'total' => 0,
            ];
        }
    }

    public function advancedSearch(string $modelClass, array $criteria = []): array
    {
        try {
            $searchId = md5(json_encode([$modelClass, $criteria]));
            $cacheKey = "advanced_search:{$searchId}";

            return $this->cacheService->remember($cacheKey, 300, function () use ($modelClass, $criteria) {
                $query = $modelClass::query();

                if (isset($criteria['conditions'])) {
                    $this->applyAdvancedConditions($query, $criteria['conditions']);
                }

                if (isset($criteria['aggregations'])) {
                    $this->applyAggregations($query, $criteria['aggregations']);
                }

                if (isset($criteria['group_by'])) {
                    $this->applyGroupBy($query, $criteria['group_by']);
                }

                if (isset($criteria['having'])) {
                    $this->applyHaving($query, $criteria['having']);
                }

                $this->applySorting($query, $criteria['sort'] ?? []);
                $this->applyMultitenancy($query, $criteria['multitenancy'] ?? []);

                $total = $query->count();

                $this->applyPagination($query, $criteria['pagination'] ?? []);

                $results = $query->get();

                $this->loggingService->logBusinessEvent('advanced_search_performed', [
                    'model' => $modelClass,
                    'criteria' => $criteria,
                    'total_results' => $total,
                    'search_id' => $searchId,
                ]);

                return [
                    'success' => true,
                    'search_id' => $searchId,
                    'total' => $total,
                    'results' => $results->toArray(),
                    'aggregations' => $this->getAggregations($query, $criteria['aggregations'] ?? []),
                    'metadata' => [
                        'model' => $modelClass,
                        'criteria' => $criteria,
                        'executed_at' => now()->toIso8601String(),
                    ],
                ];
            });
        } catch (Exception $e) {
            $this->loggingService->logBusinessError($e, [
                'advanced_search' => $modelClass,
                'criteria' => $criteria,
            ]);
            return [
                'success' => false,
                'message' => 'Advanced search failed: ' . $e->getMessage(),
                'results' => [],
                'total' => 0,
            ];
        }
    }

    public function fuzzySearch(string $modelClass, string $searchTerm, array $fields = [], array $options = []): array
    {
        try {
            $searchId = md5(json_encode([$modelClass, $searchTerm, $fields, $options]));
            $cacheKey = "fuzzy_search:{$searchId}";

            return $this->cacheService->remember($cacheKey, 300, function () use ($modelClass, $searchTerm, $fields, $options) {
                $query = $modelClass::query();

                if (empty($fields)) {
                    $fields = $this->getSearchableFields($modelClass);
                }

                $this->applyFuzzySearch($query, $searchTerm, $fields, $options);
                $this->applySorting($query, $options['sort'] ?? [['field' => 'relevance', 'direction' => 'desc']]);
                $this->applyMultitenancy($query, $options['multitenancy'] ?? []);

                $total = $query->count();

                $this->applyPagination($query, $options['pagination'] ?? []);

                $results = $query->get();

                $this->loggingService->logBusinessEvent('fuzzy_search_performed', [
                    'model' => $modelClass,
                    'search_term' => $searchTerm,
                    'fields' => $fields,
                    'total_results' => $total,
                    'search_id' => $searchId,
                ]);

                return [
                    'success' => true,
                    'search_id' => $searchId,
                    'total' => $total,
                    'results' => $results->toArray(),
                    'metadata' => [
                        'model' => $modelClass,
                        'search_term' => $searchTerm,
                        'fields' => $fields,
                        'executed_at' => now()->toIso8601String(),
                    ],
                ];
            });
        } catch (Exception $e) {
            $this->loggingService->logBusinessError($e, [
                'fuzzy_search' => $modelClass,
                'search_term' => $searchTerm,
                'fields' => $fields,
            ]);
            return [
                'success' => false,
                'message' => 'Fuzzy search failed: ' . $e->getMessage(),
                'results' => [],
                'total' => 0,
            ];
        }
    }

    public function fulltextSearch(string $modelClass, string $searchTerm, array $fields = [], array $options = []): array
    {
        try {
            $searchId = md5(json_encode([$modelClass, $searchTerm, $fields, $options]));
            $cacheKey = "fulltext_search:{$searchId}";

            return $this->cacheService->remember($cacheKey, 300, function () use ($modelClass, $searchTerm, $fields, $options) {
                $query = $modelClass::query();

                if (empty($fields)) {
                    $fields = $this->getFulltextFields($modelClass);
                }

                $this->applyFulltextSearch($query, $searchTerm, $fields, $options);
                $this->applySorting($query, $options['sort'] ?? [['field' => 'relevance', 'direction' => 'desc']]);
                $this->applyMultitenancy($query, $options['multitenancy'] ?? []);

                $total = $query->count();

                $this->applyPagination($query, $options['pagination'] ?? []);

                $results = $query->get();

                $this->loggingService->logBusinessEvent('fulltext_search_performed', [
                    'model' => $modelClass,
                    'search_term' => $searchTerm,
                    'fields' => $fields,
                    'total_results' => $total,
                    'search_id' => $searchId,
                ]);

                return [
                    'success' => true,
                    'search_id' => $searchId,
                    'total' => $total,
                    'results' => $results->toArray(),
                    'metadata' => [
                        'model' => $modelClass,
                        'search_term' => $searchTerm,
                        'fields' => $fields,
                        'executed_at' => now()->toIso8601String(),
                    ],
                ];
            });
        } catch (Exception $e) {
            $this->loggingService->logBusinessError($e, [
                'fulltext_search' => $modelClass,
                'search_term' => $searchTerm,
                'fields' => $fields,
            ]);
            return [
                'success' => false,
                'message' => 'Fulltext search failed: ' . $e->getMessage(),
                'results' => [],
                'total' => 0,
            ];
        }
    }

    public function searchSuggestions(string $modelClass, string $query, array $fields = [], int $limit = 10): array
    {
        try {
            $cacheKey = "search_suggestions:{$modelClass}:{$query}";

            return $this->cacheService->remember($cacheKey, 600, function () use ($modelClass, $query, $fields, $limit) {
                if (empty($fields)) {
                    $fields = $this->getSearchableFields($modelClass);
                }

                $suggestions = [];

                foreach ($fields as $field) {
                    $results = $modelClass::where($field, 'like', "%{$query}%")
                        ->distinct()
                        ->limit($limit)
                        ->pluck($field)
                        ->filter()
                        ->map(function ($value) use ($field) {
                            return [
                                'field' => $field,
                                'value' => $value,
                                'type' => $this->getFieldType($modelClass, $field),
                            ];
                        })
                        ->toArray();

                    $suggestions = array_merge($suggestions, $results);
                }

                $this->loggingService->logBusinessEvent('search_suggestions_generated', [
                    'model' => $modelClass,
                    'query' => $query,
                    'fields' => $fields,
                    'suggestions_count' => count($suggestions),
                ]);

                return [
                    'success' => true,
                    'query' => $query,
                    'suggestions' => array_slice($suggestions, 0, $limit),
                    'metadata' => [
                        'model' => $modelClass,
                        'fields' => $fields,
                        'generated_at' => now()->toIso8601String(),
                    ],
                ];
            });
        } catch (Exception $e) {
            $this->loggingService->logBusinessError($e, [
                'search_suggestions' => $modelClass,
                'query' => $query,
                'fields' => $fields,
            ]);
            return [
                'success' => false,
                'message' => 'Suggestions generation failed: ' . $e->getMessage(),
                'suggestions' => [],
            ];
        }
    }

    public function saveSearch(string $name, array $parameters, $userId = null): array
    {
        try {
            $searchId = md5(json_encode([$name, $parameters, $userId]));
            $cacheKey = "saved_search:{$searchId}";

            $savedSearch = [
                'id' => $searchId,
                'name' => $name,
                'parameters' => $parameters,
                'user_id' => $userId,
                'created_at' => now()->toIso8601String(),
            ];

            $this->cacheService->put($cacheKey, $savedSearch, 86400); // 24 hours

            $this->loggingService->logBusinessEvent('search_saved', [
                'search_id' => $searchId,
                'name' => $name,
                'user_id' => $userId,
            ]);

            return [
                'success' => true,
                'saved_search' => $savedSearch,
            ];
        } catch (Exception $e) {
            $this->loggingService->logBusinessError($e, [
                'save_search' => $name,
                'parameters' => $parameters,
                'user_id' => $userId,
            ]);
            return [
                'success' => false,
                'message' => 'Save search failed: ' . $e->getMessage(),
            ];
        }
    }

    public function getSavedSearches($userId = null): array
    {
        try {
            $cacheKey = $userId ? "saved_searches:{$userId}" : 'saved_searches:global';

            return $this->cacheService->remember($cacheKey, 3600, function () use ($userId) {
                // This would typically come from a database
                // For now, return empty array as placeholder
                return [];
            });
        } catch (Exception $e) {
            $this->loggingService->logBusinessError($e, [
                'get_saved_searches' => $userId,
            ]);
            return [];
        }
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $field => $filter) {
            if (is_array($filter)) {
                $operator = $filter['operator'] ?? 'eq';
                $value = $filter['value'] ?? null;
                $logic = $filter['logic'] ?? 'and';

                if (isset($this->operators[$operator])) {
                    $this->applyFilterCondition($query, $field, $operator, $value, $logic);
                }
            } else {
                $query->where($field, $filter);
            }
        }
    }

    protected function applyFilterCondition(Builder $query, string $field, string $operator, $value, string $logic = 'and'): void
    {
        $sqlOperator = $this->operators[$operator];

        switch ($operator) {
            case 'like':
            case 'not_like':
                $value = "%{$value}%";
                $logic === 'or' ? $query->orWhere($field, $sqlOperator, $value) : $query->where($field, $sqlOperator, $value);
                break;
            case 'in':
            case 'not_in':
                $logic === 'or' ? $query->orWhereIn($field, $value) : $query->whereIn($field, $value);
                break;
            case 'between':
                $logic === 'or' ? $query->orWhereBetween($field, $value) : $query->whereBetween($field, $value);
                break;
            case 'is_null':
                $logic === 'or' ? $query->orWhereNull($field) : $query->whereNull($field);
                break;
            case 'is_not_null':
                $logic === 'or' ? $query->orWhereNotNull($field) : $query->whereNotNull($field);
                break;
            case 'json_contains':
                $logic === 'or' ? $query->orWhereJsonContains($field, $value) : $query->whereJsonContains($field, $value);
                break;
            default:
                $logic === 'or' ? $query->orWhere($field, $sqlOperator, $value) : $query->where($field, $sqlOperator, $value);
        }
    }

    protected function applySearch(Builder $query, array $search): void
    {
        if (empty($search)) {
            return;
        }

        $term = $search['term'] ?? '';
        $fields = $search['fields'] ?? [];
        $type = $search['type'] ?? 'simple';
        $operator = $search['operator'] ?? 'or';

        if (empty($term) || empty($fields)) {
            return;
        }

        switch ($type) {
            case 'simple':
                $this->applySimpleSearch($query, $term, $fields, $operator);
                break;
            case 'fuzzy':
                $this->applyFuzzySearch($query, $term, $fields, $operator);
                break;
            case 'fulltext':
                $this->applyFulltextSearch($query, $term, $fields, $operator);
                break;
            case 'regex':
                $this->applyRegexSearch($query, $term, $fields, $operator);
                break;
        }
    }

    protected function applySimpleSearch(Builder $query, string $term, array $fields, string $operator): void
    {
        $query->where(function ($q) use ($term, $fields, $operator) {
            foreach ($fields as $field) {
                if ($operator === 'or') {
                    $q->orWhere($field, 'like', "%{$term}%");
                } else {
                    $q->where($field, 'like', "%{$term}%");
                }
            }
        });
    }

    protected function applyFuzzySearch(Builder $query, string $term, array $fields, string $operator): void
    {
        $terms = explode(' ', $term);

        $query->where(function ($q) use ($terms, $fields, $operator) {
            foreach ($terms as $searchTerm) {
                foreach ($fields as $field) {
                    if ($operator === 'or') {
                        $q->orWhere($field, 'like', "%{$searchTerm}%");
                    } else {
                        $q->where($field, 'like', "%{$searchTerm}%");
                    }
                }
            }
        });
    }

    protected function applyFulltextSearch(Builder $query, string $term, array $fields, string $operator): void
    {
        // This would require fulltext indexes setup
        // For now, fall back to simple search
        $this->applySimpleSearch($query, $term, $fields, $operator);
    }

    protected function applyRegexSearch(Builder $query, string $term, array $fields, string $operator): void
    {
        $query->where(function ($q) use ($term, $fields, $operator) {
            foreach ($fields as $field) {
                if ($operator === 'or') {
                    $q->orWhereRaw("{$field} REGEXP ?", [$term]);
                } else {
                    $q->whereRaw("{$field} REGEXP ?", [$term]);
                }
            }
        });
    }

    protected function applyAdvancedConditions(Builder $query, array $conditions): void
    {
        foreach ($conditions as $condition) {
            if (isset($condition['type']) && $condition['type'] === 'nested') {
                $query->where(function ($q) use ($condition) {
                    $this->applyAdvancedConditions($q, $condition['conditions']);
                });
            } else {
                $this->applyFilterCondition(
                    $query,
                    $condition['field'],
                    $condition['operator'] ?? 'eq',
                    $condition['value'] ?? null,
                    $condition['logic'] ?? 'and'
                );
            }
        }
    }

    protected function applyAggregations(Builder $query, array $aggregations): void
    {
        foreach ($aggregations as $aggregation) {
            $type = $aggregation['type'] ?? 'count';
            $field = $aggregation['field'] ?? '*';
            $alias = $aggregation['alias'] ?? "{$type}_{$field}";

            switch ($type) {
                case 'count':
                    $query->selectRaw("COUNT({$field}) as {$alias}");
                    break;
                case 'sum':
                    $query->selectRaw("SUM({$field}) as {$alias}");
                    break;
                case 'avg':
                    $query->selectRaw("AVG({$field}) as {$alias}");
                    break;
                case 'min':
                    $query->selectRaw("MIN({$field}) as {$alias}");
                    break;
                case 'max':
                    $query->selectRaw("MAX({$field}) as {$alias}");
                    break;
            }
        }
    }

    protected function applyGroupBy(Builder $query, array $groupBy): void
    {
        $query->groupBy($groupBy);
    }

    protected function applyHaving(Builder $query, array $having): void
    {
        foreach ($having as $condition) {
            $query->having($condition['field'], $condition['operator'] ?? '=', $condition['value'] ?? null);
        }
    }

    protected function applySorting(Builder $query, array $sort): void
    {
        if (empty($sort)) {
            return;
        }

        foreach ($sort as $sortItem) {
            $field = $sortItem['field'] ?? $sortItem;
            $direction = $sortItem['direction'] ?? 'asc';

            $query->orderBy($field, $direction);
        }
    }

    protected function applyScopes(Builder $query, array $scopes): void
    {
        foreach ($scopes as $scope => $parameters) {
            if (method_exists($query->getModel(), 'scope' . ucfirst($scope))) {
                $query->$scope($parameters);
            }
        }
    }

    protected function applyMultitenancy(Builder $query, array $multitenancy): void
    {
        if (isset($multitenancy['empresa_id'])) {
            $query->where('empresa_id', $multitenancy['empresa_id']);
        }

        if (isset($multitenancy['sucursal_id'])) {
            $query->where('sucursal_id', $multitenancy['sucursal_id']);
        }

        if (isset($multitenancy['exclude_deleted']) && $multitenancy['exclude_deleted']) {
            $query->whereNull('deleted_at');
        }
    }

    protected function applyPagination(Builder $query, array $pagination): void
    {
        $page = $pagination['page'] ?? 1;
        $perPage = $pagination['per_page'] ?? 15;
        $maxPerPage = $pagination['max_per_page'] ?? 100;

        $perPage = min($perPage, $maxPerPage);

        $query->forPage($page, $perPage);
    }

    protected function applyPostProcessing(Collection $results, array $postProcessing): void
    {
        if (isset($postProcessing['load_relations'])) {
            $results->load($postProcessing['load_relations']);
        }

        if (isset($postProcessing['append_attributes'])) {
            foreach ($results as $result) {
                foreach ($postProcessing['append_attributes'] as $attribute) {
                    $result->append($attribute);
                }
            }
        }
    }

    protected function getSearchableFields(string $modelClass): array
    {
        $model = new $modelClass();
        $fillable = $model->getFillable();

        // Filter out non-string fields
        return array_filter($fillable, function ($field) use ($model) {
            $casts = $model->getCasts();
            return !isset($casts[$field]) || in_array($casts[$field], ['string', 'array', 'json']);
        });
    }

    protected function getFulltextFields(string $modelClass): array
    {
        // This would typically come from model configuration
        // For now, return searchable text fields
        return $this->getSearchableFields($modelClass);
    }

    protected function getFieldType(string $modelClass, string $field): string
    {
        $model = new $modelClass();
        $casts = $model->getCasts();

        if (isset($casts[$field])) {
            return $casts[$field];
        }

        // Try to get from database schema
        $table = $model->getTable();
        $column = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$field]);

        if (!empty($column)) {
            $type = $column[0]->Type;
            if (str_contains($type, 'varchar') || str_contains($type, 'text')) {
                return 'string';
            } elseif (str_contains($type, 'int')) {
                return 'integer';
            } elseif (str_contains($type, 'decimal') || str_contains($type, 'float')) {
                return 'float';
            } elseif (str_contains($type, 'datetime') || str_contains($type, 'timestamp')) {
                return 'datetime';
            } elseif (str_contains($type, 'date')) {
                return 'date';
            } elseif (str_contains($type, 'json')) {
                return 'json';
            }
        }

        return 'string';
    }

    protected function getAggregations(Builder $query, array $aggregations): array
    {
        $results = [];

        foreach ($aggregations as $aggregation) {
            $type = $aggregation['type'] ?? 'count';
            $field = $aggregation['field'] ?? '*';
            $alias = $aggregation['alias'] ?? "{$type}_{$field}";

            $value = $query->get()->first()->{$alias} ?? 0;
            $results[$alias] = $value;
        }

        return $results;
    }
}
