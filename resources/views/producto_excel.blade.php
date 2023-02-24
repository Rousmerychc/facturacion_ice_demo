<table>
    @foreach($producto as $p)
    <tr>
        <td>{{$p->codigo_actividad}}</td>
        <td>{{$p->codigo_producto}}</td>
        <td>{{$p->descripcion_producto}}</td>
    </tr>
    @endforeach
</table>