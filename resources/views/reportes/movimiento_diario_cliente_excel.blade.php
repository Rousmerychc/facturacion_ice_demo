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
    <th colspan = "6" style ="text-align: center; font-size: 12; font-weight: bold;">{{$cliente_datos->razon_social_cli}}</th>
</tr>
         
<tr>
    <th style="width:70px; text-align: center; background-color: #98DCC2; border: 1 solid #000000; font-weight: bold;">Fecha</th>
    <th style="width:50px; text-align: center; background-color: #98DCC2; border: 1 solid #000000; font-weight: bold;">Nro Factura</th>
    <th style="width:80px; text-align: center; background-color: #98DCC2; border: 1 solid #000000; font-weight: bold;">Codigo <br> Producto</th>
    <th style="width:120px; text-align: center; background-color: #98DCC2; border: 1 solid #000000;  font-weight: bold;">Cantidad <br> (Kilogramo) </th>
    <th style="width:180px; text-align: center; background-color: #98DCC2; border: 1 solid #000000;  font-weight: bold;">Precio Unitario</th>
    <th style="width:180px; text-align: center; background-color: #98DCC2; border: 1 solid #000000;  font-weight: bold;">Subtotal</th>
</tr>    

<tbody>
    @php
        $sum =0;
    @endphp
    @foreach($factura as $fac)
        <tr>
            <td>{{$fac->fecha}}</td>
            <td>{{$fac->id}}</td>
            <td colspan ="4" style ="border: 1 solid #000000;"></td>
        </tr>
        @foreach($detalle as $det)
            @if($fac->id == $det->id_factura)
                <tr>
                    <td colspan ="2" style ="border: 1 solid #000000;"></td>
                    <td style ="border: 1 solid #000000;">{{$det->codigo_empresa}}</td>
                    <td style = " text-align: right; border: 1 solid #000000;">{{number_format($det->cantidad,5)}}</td>
                    <td style = " text-align: right; border: 1 solid #000000;">{{number_format($det->precio_unitario_sin,5)}}</td>
                    <td style = " text-align: right; border: 1 solid #000000;">{{number_format($det->subtotal,5)}}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td colspan ="4" style ="border: 1 solid #000000;"></td>
            <td style="border: 1 solid #000000; font-weight: bold;">Subtotal</td>
            <td style = " text-align: right; border: 1 solid #000000;">{{number_format($fac->monto_total,2)}}</td>
            @php
                $sum = $sum + $fac->monto_total;
            @endphp
        </tr>
    @endforeach
    <tr>
        <td colspan ="4" style ="border: 1 solid #000000;"></td>
        <td style="border: 1 solid #000000; font-weight: bold;">Total General</td>
        <td style = " text-align: right; border: 1 solid #000000;">{{number_format($sum,2)}}</td>
    </tr>
</tbody>

  </table>