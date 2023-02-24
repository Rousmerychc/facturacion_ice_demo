<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            font-size: 65%;
            margin:0px;
            padding:0px;
            font-family:sans-serif !important;
        }
        .divpagina{
            margin: 0px;
            /* border: solid 1px black; */
        }
        .divtitulo{
            width:50%; 
        }
        .t1col1{
             width:450px;
             padding-top:20px; 
        }
        .t1col2{
            width:300px; 
            padding-top:20px; 
        }
        .titfac{
            font-size:0.9rem;
            font-weight: bold;
            padding-top:25px;
        }
        .datosnegrita{
            font-weight: bold;
        }
        .tabla2{
            width:100%;  
        }
        .espacio{
            margin-top:20px;
        }
        .espacio2{
            margin-top:5px;
        }
        .t2col3{
            /* text-align: right; */
            width: 100px;
            padding-left:30px ;
        }
        .t2col1{
            width:190px;
        }
        .t2col4{
            width:240px;
            
        }
        .margenizq{
            padding-left: 15px;
        }
        .margendatos{
            padding-left:4px;
            padding-right:4px;
        }
        .textoizq{
            text-align: right;
        }
        .t3col0{
            width:60px;
        }
        .t3col1{
            width:50px;
        }
        .t3col2{
            width:70px;
        }
        .t3col3{
            width:80px;
        }
        
        .t3col4{
            width:180px;
        }
        .t3col5{
            width:80px !important;
        }
        .t3col56{
            width:180px !important;
        }
        
        table {
            border-collapse: collapse;
        }
        .tabla3{
            width:710px;
        }
        p{
            font-size:75% !important;
            text-align: justify;
        }
        .t4col1{
            width:85%;
        }
        .tablapro .top td{
            border-top: solid 1px black;
        }
        .tablapro .izq td{
            border-left: solid 1px black;
        }
        .tablapro .der td{
            border-right: solid 1px black;
        }        
        .tablapro .bot td{
            border-bottom: solid 1px black;
        }

        .top1{
            border-top: solid 1px black;
        }
        .izq1{
            border-left: solid 1px black;
        }
        .der1{
            border-right: solid 1px black;
        }        
        .bot1{
            border-bottom: solid 1px black;
        }
        .tabla50{
            width:50%;
        }
        
        .tabla5{
            width:90%;
        }
        .tabla_pa_inf{
            border: solid 1px black;
            width:100%;
        }
        .t5col1{
            width:40%;
        }
        .made{
            padding:0px;
            margin-top:0px;
        }
        .logo{
            height:20px;
            width:200px;
        }
        span{
            font-weight: none !important;
        }
        .qr{
            margin: 0px;
            padding:0px;
           
        }
        .tabla4{
            width:100%;
            padding: 0px !important;
            margin:0px !important;
        }
        .t4col1{
            width:85%;
            padding: 0px !important;
            margin:0px !important;   
        }
        .encabezado_datos{
            font-size:0.65rem !important;
           
        }
        .espaciotd{
            padding-top: 4px;
            padding-bottom:4px;
        }
        .espaciotd1{
            padding-top: 2px;
            padding-bottom:2px;
        }
        .fondo{
            background:#E2F2F4;
        }
    </style>
