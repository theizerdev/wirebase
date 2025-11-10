<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DynamicDatabaseExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $exportData;
    protected $tableColumns;
    protected $headings;

    public function __construct($exportData)
    {
        $this->exportData = $exportData;
        $this->tableColumns = $this->exportData['columns'];
        $this->headings = $this->generateHeadings();
    }

    public function query()
    {
        $query = DB::table($this->exportData['table'])
            ->where('empresa_id', $this->exportData['empresa_id'])
            ->where('sucursal_id', $this->exportData['sucursal_id']);

        // Aplicar condiciones
        foreach ($this->exportData['conditions'] as $index => $condition) {
            $logic = $index > 0 ? strtolower($condition['logic']) : 'and';

            if ($condition['operator'] === 'IS NULL') {
                if ($logic === 'or') {
                    $query->orWhereNull($condition['column']);
                } else {
                    $query->whereNull($condition['column']);
                }
            } elseif ($condition['operator'] === 'IS NOT NULL') {
                if ($logic === 'or') {
                    $query->orWhereNotNull($condition['column']);
                } else {
                    $query->whereNotNull($condition['column']);
                }
            } elseif (in_array($condition['operator'], ['IN', 'NOT IN'])) {
                $values = array_map('trim', explode(',', $condition['value']));
                if ($logic === 'or') {
                    if ($condition['operator'] === 'IN') {
                        $query->orWhereIn($condition['column'], $values);
                    } else {
                        $query->orWhereNotIn($condition['column'], $values);
                    }
                } else {
                    if ($condition['operator'] === 'IN') {
                        $query->whereIn($condition['column'], $values);
                    } else {
                        $query->whereNotIn($condition['column'], $values);
                    }
                }
            } else {
                if ($logic === 'or') {
                    $query->orWhere($condition['column'], $condition['operator'], $condition['value']);
                } else {
                    $query->where($condition['column'], $condition['operator'], $condition['value']);
                }
            }
        }

        // Ordenar por ID descendente para tener los registros más recientes primero
        return $query->orderBy('id', 'desc');
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($row): array
    {
        $mappedData = [];

        foreach ($this->tableColumns as $column) {
            $value = $row->{$column} ?? null;

            // Formatear valores según el tipo de columna
            $value = $this->formatValue($column, $value);

            $mappedData[] = $value;
        }

        return $mappedData;
    }

    public function title(): string
    {
        return 'Export_' . ucfirst($this->exportData['table']);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la primera fila (encabezados)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Estilo para el resto de la hoja
            'A2:Z1000' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }

    private function generateHeadings(): array
    {
        $headings = [];

        foreach ($this->tableColumns as $column) {
            $headings[] = $this->formatColumnHeading($column);
        }

        return $headings;
    }

    private function formatColumnHeading($column): string
    {
        // Mapeo de nombres de columnas comunes a nombres más amigables
        $friendlyNames = [
            'id' => 'ID',
            'nombres' => 'Nombres',
            'apellidos' => 'Apellidos',
            'documento_identidad' => 'Documento de Identidad',
            'fecha_nacimiento' => 'Fecha de Nacimiento',
            'correo_electronico' => 'Correo Electrónico',
            'telefono' => 'Teléfono',
            'direccion' => 'Dirección',
            'status' => 'Estado',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
            'empresa_id' => 'Empresa',
            'sucursal_id' => 'Sucursal',
            'codigo' => 'Código',
            'grado' => 'Grado',
            'seccion' => 'Sección',
            'turno_id' => 'Turno',
            'nivel_educativo_id' => 'Nivel Educativo',
            'school_periods_id' => 'Período Escolar',
            'representante_nombres' => 'Nombres del Representante',
            'representante_apellidos' => 'Apellidos del Representante',
            'representante_documento_identidad' => 'Documento del Representante',
            'representante_telefonos' => 'Teléfonos del Representante',
            'representante_correo' => 'Correo del Representante',
            'representante_direccion' => 'Dirección del Representante',
            'name' => 'Nombre',
            'email' => 'Correo Electrónico',
            'email_verified_at' => 'Correo Verificado',
            'password' => 'Contraseña',
            'verification_code' => 'Código de Verificación',
            'remember_token' => 'Token de Recuerdo',
            'is_active' => 'Activo',
            'is_verified' => 'Verificado',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
            'costo_matricula' => 'Costo de Matrícula',
            'costo_mensualidad' => 'Costo de Mensualidad',
            'monto' => 'Monto',
            'fecha' => 'Fecha',
            'tipo' => 'Tipo',
            'metodo_pago' => 'Método de Pago',
            'referencia' => 'Referencia',
            'comprobante' => 'Comprobante',
            'observaciones' => 'Observaciones',
            'periodo' => 'Período',
            'anio' => 'Año',
            'mes' => 'Mes',
            'dias_mora' => 'Días de Mora',
            'interes_mora' => 'Interés de Mora',
            'monto_total' => 'Monto Total',
            'monto_pagado' => 'Monto Pagado',
            'saldo_pendiente' => 'Saldo Pendiente',
            'estado' => 'Estado',
            'numero' => 'Número',
            'serie' => 'Serie',
            'caja_id' => 'Caja',
            'concepto_pago_id' => 'Concepto de Pago',
            'student_id' => 'Estudiante',
            'estudiante_id' => 'Estudiante',
            'pago_id' => 'Pago',
            'matricula_id' => 'Matrícula',
            'user_id' => 'Usuario',
            'pais_id' => 'País',
            'moneda' => 'Moneda',
            'tasa_cambio' => 'Tasa de Cambio',
            'monto_original' => 'Monto Original',
            'monto_convertido' => 'Monto Convertido',
            'fecha_pago' => 'Fecha de Pago',
            'fecha_vencimiento' => 'Fecha de Vencimiento',
            'fecha_emision' => 'Fecha de Emisión',
            'hora_inicio' => 'Hora de Inicio',
            'hora_fin' => 'Hora de Fin',
            'capacidad' => 'Capacidad',
            'color' => 'Color',
            'icono' => 'Icono',
            'prioridad' => 'Prioridad',
            'leido' => 'Leído',
            'archivado' => 'Archivado',
            'importante' => 'Importante',
            'spam' => 'Spam',
            'uuid' => 'UUID',
            'connection' => 'Conexión',
            'queue' => 'Cola',
            'payload' => 'Payload',
            'exception' => 'Excepción',
            'failed_at' => 'Falló en',
            'last_used_at' => 'Último Uso',
            'guard_name' => 'Guard',
            'display_name' => 'Nombre para Mostrar',
            'description' => 'Descripción',
            'module' => 'Módulo',
            'action' => 'Acción',
            'subject_type' => 'Tipo de Sujeto',
            'subject_id' => 'ID de Sujeto',
            'causer_type' => 'Tipo de Causante',
            'causer_id' => 'ID de Causante',
            'properties' => 'Propiedades',
            'batch_uuid' => 'UUID de Lote',
            'email' => 'Correo',
            'token' => 'Token',
            'abilities' => 'Habilidades',
            'last_used_at' => 'Último Uso',
            'expires_at' => 'Expira en',
            'two_factor_secret' => 'Secreto 2FA',
            'two_factor_recovery_codes' => 'Códigos de Recuperación 2FA',
            'two_factor_confirmed_at' => '2FA Confirmado en',
            'current_team_id' => 'ID de Equipo Actual',
            'profile_photo_path' => 'Foto de Perfil',
            'rol' => 'Rol',
            'permisos' => 'Permisos',
            'ultimo_acceso' => 'Último Acceso',
            'ip_address' => 'Dirección IP',
            'user_agent' => 'Agente de Usuario',
            'login_at' => 'Inicio de Sesión',
            'logout_at' => 'Cierre de Sesión',
            'duration' => 'Duración',
            'active' => 'Activo',
            'session_id' => 'ID de Sesión',
            'browser' => 'Navegador',
            'platform' => 'Plataforma',
            'device' => 'Dispositivo',
            'country' => 'País',
            'city' => 'Ciudad',
            'latitude' => 'Latitud',
            'longitude' => 'Longitud',
            'location' => 'Ubicación',
            'isp' => 'ISP',
            'organization' => 'Organización',
            'timezone' => 'Zona Horaria',
            'currency' => 'Moneda',
            'symbol' => 'Símbolo',
            'rate' => 'Tasa',
            'source' => 'Fuente',
            'default' => 'Predeterminado',
            'auto_update' => 'Auto Actualizar',
            'decimals' => 'Decimales',
            'decimal_separator' => 'Separador Decimal',
            'thousand_separator' => 'Separador de Miles',
            'date_format' => 'Formato de Fecha',
            'time_format' => 'Formato de Hora',
            'timezone' => 'Zona Horaria',
            'first_day_of_week' => 'Primer Día de Semana',
            'tax_rate' => 'Tasa de Impuesto',
            'tax_name' => 'Nombre del Impuesto',
            'tax_enabled' => 'Impuesto Habilitado',
            'address' => 'Dirección',
            'phone' => 'Teléfono',
            'website' => 'Sitio Web',
            'logo' => 'Logo',
            'favicon' => 'Favicon',
            'primary_color' => 'Color Primario',
            'secondary_color' => 'Color Secundario',
            'accent_color' => 'Color de Acento',
            'background_color' => 'Color de Fondo',
            'text_color' => 'Color de Texto',
            'border_color' => 'Color de Borde',
            'hover_color' => 'Color Hover',
            'active_color' => 'Color Activo',
            'disabled_color' => 'Color Desactivado',
            'success_color' => 'Color Éxito',
            'warning_color' => 'Color Advertencia',
            'error_color' => 'Color Error',
            'info_color' => 'Color Información',
            'light_color' => 'Color Claro',
            'dark_color' => 'Color Oscuro',
            'muted_color' => 'Color Apagado',
            'white' => 'Blanco',
            'black' => 'Negro',
            'transparent' => 'Transparente',
            'gradient' => 'Gradiente',
            'shadow' => 'Sombra',
            'border_radius' => 'Radio de Borde',
            'border_width' => 'Ancho de Borde',
            'border_style' => 'Estilo de Borde',
            'font_family' => 'Familia de Fuente',
            'font_size' => 'Tamaño de Fuente',
            'font_weight' => 'Peso de Fuente',
            'line_height' => 'Altura de Línea',
            'letter_spacing' => 'Espaciado de Letras',
            'text_align' => 'Alineación de Texto',
            'text_transform' => 'Transformación de Texto',
            'text_decoration' => 'Decoración de Texto',
            'opacity' => 'Opacidad',
            'visibility' => 'Visibilidad',
            'display' => 'Display',
            'position' => 'Posición',
            'top' => 'Arriba',
            'right' => 'Derecha',
            'bottom' => 'Abajo',
            'left' => 'Izquierda',
            'width' => 'Ancho',
            'height' => 'Altura',
            'min_width' => 'Ancho Mínimo',
            'min_height' => 'Altura Mínima',
            'max_width' => 'Ancho Máximo',
            'max_height' => 'Altura Máxima',
            'margin' => 'Margen',
            'padding' => 'Padding',
            'overflow' => 'Overflow',
            'z_index' => 'Z-Index',
            'float' => 'Float',
            'clear' => 'Clear',
            'flex' => 'Flex',
            'flex_direction' => 'Dirección Flex',
            'flex_wrap' => 'Flex Wrap',
            'flex_grow' => 'Flex Grow',
            'flex_shrink' => 'Flex Shrink',
            'flex_basis' => 'Flex Basis',
            'justify_content' => 'Justificar Contenido',
            'align_items' => 'Alinear Items',
            'align_self' => 'Alinear Self',
            'align_content' => 'Alinear Contenido',
            'grid' => 'Grid',
            'grid_template_columns' => 'Columnas de Grid',
            'grid_template_rows' => 'Filas de Grid',
            'grid_template_areas' => 'Áreas de Grid',
            'grid_column' => 'Columna de Grid',
            'grid_row' => 'Fila de Grid',
            'grid_area' => 'Área de Grid',
            'gap' => 'Gap',
            'column_gap' => 'Gap de Columna',
            'row_gap' => 'Gap de Fila',
            'transition' => 'Transición',
            'transition_property' => 'Propiedad de Transición',
            'transition_duration' => 'Duración de Transición',
            'transition_timing_function' => 'Función de Tiempo de Transición',
            'transition_delay' => 'Delay de Transición',
            'animation' => 'Animación',
            'animation_name' => 'Nombre de Animación',
            'animation_duration' => 'Duración de Animación',
            'animation_timing_function' => 'Función de Tiempo de Animación',
            'animation_delay' => 'Delay de Animación',
            'animation_iteration_count' => 'Contador de Iteración de Animación',
            'animation_direction' => 'Dirección de Animación',
            'animation_fill_mode' => 'Modo de Fill de Animación',
            'animation_play_state' => 'Estado de Play de Animación',
            'transform' => 'Transform',
            'transform_origin' => 'Origen de Transform',
            'rotate' => 'Rotar',
            'scale' => 'Escalar',
            'skew' => 'Sesgar',
            'translate' => 'Traducir',
            'filter' => 'Filtro',
            'blur' => 'Blur',
            'brightness' => 'Brillo',
            'contrast' => 'Contraste',
            'grayscale' => 'Escala de Grises',
            'hue_rotate' => 'Rotación de Matiz',
            'invert' => 'Invertir',
            'saturate' => 'Saturar',
            'sepia' => 'Sepia',
            'backdrop_filter' => 'Filtro de Fondo',
            'backdrop_blur' => 'Blur de Fondo',
            'backdrop_brightness' => 'Brillo de Fondo',
            'backdrop_contrast' => 'Contraste de Fondo',
            'backdrop_grayscale' => 'Escala de Grises de Fondo',
            'backdrop_hue_rotate' => 'Rotación de Matiz de Fondo',
            'backdrop_invert' => 'Invertir de Fondo',
            'backdrop_opacity' => 'Opacidad de Fondo',
            'backdrop_saturate' => 'Saturar de Fondo',
            'backdrop_sepia' => 'Sepia de Fondo',
            'outline' => 'Outline',
            'outline_color' => 'Color de Outline',
            'outline_width' => 'Ancho de Outline',
            'outline_style' => 'Estilo de Outline',
            'outline_offset' => 'Offset de Outline',
            'cursor' => 'Cursor',
            'pointer_events' => 'Eventos de Puntero',
            'user_select' => 'Selección de Usuario',
            'resize' => 'Redimensionar',
            'scroll_behavior' => 'Comportamiento de Scroll',
            'scroll_snap_type' => 'Tipo de Snap de Scroll',
            'scroll_snap_align' => 'Alineación de Snap de Scroll',
            'scroll_padding' => 'Padding de Scroll',
            'scroll_margin' => 'Margen de Scroll',
            'will_change' => 'Will Change',
            'contain' => 'Contener',
            'content' => 'Contenido',
            'counter_increment' => 'Incremento de Contador',
            'counter_reset' => 'Reset de Contador',
            'counter_set' => 'Set de Contador',
            'quotes' => 'Comillas',
            'tab_size' => 'Tamaño de Tab',
            'word_break' => 'Romper Palabra',
            'word_spacing' => 'Espaciado de Palabra',
            'word_wrap' => 'Wrap de Palabra',
            'hyphens' => 'Guiones',
            'line_break' => 'Romper Línea',
            'text_overflow' => 'Overflow de Texto',
            'text_rendering' => 'Renderizado de Texto',
            'text_size_adjust' => 'Ajuste de Tamaño de Texto',
            'text_indent' => 'Indentación de Texto',
            'vertical_align' => 'Alineación Vertical',
            'white_space' => 'Espacio en Blanco',
            'writing_mode' => 'Modo de Escritura',
            'direction' => 'Dirección',
            'unicode_bidi' => 'Unicode Bidi',
            'image_rendering' => 'Renderizado de Imagen',
            'object_fit' => 'Fit de Objeto',
            'object_position' => 'Posición de Objeto',
            'shape_image_threshold' => 'Umbral de Imagen de Forma',
            'shape_margin' => 'Margen de Forma',
            'shape_outside' => 'Outside de Forma',
            'mask' => 'Máscara',
            'mask_image' => 'Imagen de Máscara',
            'mask_mode' => 'Modo de Máscara',
            'mask_repeat' => 'Repetición de Máscara',
            'mask_position' => 'Posición de Máscara',
            'mask_size' => 'Tamaño de Máscara',
            'mask_clip' => 'Clip de Máscara',
            'mask_origin' => 'Origen de Máscara',
            'mask_composite' => 'Composición de Máscara',
            'clip_path' => 'Path de Clip',
            'clip_rule' => 'Rule de Clip',
            'color_interpolation' => 'Interpolación de Color',
            'color_interpolation_filters' => 'Interpolación de Color de Filtros',
            'color_rendering' => 'Renderizado de Color',
            'flood_color' => 'Color de Flood',
            'flood_opacity' => 'Opacidad de Flood',
            'lighting_color' => 'Color de Iluminación',
            'stop_color' => 'Color de Stop',
            'stop_opacity' => 'Opacidad de Stop',
            'color_profile' => 'Perfil de Color',
            'rendering_intent' => 'Intento de Renderizado',
            'dominant_baseline' => 'Baseline Dominante',
            'baseline_shift' => 'Shift de Baseline',
            'kerning' => 'Kerning',
            'font_feature_settings' => 'Configuración de Características de Fuente',
            'font_variant' => 'Variante de Fuente',
            'font_variant_alternates' => 'Alternativas de Variante de Fuente',
            'font_variant_caps' => 'Mayúsculas de Variante de Fuente',
            'font_variant_east_asian' => 'Asiático Oriental de Variante de Fuente',
            'font_variant_ligatures' => 'Ligaduras de Variante de Fuente',
            'font_variant_numeric' => 'Numérico de Variante de Fuente',
            'font_variant_position' => 'Posición de Variante de Fuente',
            'font_size_adjust' => 'Ajuste de Tamaño de Fuente',
            'font_stretch' => 'Stretch de Fuente',
            'font_style' => 'Estilo de Fuente',
            'src' => 'Fuente',
            'format' => 'Formato',
            'tech' => 'Tecnología',
            'unicode_range' => 'Rango Unicode',
            'font_family' => 'Familia de Fuente',
            'font_weight' => 'Peso de Fuente',
            'font_style' => 'Estilo de Fuente',
            'font_stretch' => 'Stretch de Fuente',
            'font_variant' => 'Variante de Fuente',
            'font_feature_settings' => 'Configuración de Características de Fuente',
            'font_variation_settings' => 'Configuración de Variación de Fuente',
            'ascent_override' => 'Override de Ascenso',
            'descent_override' => 'Override de Descenso',
            'line_gap_override' => 'Override de Espacio de Línea',
            'size_adjust' => 'Ajuste de Tamaño',
            'override' => 'Override',
            'palette' => 'Paleta',
            'base_palette' => 'Paleta Base',
            'override_colors' => 'Colores Override',
            'font_palette' => 'Paleta de Fuente',
            'values' => 'Valores',
            'additive_symbols' => 'Símbolos Aditivos',
            'negative' => 'Negativo',
            'pad' => 'Pad',
            'prefix' => 'Prefijo',
            'suffix' => 'Sufijo',
            'range' => 'Rango',
            'system' => 'Sistema',
            'symbols' => 'Símbolos',
            'fallback' => 'Fallback',
            'speak_as' => 'Hablar Como',
            'initial_value' => 'Valor Inicial',
            'inherits' => 'Hereda',
            'computed_value' => 'Valor Computado',
            'animation_type' => 'Tipo de Animación',
            'applies_to' => 'Aplica a',
            'percentages' => 'Porcentajes',
            'media' => 'Media',
            'computed' => 'Computado',
            'order' => 'Orden',
            'align' => 'Alinear',
            'justify' => 'Justificar',
            'self' => 'Self',
            'content' => 'Contenido',
            'items' => 'Items',
            'tracks' => 'Tracks',
            'lines' => 'Líneas',
            'areas' => 'Áreas',
            'auto' => 'Auto',
            'template' => 'Template',
            'start' => 'Inicio',
            'end' => 'Fin',
            'center' => 'Centro',
            'stretch' => 'Stretch',
            'baseline' => 'Baseline',
            'space_around' => 'Space Around',
            'space_between' => 'Space Between',
            'space_evenly' => 'Space Evenly',
            'row' => 'Fila',
            'column' => 'Columna',
            'dense' => 'Denso',
            'reverse' => 'Reverso',
            'wrap' => 'Wrap',
            'nowrap' => 'Nowrap',
            'initial' => 'Inicial',
            'inherit' => 'Heredar',
            'unset' => 'Unset',
            'revert' => 'Revert',
            'all' => 'Todo',
            'none' => 'Ninguno',
            'hidden' => 'Oculto',
            'dotted' => 'Punteado',
            'dashed' => 'Discontinuo',
            'solid' => 'Sólido',
            'double' => 'Doble',
            'groove' => 'Groove',
            'ridge' => 'Ridge',
            'inset' => 'Inset',
            'outset' => 'Outset',
            'thin' => 'Delgado',
            'medium' => 'Medio',
            'thick' => 'Grueso',
            'normal' => 'Normal',
            'bold' => 'Negrita',
            'bolder' => 'Más Negrita',
            'lighter' => 'Más Ligera',
            'italic' => 'Itálica',
            'oblique' => 'Oblicua',
            'small_caps' => 'Small Caps',
            'uppercase' => 'Mayúsculas',
            'lowercase' => 'Minúsculas',
            'capitalize' => 'Capitalizar',
            'underline' => 'Subrayado',
            'overline' => 'Overline',
            'line_through' => 'Tachado',
            'blink' => 'Blink',
            'serif' => 'Serif',
            'sans_serif' => 'Sans Serif',
            'monospace' => 'Monospace',
            'cursive' => 'Cursiva',
            'fantasy' => 'Fantasía',
            'xx_small' => 'XX Small',
            'x_small' => 'X Small',
            'small' => 'Pequeño',
            'large' => 'Grande',
            'x_large' => 'X Large',
            'xx_large' => 'XX Large',
            'smaller' => 'Más Pequeño',
            'larger' => 'Más Grande',
            'inside' => 'Dentro',
            'outside' => 'Fuera',
            'disc' => 'Disco',
            'circle' => 'Círculo',
            'square' => 'Cuadrado',
            'decimal' => 'Decimal',
            'decimal_leading_zero' => 'Decimal con Cero',
            'lower_roman' => 'Romano Minúscula',
            'upper_roman' => 'Romano Mayúscula',
            'lower_greek' => 'Griego Minúscula',
            'upper_greek' => 'Griego Mayúscula',
            'lower_latin' => 'Latín Minúscula',
            'upper_latin' => 'Latín Mayúscula',
            'armenian' => 'Armenio',
            'georgian' => 'Georgiano',
            'lower_alpha' => 'Alfa Minúscula',
            'upper_alpha' => 'Alfa Mayúscula',
            'no_open_quote' => 'Sin Comilla de Apertura',
            'no_close_quote' => 'Sin Comilla de Cierre',
            'open_quote' => 'Comilla de Apertura',
            'close_quote' => 'Comilla de Cierre',
            'attr' => 'Atributo',
            'counter' => 'Contador',
            'counters' => 'Contadores',
            'url' => 'URL',
            'linear_gradient' => 'Gradiente Lineal',
            'radial_gradient' => 'Gradiente Radial',
            'conic_gradient' => 'Gradiente Cónico',
            'repeating_linear_gradient' => 'Gradiente Lineal Repetido',
            'repeating_radial_gradient' => 'Gradiente Radial Repetido',
            'repeating_conic_gradient' => 'Gradiente Cónico Repetido',
            'cross_fade' => 'Cross Fade',
            'image' => 'Imagen',
            'element' => 'Elemento',
            'paint' => 'Pintura',
            'stroke' => 'Trazo',
            'fill' => 'Relleno',
            'evenodd' => 'Even Odd',
            'nonzero' => 'Non Zero',
            'crisp_edges' => 'Bordes Nítidos',
            'pixelated' => 'Pixelado',
            'auto' => 'Auto',
            'smooth' => 'Suave',
            'high_quality' => 'Alta Calidad',
            'optimize_speed' => 'Optimizar Velocidad',
            'optimize_quality' => 'Optimizar Calidad',
            'geometric_precision' => 'Precisión Geométrica',
            'sRGB' => 'sRGB',
            'linearRGB' => 'Linear RGB',
            'currentColor' => 'Color Actual',
            'transparent' => 'Transparente',
            'aliceblue' => 'Alice Blue',
            'antiquewhite' => 'Antique White',
            'aqua' => 'Aqua',
            'aquamarine' => 'Aquamarine',
            'azure' => 'Azure',
            'beige' => 'Beige',
            'bisque' => 'Bisque',
            'black' => 'Negro',
            'blanchedalmond' => 'Blanched Almond',
            'blue' => 'Azul',
            'blueviolet' => 'Blue Violet',
            'brown' => 'Marrón',
            'burlywood' => 'Burlywood',
            'cadetblue' => 'Cadet Blue',
            'chartreuse' => 'Chartreuse',
            'chocolate' => 'Chocolate',
            'coral' => 'Coral',
            'cornflowerblue' => 'Cornflower Blue',
            'cornsilk' => 'Cornsilk',
            'crimson' => 'Crimson',
            'cyan' => 'Cyan',
            'darkblue' => 'Dark Blue',
            'darkcyan' => 'Dark Cyan',
            'darkgoldenrod' => 'Dark Goldenrod',
            'darkgray' => 'Dark Gray',
            'darkgreen' => 'Dark Green',
            'darkgrey' => 'Dark Grey',
            'darkkhaki' => 'Dark Khaki',
            'darkmagenta' => 'Dark Magenta',
            'darkolivegreen' => 'Dark Olive Green',
            'darkorange' => 'Dark Orange',
            'darkorchid' => 'Dark Orchid',
            'darkred' => 'Dark Red',
            'darksalmon' => 'Dark Salmon',
            'darkseagreen' => 'Dark Sea Green',
            'darkslateblue' => 'Dark Slate Blue',
            'darkslategray' => 'Dark Slate Gray',
            'darkslategrey' => 'Dark Slate Grey',
            'darkturquoise' => 'Dark Turquoise',
            'darkviolet' => 'Dark Violet',
            'deeppink' => 'Deep Pink',
            'deepskyblue' => 'Deep Sky Blue',
            'dimgray' => 'Dim Gray',
            'dimgrey' => 'Dim Grey',
            'dodgerblue' => 'Dodger Blue',
            'firebrick' => 'Firebrick',
            'floralwhite' => 'Floral White',
            'forestgreen' => 'Forest Green',
            'fuchsia' => 'Fuchsia',
            'gainsboro' => 'Gainsboro',
            'ghostwhite' => 'Ghost White',
            'gold' => 'Gold',
            'goldenrod' => 'Goldenrod',
            'gray' => 'Gray',
            'green' => 'Verde',
            'greenyellow' => 'Green Yellow',
            'grey' => 'Grey',
            'honeydew' => 'Honeydew',
            'hotpink' => 'Hot Pink',
            'indianred' => 'Indian Red',
            'indigo' => 'Indigo',
            'ivory' => 'Ivory',
            'khaki' => 'Khaki',
            'lavender' => 'Lavender',
            'lavenderblush' => 'Lavender Blush',
            'lawngreen' => 'Lawn Green',
            'lemonchiffon' => 'Lemon Chiffon',
            'lightblue' => 'Light Blue',
            'lightcoral' => 'Light Coral',
            'lightcyan' => 'Light Cyan',
            'lightgoldenrodyellow' => 'Light Goldenrod Yellow',
            'lightgray' => 'Light Gray',
            'lightgreen' => 'Light Green',
            'lightgrey' => 'Light Grey',
            'lightpink' => 'Light Pink',
            'lightsalmon' => 'Light Salmon',
            'lightseagreen' => 'Light Sea Green',
            'lightskyblue' => 'Light Sky Blue',
            'lightslategray' => 'Light Slate Gray',
            'lightslategrey' => 'Light Slate Grey',
            'lightsteelblue' => 'Light Steel Blue',
            'lightyellow' => 'Light Yellow',
            'lime' => 'Lime',
            'limegreen' => 'Lime Green',
            'linen' => 'Linen',
            'magenta' => 'Magenta',
            'maroon' => 'Maroon',
            'mediumaquamarine' => 'Medium Aquamarine',
            'mediumblue' => 'Medium Blue',
            'mediumorchid' => 'Medium Orchid',
            'mediumpurple' => 'Medium Purple',
            'mediumseagreen' => 'Medium Sea Green',
            'mediumslateblue' => 'Medium Slate Blue',
            'mediumspringgreen' => 'Medium Spring Green',
            'mediumturquoise' => 'Medium Turquoise',
            'mediumvioletred' => 'Medium Violet Red',
            'midnightblue' => 'Midnight Blue',
            'mintcream' => 'Mint Cream',
            'mistyrose' => 'Misty Rose',
            'moccasin' => 'Moccasin',
            'navajowhite' => 'Navajo White',
            'navy' => 'Navy',
            'oldlace' => 'Old Lace',
            'olive' => 'Olive',
            'olivedrab' => 'Olive Drab',
            'orange' => 'Naranja',
            'orangered' => 'Orange Red',
            'orchid' => 'Orchid',
            'palegoldenrod' => 'Pale Goldenrod',
            'palegreen' => 'Pale Green',
            'paleturquoise' => 'Pale Turquoise',
            'palevioletred' => 'Pale Violet Red',
            'papayawhip' => 'Papaya Whip',
            'peachpuff' => 'Peach Puff',
            'peru' => 'Peru',
            'pink' => 'Pink',
            'plum' => 'Plum',
            'powderblue' => 'Powder Blue',
            'purple' => 'Púrpura',
            'red' => 'Rojo',
            'rosybrown' => 'Rosy Brown',
            'royalblue' => 'Royal Blue',
            'saddlebrown' => 'Saddle Brown',
            'salmon' => 'Salmon',
            'sandybrown' => 'Sandy Brown',
            'seagreen' => 'Sea Green',
            'seashell' => 'Seashell',
            'sienna' => 'Sienna',
            'silver' => 'Silver',
            'skyblue' => 'Sky Blue',
            'slateblue' => 'Slate Blue',
            'slategray' => 'Slate Gray',
            'slategrey' => 'Slate Grey',
            'snow' => 'Snow',
            'springgreen' => 'Spring Green',
            'steelblue' => 'Steel Blue',
            'tan' => 'Tan',
            'teal' => 'Teal',
            'thistle' => 'Thistle',
            'tomato' => 'Tomato',
            'turquoise' => 'Turquoise',
            'violet' => 'Violet',
            'wheat' => 'Wheat',
            'white' => 'Blanco',
            'whitesmoke' => 'White Smoke',
            'yellow' => 'Amarillo',
            'yellowgreen' => 'Yellow Green',
        ];

        // Buscar el nombre amigable, si no existe, formatear el nombre original
        return $friendlyNames[strtolower($column)] ?? ucwords(str_replace('_', ' ', $column));
    }

    private function formatValue($column, $value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Formatear valores booleanos
        if (in_array($column, ['status', 'is_active', 'is_verified', 'leido', 'archivado', 'importante', 'spam', 'default', 'auto_update', 'tax_enabled', 'active'])) {
            return $value ? 'Sí' : 'No';
        }

        // Formatear fechas
        if (in_array($column, ['fecha_nacimiento', 'fecha', 'fecha_pago', 'fecha_vencimiento', 'fecha_emision', 'created_at', 'updated_at', 'deleted_at', 'email_verified_at', 'two_factor_confirmed_at', 'failed_at', 'last_used_at', 'login_at', 'logout_at', 'last_used_at', 'birthday'])) {
            try {
                $date = \Carbon\Carbon::parse($value);
                return $date->format('d/m/Y H:i:s');
            } catch (\Exception $e) {
                return $value;
            }
        }

        // Formatear valores JSON (como teléfonos)
        if (in_array($column, ['representante_telefonos', 'telefonos', 'abilities', 'properties'])) {
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return implode(', ', $decoded);
                }
            } elseif (is_array($value)) {
                return implode(', ', $value);
            }
        }

        // Formatear valores monetarios (columnas que contienen 'monto', 'costo', 'precio', 'tasa', 'rate')
        if (preg_match('/(monto|costo|precio|tasa|rate|saldo)/i', $column) && is_numeric($value)) {
            return '$' . number_format($value, 2, ',', '.');
        }

        // Formatear porcentajes
        if (preg_match('/(porcentaje|percentage|interes)/i', $column) && is_numeric($value)) {
            return number_format($value, 2, ',', '.') . '%';
        }

        // Formatear números decimales
        if (is_numeric($value) && strpos($value, '.') !== false) {
            return number_format($value, 2, ',', '.');
        }

        // Si es un número entero grande, formatearlo con separadores de miles
        if (is_numeric($value) && $value > 999) {
            return number_format($value, 0, ',', '.');
        }

        return $value;
    }
}
