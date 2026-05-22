<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Zonas Asignadas
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Seleccione las zonas a las que este usuario tendrá acceso
        </p>
    </div>

    <!-- Mensaje de éxito -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-green-700 dark:text-green-400">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Lista de zonas disponibles -->
    <div class="space-y-3">
        @forelse($zonasDisponibles as $zona)
            <label class="flex items-start p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                <input 
                    type="checkbox" 
                    wire:model="selectedZonas" 
                    value="{{ $zona['id'] }}"
                    class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                >
                <div class="ml-3 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $zona['nombre'] }}
                        </span>
                        @if(!empty($zona['codigo']))
                            <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">
                                {{ $zona['codigo'] }}
                            </span>
                        @endif
                    </div>
                    @if(!empty($zona['descripcion']))
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ Str::limit($zona['descripcion'], 100) }}
                        </p>
                    @endif
                    @if(!empty($zona['sucursal_id']))
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            Sucursal: {{ \App\Models\Sucursal::find($zona['sucursal_id'])?->nombre ?? 'N/A' }}
                        </p>
                    @else
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            Zona global de empresa
                        </p>
                    @endif
                </div>
            </label>
        @empty
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="mt-2">No hay zonas disponibles para esta empresa</p>
            </div>
        @endforelse
    </div>

    <!-- Contador de zonas seleccionadas -->
    @if(count($zonasDisponibles) > 0)
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Zonas seleccionadas: <strong class="text-gray-900 dark:text-white">{{ count($selectedZonas) }}</strong> de {{ count($zonasDisponibles) }}
                </span>
                <button 
                    wire:click="saveZonas"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="saveZonas">
                        Guardar Zonas
                    </span>
                    <span wire:loading wire:target="saveZonas">
                        <svg class="animate-spin h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </div>
    @endif
</div>
