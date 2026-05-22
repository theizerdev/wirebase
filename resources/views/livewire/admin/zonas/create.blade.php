<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.zonas.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver al listado
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Crear Nueva Zona</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Complete los datos para crear una nueva zona de acceso</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <form wire:submit="save">
                <!-- Nombre -->
                <div class="mb-4">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nombre"
                        wire:model="nombre"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('nombre') border-red-500 @enderror"
                        placeholder="Ej: Zona Norte"
                    >
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Código -->
                <div class="mb-4">
                    <label for="codigo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Código
                    </label>
                    <input 
                        type="text" 
                        id="codigo"
                        wire:model="codigo"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('codigo') border-red-500 @enderror"
                        placeholder="Ej: ZN-001"
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Código único opcional para identificar la zona</p>
                    @error('codigo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descripción
                    </label>
                    <textarea 
                        id="descripcion"
                        wire:model="descripcion"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('descripcion') border-red-500 @enderror"
                        placeholder="Descripción detallada de la zona..."
                    ></textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Distrito -->
                <div class="mb-4">
                    <label for="distrito" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Distrito
                    </label>
                    <input 
                        type="text" 
                        id="distrito"
                        wire:model="distrito"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('distrito') border-red-500 @enderror"
                        placeholder="Ej: Distrito Norte, Área Metropolitana"
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Distrito o área geográfica de la zona</p>
                    @error('distrito')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sucursal -->
                <div class="mb-4">
                    <label for="sucursal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sucursal
                    </label>
                    <select 
                        id="sucursal_id"
                        wire:model="sucursal_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('sucursal_id') border-red-500 @enderror"
                    >
                        <option value="">Zona Global (todas las sucursales)</option>
                        @foreach(\App\Models\Sucursal::where('empresa_id', auth()->user()->empresa_id)->get() as $sucursal)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deje en blanco para crear una zona global de empresa</p>
                    @error('sucursal_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado Activo -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="activo"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        >
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Zona activa</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Las zonas inactivas no aparecerán en las opciones de asignación</p>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a 
                        href="{{ route('admin.zonas.index') }}"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancelar
                    </a>
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="save">Crear Zona</span>
                        <span wire:loading wire:target="save">
                            <svg class="animate-spin h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
