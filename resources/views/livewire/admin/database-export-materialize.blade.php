<div class="row">
  <div class="col s12">
    <div class="card">
      <div class="card-content">
        <h4 class="card-title">
          <i class="material-icons">cloud_download</i> Exportador de Base de Datos
        </h4>
        <p class="mb-4">Exporte datos de cualquier tabla de la base de datos con opciones avanzadas de filtrado.</p>

        <div class="row">
          <div class="col s12 m6 input-field">
            <select wire:model="selectedTable" id="tableSelect">
              <option value="" disabled selected>Seleccione una tabla</option>
              @if($exportFormat === 'sql')
                <option value="*">📦 TODA LA BASE DE DATOS</option>
              @endif
              @foreach($availableTables as $table => $name)
                <option value="{{ $table }}">{{ $name }}</option>
              @endforeach
            </select>
            <label for="tableSelect">Seleccionar Tabla *</label>
            @error('selectedTable')
              <span class="red-text">{{ $message }}</span>
            @enderror
          </div>

          <div class="col s12 m6 input-field">
            <select wire:model="exportFormat" id="formatSelect">
              <option value="" disabled selected>Seleccione formato</option>
              <option value="xlsx">Excel (.xlsx)</option>
              <option value="csv">CSV (.csv)</option>
              <option value="pdf">PDF (.pdf)</option>
              <option value="sql">SQL (.sql)</option>
            </select>
            <label for="formatSelect">Formato de Exportación *</label>
          </div>
        </div>

        @if($selectedTable && $selectedTable !== '*')
          <div class="divider"></div>

          <!-- Selección de Columnas -->
          <div class="row">
            <div class="col s12">
              <h5>
                <i class="material-icons">view_column</i> Columnas a Exportar
                <div class="right">
                  <button wire:click="selectAllColumns" type="button" class="btn-small waves-effect waves-light mr-2">
                    <i class="material-icons left">check_box</i> Seleccionar Todo
                  </button>
                  <button wire:click="deselectAllColumns" type="button" class="btn-small waves-effect waves-light">
                    <i class="material-icons left">check_box_outline_blank</i> Deseleccionar Todo
                  </button>
                </div>
              </h5>

              <div class="row">
                @foreach($availableColumns as $column => $info)
                  <div class="col s12 m4 l3">
                    <p>
                      <label>
                        <input 
                          wire:model="selectedColumns" 
                          type="checkbox" 
                          class="filled-in" 
                          id="column_{{ $column }}" 
                          value="{{ $column }}" />
                        <span>{{ $info['name'] }}</span>
                      </label>
                    </p>
                  </div>
                @endforeach
              </div>

              @error('selectedColumns')
                <span class="red-text">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="divider"></div>

          <!-- Condiciones de Exportación -->
          <div class="row">
            <div class="col s12">
              <h5>
                <i class="material-icons">filter_list</i> Condiciones de Exportación
                <button wire:click="addCondition" type="button" class="btn-small waves-effect waves-light right">
                  <i class="material-icons left">add</i> Añadir Condición
                </button>
              </h5>

              @if(count($conditions) === 0)
                <div class="card-panel grey lighten-4">
                  <i class="material-icons left">info</i>
                  No hay condiciones definidas. Puede exportar todos los registros o añadir condiciones para filtrar los datos.
                </div>
              @else
                @foreach($conditions as $index => $condition)
                  <div class="card-panel blue lighten-5">
                    <div class="row mb-0">
                      @if($index > 0)
                        <div class="col s12 m2 input-field">
                          <select wire:model="conditions.{{ $index }}.logic">
                            <option value="AND" {{ ($condition['logic'] ?? 'AND') == 'AND' ? 'selected' : '' }}>Y (AND)</option>
                            <option value="OR" {{ ($condition['logic'] ?? 'AND') == 'OR' ? 'selected' : '' }}>O (OR)</option>
                          </select>
                          <label>Lógica</label>
                        </div>
                      @endif

                      <div class="col s12 @if($index > 0) m3 @else m4 @endif input-field">
                        <select wire:model="conditions.{{ $index }}.column">
                          <option value="" selected>-- Seleccione --</option>
                          @foreach($availableColumns as $col => $info)
                            <option value="{{ $col }}" {{ ($condition['column'] ?? '') == $col ? 'selected' : '' }}>{{ $info['name'] }}</option>
                          @endforeach
                        </select>
                        <label>Columna *</label>
                        @error("conditions.{$index}.column")
                          <span class="red-text">{{ $message }}</span>
                        @enderror
                      </div>

                      <div class="col s12 @if($index > 0) m3 @else m4 @endif input-field">
                        <select wire:model="conditions.{{ $index }}.operator">
                          <option value="=" {{ ($condition['operator'] ?? '=') == '=' ? 'selected' : '' }}>Igual (=)</option>
                          <option value="!=" {{ ($condition['operator'] ?? '=') == '!=' ? 'selected' : '' }}>Diferente (!=)</option>
                          <option value="<" {{ ($condition['operator'] ?? '=') == '<' ? 'selected' : '' }}>Menor que (&lt;)</option>
                          <option value="<=" {{ ($condition['operator'] ?? '=') == '<=' ? 'selected' : '' }}>Menor o igual (&lt;=)</option>
                          <option value=">" {{ ($condition['operator'] ?? '=') == '>' ? 'selected' : '' }}>Mayor que (&gt;)</option>
                          <option value=">=" {{ ($condition['operator'] ?? '=') == '>=' ? 'selected' : '' }}>Mayor o igual (&gt;=)</option>
                          <option value="LIKE" {{ ($condition['operator'] ?? '=') == 'LIKE' ? 'selected' : '' }}>Contiene (LIKE)</option>
                          <option value="NOT LIKE" {{ ($condition['operator'] ?? '=') == 'NOT LIKE' ? 'selected' : '' }}>No contiene (NOT LIKE)</option>
                          <option value="IN" {{ ($condition['operator'] ?? '=') == 'IN' ? 'selected' : '' }}>En lista (IN)</option>
                          <option value="NOT IN" {{ ($condition['operator'] ?? '=') == 'NOT IN' ? 'selected' : '' }}>No en lista (NOT IN)</option>
                          <option value="IS NULL" {{ ($condition['operator'] ?? '=') == 'IS NULL' ? 'selected' : '' }}>Es nulo (IS NULL)</option>
                          <option value="IS NOT NULL" {{ ($condition['operator'] ?? '=') == 'IS NOT NULL' ? 'selected' : '' }}>No es nulo (IS NOT NULL)</option>
                        </select>
                        <label>Operador *</label>
                        @error("conditions.{$index}.operator")
                          <span class="red-text">{{ $message }}</span>
                        @enderror
                      </div>

                      <div class="col s12 @if($index > 0) m3 @else m4 @endif input-field">
                        <input
                          wire:model="conditions.{{ $index }}.value"
                          type="text"
                          placeholder="Valor a comparar"
                          {{ in_array($condition['operator'] ?? '', ['IS NULL', 'IS NOT NULL']) ? 'disabled' : '' }}
                        >
                        <label>Valor</label>
                        @error("conditions.{$index}.value")
                          <span class="red-text">{{ $message }}</span>
                        @enderror
                      </div>

                      <div class="col s12 m1">
                        <button wire:click="removeCondition({{ $index }})" type="button" class="btn-small red waves-effect waves-light mt-4">
                          <i class="material-icons">delete</i>
                        </button>
                      </div>
                    </div>
                  </div>
                @endforeach
              @endif
            </div>
          </div>

          <div class="divider"></div>

          <!-- Opciones de Exportación -->
          <div class="row">
            <div class="col s12 m6">
              <h5><i class="material-icons">settings</i> Opciones de Exportación</h5>
              
              <div class="input-field">
                <input wire:model="exportFileName" id="filename" type="text">
                <label for="filename">Nombre del Archivo (opcional)</label>
                <span class="helper-text">Si no especifica un nombre, se generará automáticamente</span>
              </div>
              
              <p>
                <label>
                  <input wire:model="includeHeaders" type="checkbox" class="filled-in" checked="checked" />
                  <span>Incluir encabezados en la exportación</span>
                </label>
              </p>
            </div>

            <div class="col s12 m6">
              <h5><i class="material-icons">info</i> Información de Exportación</h5>
              <div class="collection">
                <div class="collection-item">
                  <span class="badge">{{ $selectedTable === '*' ? 'TODA LA BASE DE DATOS' : ucfirst(str_replace('_', ' ', $selectedTable)) }}</span>
                  Tabla:
                </div>
                
                @if($selectedTable !== '*')
                <div class="collection-item">
                  <span class="badge">{{ count($selectedColumns) }}</span>
                  Columnas seleccionadas:
                </div>
                <div class="collection-item">
                  <span class="badge">{{ count($conditions) }}</span>
                  Condiciones:
                </div>
                @endif
                
                <div class="collection-item">
                  <span class="badge">{{ strtoupper($exportFormat) }}</span>
                  Formato:
                </div>
                
                <div class="collection-item">
                  <span class="badge">{{ $exportFileName ?: $this->generateDefaultFileName() }}.{{ $exportFormat }}</span>
                  Nombre del archivo:
                </div>
              </div>
            </div>
          </div>
        @endif

        <!-- Botones de Acción -->
        <div class="row">
          <div class="col s12 center">
            <button
              wire:click="startExport"
              wire:loading.attr="disabled"
              type="button"
              class="btn-large waves-effect waves-light green mr-2"
              {{ !$selectedTable || ($selectedTable !== '*' && count($selectedColumns) === 0) ? 'disabled' : '' }}
            >
              <i class="material-icons left">cloud_download</i>
              <span wire:loading.remove>
                @if($exportFormat === 'sql' && $selectedTable === '*')
                  Exportar Base de Datos Completa
                @elseif($exportFormat === 'sql')
                  Exportar SQL
                @else
                  Exportar Datos
                @endif
              </span>
              <span wire:loading>Exportando...</span>
            </button>
            
            <button
              wire:click="mount"
              wire:loading.attr="disabled"
              type="button"
              class="btn-large waves-effect waves-light grey"
            >
              <i class="material-icons left">refresh</i>
              <span wire:loading.remove>Reiniciar Formulario</span>
              <span wire:loading>Reiniciando...</span>
            </button>
          </div>
        </div>

        <!-- Barra de Progreso -->
        @if($isExporting)
          <div class="row">
            <div class="col s12">
              <div class="progress">
                <div class="determinate" style="width: {{ $exportProgress }}%"></div>
              </div>
              <p class="center">{{ $exportProgress }}% completado</p>
            </div>
          </div>
        @endif

        <!-- Mensajes de Estado -->
        @if(session()->has('message'))
          <div class="row">
            <div class="col s12">
              <div class="card-panel {{ session('message_type', 'blue') === 'error' ? 'red' : (session('message_type', 'blue') === 'success' ? 'green' : 'blue') }} white-text">
                <i class="material-icons left">{{ session('message_type', 'blue') === 'error' ? 'error' : (session('message_type', 'blue') === 'success' ? 'check_circle' : 'info') }}</i>
                {{ session('message') }}
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@script
<script>
  document.addEventListener('livewire:init', function() {
    // Inicializar selects de Materialize
    setTimeout(function() {
      var elems = document.querySelectorAll('select');
      var instances = M.FormSelect.init(elems);
    }, 100);
    
    // Escuchar eventos de Livewire para reinicializar selects cuando se actualicen
    $wire.on('contentChanged', function() {
      setTimeout(function() {
        var elems = document.querySelectorAll('select');
        var instances = M.FormSelect.init(elems);
      }, 100);
    });
  });
</script>
@endscript