</head>
<body>
    <div>
        <table>
            <tr>
                <td class = "t1col1">
                    <div class = "divtitulo">
                        <center>
                            <span class = "encabezado_datos">
                                <!-- <img  src="{{ storage_path('/logo/sibelis.png') }}" height="55" width="150"/> <br> -->
                                <!-- <b> SIBELIS SRL <br> -->
                                {{$factura->descripcion}} <br>
                                No Punto de Venta {{$factura->punto_venta}} <br>
                                {{$factura->direccion}}<br>
                                Telefono: {{$factura->telefono}} <br>
                                {{$factura->municipio}}</b>
                            </span>
                            
                        </center>
                    </div>
                
                </td>
                <td class = "t1col2">
                    <table  style = "width: 60%;">
                        <tr>
                            <td class = "margendatos"> <b>NIT</b></td>
                            <td class = "margendatos"> {{$datos_empresa->nit}}</td>
                        </tr>
                        <tr>
                            <td class = "margendatos"> <b>Nº</b> </td>
                            <td class = "margendatos">{{$factura->nro_nota_venta}}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan = "2" class = "titfac"> <div ><center> {{$factura->tipo_nota}}</center> </div></td>
            </tr>
        </table>
    </div>
    
    <div class = "espacio">
        <table class = "tabla2" >
            <tr >
                <th class = "t2col1"></th>
                <th></th>
                <th></th>
                <th class = "t2col4"></th>
            </tr>
            <tr>
                <td class = "datosnegrita">Fecha: </td>
                <td class = "margenizq">{{$factura->fecha_hora}}</td>
                <td class = "t2col3 datosnegrita">NIT/CI/CEX:  </td>
                <td class = "margenizq">{{$factura->nro_documento}} &nbsp; {{$factura->complemento}}</td>
            </tr>
            <tr>
                <td class = "datosnegrita">Nombre/Razón Social: <br> </td>
                <td class = "margenizq">{{$factura->razon_social}}</td>
                <td class = "t2col3 datosnegrita">Cod. Cliente: <br></td>
                <td class = "margenizq"> {{$factura->codigo_cliente}}</td>
            </tr>           
            
        </table>
    </div>
    
    <div class = "espacio tablapro ">
        <table class = "tabla3">
            <tr> 
                <th class = " t3col0"> </th>
                <th class = " t3col2"></th>
                <th class = " t3col3"></th>
                <th class = " t3col4"></th>
                <th class = " t3col5"></th>
                <th class = " t3col5"></th>
                <th class = " t3col5"></th>
            </tr>
            <tr class = "datosnegrita top izq der">
                <td class = "espaciotd fondo"><center>CODIGO PRODUCTO</center></td>
                <td class = "espaciotd fondo"><center>CANTIDAD <br></td>
                <td class = "espaciotd fondo"><center>UNIDAD DE MEDIDA  </center></td>
                <td class = "espaciotd fondo"><center>DESCRIPCIÓN <br></center></td>
                <td class = "espaciotd fondo"><center>PRECIO UNITARIO </td>
                <td class = "espaciotd fondo"><center>DESCUENTO</center></td>
                <td class = "espaciotd fondo"><center>SUBTOTAL</center></td> 
            </tr>
            
            @foreach($detalle as $d)
                <tr class = " top izq der">
                    <td class = "margendatos espaciotd">{{$d->codigo_producto_empresa}}</td>
                    <td class = "margendatos espaciotd textoizq">{{ number_format($d->cantidad,2) }}</td>
                    <td class = "margendatos espaciotd">{{$d->unidad_medida_des}}</td>
                    <td class = "margendatos espaciotd">{{$d->descripcion}}</td>    
                    <td class = "margendatos espaciotd textoizq">{{number_format($d->precio,2)}}</td>
                    <td class = "margendatos espaciotd textoizq">0.00 </td>
                    <td class = "margendatos espaciotd textoizq">{{number_format($d->subtotal,2)}}</td>
                </tr>
            @endforeach
            
            <tr >
                <td colspan ="4"  class ="espaciotd1  margendatos datosnegrita top1 "></td>
                <td colspan = "2" class =" espaciotd1  margendatos top1 bot1 izq1 der1">SUBTOTAL Bs.</td>
                <td class ="margendatos textoizq  top1 bot1 izq1 der1"> {{number_format($factura->monto_total,2)}}</td>
            </tr>
            <tr>
                <td colspan ="4"></td>
                <td colspan = "2" class =" espaciotd1 margendatos top1 bot1 izq1 der1">DESCUENTO Bs.</td>
                <td class ="margendatos  espaciotd1  textoizq  top1 bot1 izq1 der1">0.00</td>
            </tr>
            <tr>
                <td colspan ="4"></td>
                <td colspan = "2" class ="espaciotd1  margendatos  top1 bot1 izq1 der1">TOTAL Bs.</td>
                <td class ="margendatos espaciotd1  textoizq  top1 bot1 izq1 der1">{{number_format($factura->monto_total,2)}}</td>
            </tr>
            <tr>
                <td colspan ="4"></td>
                <td colspan = "2" class =" espaciotd1  margendatos  top1 bot1 izq1 der1">MONTO GIFTCARD Bs.</td>
                <td class ="margendatos espaciotd1  textoizq  top1 bot1 izq1 der1">0.00</td>
            </tr>
            <tr>
                <td colspan ="4" class = ""></td>
                <td colspan = "2" class ="espaciotd1 fondo  margendatos datosnegrita top1 bot1 izq1 der1">MONTO A PAGAR Bs</td>
                <td class ="margendatos espaciotd1 fondo textoizq datosnegrita top1 bot1 izq1 der1">{{number_format($factura->monto_total,2)}}</td>
            </tr>
            <tr>
                <td colspan ="4" class = ""></td>
                <td colspan = "2" class =" espaciotd1 fondo margendatos datosnegrita top1 bot1 izq1 der1">IMPORTE CREDITO FISCAL Bs.</td>
                <td class ="margendatos espaciotd1 fondo textoizq datosnegrita top1 bot1 izq1 der1">{{number_format($factura->monto_total,2)}}</td>
            </tr>
        </table>
    </div>

    <div class = "espacio">
        <table class = "tabla3">
            
            <tr>
                <td class = "datosnegrita margendatos">Son : {{$literalb}} Bolivianos</td>
            </tr>
        </table>
    </div>

</body>
</html>