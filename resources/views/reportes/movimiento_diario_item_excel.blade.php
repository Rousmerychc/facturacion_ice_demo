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
    <th colspan = "6" style ="text-align: center; font-size: 12; font-weight: bold;">{{$datos_item->descripcion}}</th>
</tr>
         
<tr>
    <th style="width:70px; text-align: center; background-color: #98DCC2; border: 1 solid #000000; font-weight: bold;">Fecha</th>
    <th style="width: 70px;px; text-align: center; background-color: #98DCC2; border: 1 solid #000000; font-weight: bold;">Nro Factura</th>
    <th style="width:200px; text-align: center; background-color: #98DCC2; border: 1 solid #000000; font-weight: bold;">Razon Social</th>
    <th style="width:120px; text-align: center; background-color: #98DCC2; border: 1 solid #000000;  font-weight: bold;">Cantidad <br> (Kilogramo) </th>
    <th style="width:180px; text-align: center; background-color: #98DCC2; border: 1 solid #000000;  font-weight: bold;">Precio Unitario</th>
    <th style="width:180px; text-align: center; background-color: #98DCC2; border: 1 solid #000000;  font-weight: bold;">Subtotal</th>
</tr>    

<tbody>
    @php
        $sum =0;
    @endphp
        @foreach($detalle as $det)
          
                <tr>
                    <td style = "border: 1 solid #000000;">{{$det->fecha}}</td>
                    <td style = "border: 1 solid #000000;">{{$det->id}}</td>
                    <td style = "border: 1 solid #000000;">{{$det->razon_social}}</td>
                    <td style = " text-align: right; border: 1 solid #000000;">{{number_format($det->cantidad,5)}}</td>
                    <td style = " text-align: right; border: 1 solid #000000;">{{number_format($det->precio_unitario_sin,5)}}</td>
                    <td style = " text-align: right; border: 1 solid #000000;">{{number_format($det->subtotal,5)}}</td>
                </tr>
                @php
                        $sum = $sum + $det->subtotal;
                    @endphp
        @endforeach
        <tr>
            <td colspan ="4" style ="border: 1 solid #000000;"></td>
            <td style="border: 1 solid #000000; font-weight: bold;">Total General</td>
            <td style = " text-align: right; border: 1 solid #000000;">{{number_format($sum,2)}}</td>
        </tr>

</tbody>

  </table>