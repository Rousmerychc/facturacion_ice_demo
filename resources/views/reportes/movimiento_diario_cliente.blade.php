
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<style>
     @page {
		margin-left:1cm;
		margin-right: 1cm;
        margin-top:1cm;
	}
    table {
        border-collapse: collapse;
        width:100%;  
        font-size:0.9rem;
        border:solid 1px black;
        margin-top: 10px;
    }
    .t1col1{
        width:70px;
    }
    .t1col11{
        width:40px;
    }
    .t1col2{
        width:70px;
    }
    .t1col3{
        width:100px;
    }
    .t1col4{
        width:110px;
    }
    .t1col5{
        width:110px;
       
    }
    td{
        padding-left:10px;
        padding-right:10px;
    }
    .titulo{
        padding-top:2px !important;
        padding-bottom: 5px !important;
        margin: 0px;
    }
    .titulo2{
        padding-top:2px !important;
        padding-bottom: 5px !important;
        margin: 0px;
    }
    .textoizq{
        text-align: right;
    }
    .negrita{
        font-weight: bold;
    }
</style>

</head>

<body>

<div>
    <h3  class = "titulo">Orbol S.A.</h3>
</div>
<div>
    <center>
        <h4 class = "titulo2">
            Movimiento Diario Por Cliente<br>
            Fecha inicial: {{$fechaini}} Fecha Final: {{$fechafin}} <br>
            {{$cliente_datos->razon_social_cli}}
        </h4>
         
    </center>
    
</div>

<div>
    <table border = 1px>
        <thead>
            <tr>
                <th class = "t1col1">Fecha</th>
                <th class = "t1col11">Nro Factura</th>
                <th class = "t1col2">Codigo <br> Producto</th>
                <th class = "t1col3">Cantidad <br> (Kilogramo) </th>
                <th class = "t1col4">Precio Unitario</th>
                <th class = "t1col5">Subtotal</th>
            </tr>    
        </thead>
        <tbody>
            @php
                $sum =0;
            @endphp
            @foreach($factura as $fac)
                <tr>
                    <td>{{$fac->fecha}}</td>
                    <td>{{$fac->id}}</td>
                    <td colspan ="4"></td>
                </tr>
                @foreach($detalle as $det)
                    @if($fac->id == $det->id_factura)
                        <tr>
                            <td colspan ="2"></td>
                            <td>{{$det->codigo_empresa}}</td>
                            <td class = "textoizq">{{number_format($det->cantidad,5)}}</td>
                            <td class = "textoizq">{{number_format($det->precio_unitario_sin,5)}}</td>
                            <td class = "textoizq">{{number_format($det->subtotal,5)}}</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan ="4"></td>
                    <td class = "negrita">Subtotal</td>
                    <td class = "textoizq">{{number_format($fac->monto_total,2)}}</td>
                    @php
                        $sum = $sum + $fac->monto_total;
                    @endphp
                </tr>
            @endforeach
            <tr>
                <td colspan ="4"></td>
                <td class = "negrita">Total General</td>
                <td class = "textoizq">{{number_format($sum,5)}}</td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>

