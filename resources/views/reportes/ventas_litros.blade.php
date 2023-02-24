
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
       width:40px;
    }
    .t1col2{
        width:20px;
    }
    .t1col3{
        width:200px;
    }
    .t1col4{
        width:50px;
    }
    .t1col5{
        width:80px;
    }
    .t1col6{
        width:80px;
    }
    .t1col7{
        width:80px;
    }
    .t1col8{
        width:80px;
    }
    .t1col9{
        width:80px;
    }
    .t1col10{
        width:80px;
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
            Ventas por Litros por Sucursal <br>
            Fecha inicial: {{$fechaini}} Fecha Final: {{$fechafin}}
        </h4>
         
    </center>
    
</div>

<div>
    <table border = 1px>
        <thead>
            <tr>
                <th class = "t1col1">Grupo/Fecha</th>
                <th class = "t1col2">Nro</th>
                <th class = "t1col3">Descripcion</th>
                <th class = "t1col4">Total Litros</th>
                <th class = "t1col5">Precio Total Factura</th>
                <th class = "t1col6">Precio Total Base Iva</th>
                <th class = "t1col7">Total Iva</th>
                <th class = "t1col8">Precio Total Neto Iva</th>
                <th class = "t1col9">Total ICE Especifico</th>
                <th class = "t1col10">Total ICE Porcentual</th>
            </tr>    
        </thead>
        <tbody>
            @php
                $sum_total_litros =0;
                $sum_total_factura = 0;
                $sum_total_base_iva = 0;
                $sum_total_iva = 0;
                $sum_total_neto_iva = 0;
                $sum_ice_especifico =0;
                $sum_ice_porcentual =0;
                $id_grupo = 0;
                $id_grupo1 = 0;
                $sw = 0;
            @endphp
            @foreach($consulta as $con)
                                
                @if($id_grupo <> $con->id_grupo)
                   
                        <tr>
                            <td colspan ="2"></td>
                            <td class = "negrita">Total</td>
                            <td class = "textoizq">{{number_format($sum_total_litros,2)}}</td>
                            <td class = "textoizq">{{number_format($sum_total_factura,2)}}</td>
                            <td class = "textoizq">{{number_format($sum_total_base_iva,2)}}</td>
                            <td class = "textoizq">{{number_format($sum_total_iva,2)}}</td>
                            <td class = "textoizq">{{number_format($sum_total_neto_iva,2)}}</td>
                            <td class = "textoizq">{{number_format($sum_ice_especifico,2)}}</td>
                            <td class = "textoizq">{{number_format($sum_ice_porcentual,2)}}</td>
                        </tr>
                        @php
                            $sum_total_litros =0;
                            $sum_total_factura = 0;
                            $sum_total_base_iva = 0;
                            $sum_total_iva = 0;
                            $sum_total_neto_iva = 0;
                            $sum_ice_especifico =0;
                            $sum_ice_porcentual =0;
                        @endphp
                        <tr>
                        <td colspan ="10">
                        {{$con->descripcion_grupo}} - {{$id_grupo}} -{{ $con->id_grupo}} - {{$sw}}
                        </td>
                    </tr>
                    @php
                        $id_grupo = $con->id_grupo;
                    @endphp
                @endif

                <tr>
                    <td>{{$con->fecha}}</td>
                    <td>{{$con->id_factura}}</td>
                    <td>{{$con->descripcion}}</td>
                    <td class = "textoizq">{{number_format(($con->cantidad * $con->cantidad_litros_x_unidad),2)}}</td>
                    <td class = "textoizq">{{number_format($con->subtotal,2)}}</td>
                    @php
                        $op1 = $con->subtotal - $con->ice_esp - $con->ice_por;
                        $op2 = $op1 * 0.13;
                     @endphp
                    <td class = "textoizq">{{number_format($op1,2)}}</td>
                    <td class = "textoizq">{{number_format($op2,2)}}</td>
                    <td class = "textoizq">{{number_format(($op1 - $op2),2)}}</td>
                    <td class = "textoizq">{{number_format($con->ice_esp,2)}}</td>
                    <td class = "textoizq">{{number_format($con->ice_por,2)}}</td>
                    
                </tr> 
                  
                        @php
                            $sum_total_litros = $sum_total_litros + ($con->cantidad * $con->cantidad_litros_x_unidad);
                            $sum_total_factura =  $sum_total_factura  + $con->subtotal;
                            $sum_total_base_iva =  $sum_total_base_iva  +  $op1;
                            $sum_total_iva =  $sum_total_iva  + $op2 ;
                            $sum_total_neto_iva =  $sum_total_neto_iva  + ($op1 - $op2);
                            $sum_ice_especifico =  $sum_ice_especifico + $con->ice_esp;
                            $sum_ice_porcentual =  $sum_ice_porcentual + $con->ice_por;
                        @endphp

                    
                    
            @endforeach
            <tr>
                <td colspan ="2"></td>
                <td class = "negrita">Total</td>
                <td class = "textoizq">{{number_format($sum_total_litros,2)}}</td>
                <td class = "textoizq">{{number_format($sum_total_factura,2)}}</td>
                <td class = "textoizq">{{number_format($sum_total_base_iva,2)}}</td>
                <td class = "textoizq">{{number_format($sum_total_iva,2)}}</td>
                <td class = "textoizq">{{number_format($sum_total_neto_iva,2)}}</td>
                <td class = "textoizq">{{number_format($sum_ice_especifico,2)}}</td>
                <td class = "textoizq">{{number_format($sum_ice_porcentual,2)}}</td>
              
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>

