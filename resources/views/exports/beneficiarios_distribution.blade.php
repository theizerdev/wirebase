<table>
    <thead>
        <tr>
            <th>Grupo</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ageGroups as $group => $count)
            <tr>
                <td>Edad {{ $group }}</td>
                <td>{{ $count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Niños (0-12)</th>
            <th>Mayores (60+)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $ageCounts['niños'] }}</td>
            <td>{{ $ageCounts['mayores'] }}</td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Estado civil</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($civilStatuses as $estado => $count)
            <tr>
                <td>{{ $estado ?: 'No definido' }}</td>
                <td>{{ $count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Nivel de instrucción</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($educationLevels as $nivel => $count)
            <tr>
                <td>{{ $nivel ?: 'No definido' }}</td>
                <td>{{ $count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Estado laboral</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($workStatus as $label => $count)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Estado de estudio</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($studyStatus as $label => $count)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Estado</th>
            <th>Personas en casa de alimentación</th>
            <th>Madres en casa de alimentación</th>
        </tr>
    </thead>
    <tbody>
        @foreach($casaAlimentacionByEstado as $estado => $count)
            <tr>
                <td>{{ $estado }}</td>
                <td>{{ $count }}</td>
                <td>{{ $madresCasaAlimentacionByEstado[$estado] ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
