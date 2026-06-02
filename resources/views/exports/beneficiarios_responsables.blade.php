<table>
    <thead>
        <tr>
            <th>Responsable</th>
            <th>Beneficiarios</th>
        </tr>
    </thead>
    <tbody>
        @foreach($responsables as $nombre => $cantidad)
            <tr>
                <td>{{ $nombre }}</td>
                <td>{{ $cantidad }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
