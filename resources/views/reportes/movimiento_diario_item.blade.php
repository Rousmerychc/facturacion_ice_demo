
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
        font-size:0.8rem;
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
        width:200px;
    }
    .t1col3{
        width:80px;
    }
    .t1col4{
        width:90px;
    }
    .t1col5{
        width:90px;
       
    }
    td{
        padding-left:7px;
        padding-right:7px;
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
            Movimiento Diario Por Producto<br>
            Fecha inicial: {{$fechaini}} Fecha Final: {{$fechafin}} <br>
            {{$datos_item->descripcion}}
        </h4>
         
    </center>
    
</div>

<div>
    <table border = 1px>
        <thead>
            <tr>
                <th class = "t1col1">Fecha</th>
                <th class = "t1col11">Nro Factura</th>
                <th class = "t1col2">Razon Social</th>
                <th class = "t1col3">Cantidad <br> (Kilogramo) </th>
                <th class = "t1col4">Precio Unitario</th>
                <th class = "t1col5">Subtotal</th>
            </tr>    
        </thead>
        <tbody>
            @php
                $sum =0;
            @endphp
            @foreach($detalle as $det)
                
                <tr>
                    <td>{{$det->fecha}}</td>
                    <td>{{$det->id}}</td>
                    <td>{{$det->razon_social}}</td>
                    <td class = "textoizq">{{number_format($det->cantidad,5)}}</td>
                    <td class = "textoizq">{{number_format($det->precio_unitario_sin,5)}}</td>
                    <td class = "textoizq">{{number_format($det->subtotal,5)}}</td>
                </tr>
                @php
                        $sum = $sum + $det->subtotal;
                    @endphp
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

