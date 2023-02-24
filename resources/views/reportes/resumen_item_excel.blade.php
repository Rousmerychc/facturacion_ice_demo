<table>
<thead>


<tr>
    <th colspan = "6" style ="text-align: center; font-size: 18; font-weight: bold;">Orbol S.A.</th>
</tr>  

<tr>
    <th colspan = "6" style ="text-align: center; font-size: 14; font-weight: bold;"> Movimiento Diario </th>
</tr>
<tr>
    <th colspan = "6" style ="text-align: center; font-size: 10; font-weight: bold;"> Fecha inicial: {{$fechaini}} Fecha Final: {{$fechafin}}</th>
</tr>

         
<tr>
    <th style="width:200px; text-align: center; background-color: #98DCC2; border: 1 solid #000000; font-weight: bold;">Descripcion Producto</th>
    <th style="width:120px; text-align: center; background-color: #98DCC2; border: 1 solid #000000;  font-weight: bold;">Cantidad <br> (Kilogramo) </th>
    <th style="width:180px; text-align: center; background-color: #98DCC2; border: 1 solid #000000;  font-weight: bold;">Precio Total</th>
</tr>    

<tbody>
    @php
        $sum =0;
    @endphp
        @foreach($consulta as $c)
          
                <tr>
                    <td style = "border: 1 solid #000000;">{{$c->descripcion}}</td>
                    <td style = " text-align: right; border: 1 solid #000000;">{{number_format($c->cantidad,5)}}</td>
                    <td style = " text-align: right; border: 1 solid #000000;">{{number_format($c->total,5)}}</td>
                </tr>
                @php
                        $sum = $sum + $c->total;
                    @endphp
        @endforeach
        <tr>
            <td style ="border: 1 solid #000000;"></td>
            <td style="border: 1 solid #000000; font-weight: bold;">Total General</td>
            <td style = " text-align: right; border: 1 solid #000000;">{{number_format($sum,2)}}</td>
        </tr>

</tbody>

  </table>