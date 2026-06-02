<table>
    <thead>
        <tr>
            <th>Métrica</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $label => $value)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
