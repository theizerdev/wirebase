<table>
    <thead>
        <tr>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Cédula</th>
            <th>Edad</th>
            <th>Fecha Nacimiento</th>
            <th>Teléfono Principal</th>
            <th>Teléfono Alternativo</th>
            <th>Estado Civil</th>
            <th>Estudia Actualmente</th>
            <th>Nivel Instrucción</th>
            <th>Último Título Obtenido</th>
            <th>Trabaja Actualmente</th>
            <th>Lugar de Trabajo</th>
            <th>Ocupación</th>
            <th>Ingreso Mensual</th>
            <th>Posee Habilidad Productiva</th>
            <th>Habilidad Productiva</th>
            <th>Pertenece Organización Social</th>
            <th>Tipo Organización Social</th>
            <th>Asignaciones Económicas</th>
            <th>Responsable</th>
            <th>Estado</th>
            <th>Municipio</th>
            <th>Parroquia</th>
            <th>Fecha Creación</th>
            <th>Fecha Actualización</th>
        </tr>
    </thead>
    <tbody>
        @foreach($beneficiarios as $beneficiario)
        <tr>
            <td>{{ $beneficiario->nombres }}</td>
            <td>{{ $beneficiario->apellidos }}</td>
            <td>{{ $beneficiario->cedula }}</td>
            <td>{{ $beneficiario->edad }}</td>
            <td>{{ $beneficiario->fecha_nacimiento ? $beneficiario->fecha_nacimiento->format('d/m/Y') : '' }}</td>
            <td>{{ $beneficiario->telefono_principal }}</td>
            <td>{{ $beneficiario->telefono_alternativo }}</td>
            <td>{{ $beneficiario->estado_civil }}</td>
            <td>{{ $beneficiario->estudia_actualmente ? 'Sí' : 'No' }}</td>
            <td>{{ $beneficiario->nivel_instruccion }}</td>
            <td>{{ $beneficiario->ultimo_titulo_obtenido }}</td>
            <td>{{ $beneficiario->trabaja_actualmente ? 'Sí' : 'No' }}</td>
            <td>{{ $beneficiario->lugar_trabajo }}</td>
            <td>{{ $beneficiario->ocupacion }}</td>
            <td>{{ $beneficiario->ingreso_mensual }}</td>
            <td>{{ $beneficiario->posee_habilidad_productiva ? 'Sí' : 'No' }}</td>
            <td>{{ $beneficiario->habilidad_productiva }}</td>
            <td>{{ $beneficiario->pertenece_organizacion_social ? 'Sí' : 'No' }}</td>
            <td>{{ $beneficiario->tipo_organizacion_social }}</td>
            <td>{{ $beneficiario->asignaciones_economicas ? implode(', ', $beneficiario->asignaciones_economicas) : '' }}</td>
            <td>{{ $beneficiario->responsable ? $beneficiario->responsable->nombre_completo : '' }}</td>
            <td>{{ $beneficiario->estado ? $beneficiario->estado->nombre : '' }}</td>
            <td>{{ $beneficiario->municipio ? $beneficiario->municipio->nombre : '' }}</td>
            <td>{{ $beneficiario->parroquia ? $beneficiario->parroquia->nombre : '' }}</td>
            <td>{{ $beneficiario->created_at?->format('d/m/Y H:i') }}</td>
            <td>{{ $beneficiario->updated_at?->format('d/m/Y H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>