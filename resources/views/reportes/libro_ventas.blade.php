
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
		margin-right:1cm;
        margin-top:1cm;
        size: letter landscape;
	}
    table {
        border-collapse: collapse;
        width:100%;  
        font-size:0.5rem;
        border:solid 1px black;
        margin-top: 10px;
    }
    .t1col1{
       width:50px;
    }
    .t1col2{
        width:25px;
    }
    .t1col3{
        width:100px;
    }
    .t1col4{
        width:60px;
    }
    .t1col5{
        width:20px;
    }
    .t1col6{
        width:200px;
    }
    .t1col7{
        width:80px;
    }
    .t1col8{
        width:60px;
    }
    .t1col9{
        width:60px;
    }
    .t1col10{
        width:80px;
    }
    .t1col11{
        width:45px;
    }
   
    td{
        padding-left:3px;
        padding-right:3px;
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
    <h3  class = "titulo">DEMO</h3>
</div>
<div>
    <center>
        <h4 class = "titulo2">
            Libro de Ventas <br>
            Fecha inicial: {{$fechaini}} Fecha Final: {{$fechafin}}
        </h4>
         
    </center>
    
</div>

<div>
    <table border = 1px>
        <thead>
            <tr>
                <th class = "t1col1">Fecha</th>
                <th class = "t1col2">Nro</th>
                <th class = "t1col3">CUF</th>
                <th class = "t1col4">Nro Documento</th>
                <th class = "t1col5">Complemnto</th>
                <th class = "t1col6">Nombre/Razon Social</th>
                <th class = "t1col7">Importe Bs.</th>
                <th class = "t1col8">ICE %</th>
                <th class = "t1col9">ICE Especifico</th>
                <th class = "t1col10">Subtotal</th>
                <th class = "t1col11">Estado</th>

            </tr>    
        </thead>
        <tbody>
            @php
                $sum_monto_total =0;
                $sum_ice_porcentual =0;
                $sum_ice_especial =0;
                $sum_subototal =0;
            @endphp
            @foreach($factura as $fac)
                <tr>
                    <td>{{$fac->fecha}}</td>
                    <td>{{$fac->id_factura}}</td>
                    <td>{{$fac->cuf}}</td>
                    <td>{{$fac->nro_documento}}</td>
                    <td>{{$fac->complemento}}</td>
                    <td>{{$fac->razon_social}}</td>
                    <td class = "textoizq">{{number_format($fac->monto_total,2)}}</td>
                    <td class = "textoizq">{{number_format($fac->ice_porcentual,2)}}</td>
                    <td class = "textoizq">{{number_format($fac->ice_especial,2)}}</td>
                    <td class = "textoizq">{{number_format($fac->monto_total-$fac->ice_porcentual+$fac->ice_especial,2)}}</td>

                    @if($fac->estado != 0)
                    <td>A</td>
                    @else
                    <td></td>
                    @php
                        $sum_monto_total = $sum_monto_total + $fac->monto_total ;
                        $sum_ice_porcentual = $sum_ice_porcentual + $fac->ice_porcentual;
                        $sum_ice_especial = $sum_ice_especial + $fac->ice_especial;
                        $sum_subototal = $sum_subototal + ($fac->monto_total-$fac->ice_porcentual+$fac->ice_especial);
                    @endphp
                    @endif

                    
            @endforeach
            <tr>
                <td colspan ="5"></td>
                <td class = "negrita">Total General</td>
                <td class = "textoizq">{{$sum_monto_total}}</td>
                <td class = "textoizq">{{$sum_ice_porcentual}}</td>
                <td class = "textoizq">{{$sum_ice_especial}}</td>
                <td class = "textoizq">{{$sum_subototal}}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>

