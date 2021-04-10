<table>
    <thead>
    <tr>
        <th>date</th>
        <th>content</th>
        <th>amount</th>
        <th>type</th>
    </tr>
    </thead>
    <tbody>
    @for($i= 0 ;$i < 20000 ; $i ++)
        <tr>
            <td>{{ now() }}</td>
            <td>abc</td>
            <td>+50.000</td>
            <td>deposit</td>
        </tr>
    @endfor
    </tbody>
</table>