<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            font-size: 70%;
        }
        .divpagina{
            margin: 0px;
            /* border: solid 1px black; */
        }
        .divtitulo{
            width:50%; 
        }
        .t1col1{
             width:480px; 
        }
        .t1col2{
            width:230px; 
        }
        .titfac{
            font-size:1rem;
            font-weight: bold;
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
            margin-top:10px;
        }
        .t2col3{
            text-align: right;
        }
        .t2col1{
            width:190px;
        }
        .t2col4{
            width:200px;
            
        }
        .margenizq{
            padding-left: 15px;
        }
        .margendatos{
            padding-left:10px;
            padding-right:10px;
        }
        .textoizq{
            text-align: right;
        }
        .t3col1{
            width:70px;
        }
        .t3col2{
            width:80px;
        }
        .t3col56{
            width:100px;
        }
        .t3col3{
            width:220px;
        }
        .t3col4{
            width:110px;
        }
        table {
            border-collapse: collapse;
        }
        .tabla3{
            width:100%;
        }
        p{
            font-size:75% !important;
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
    </style>
</head>
<body>
    <div>
        <table>
            <tr>
                <td class = "t1col1">
                    <div class = "divtitulo">
                        <center>
                            {{$datos_empresa->razon_social}} <br>
                            CASA MATRIZ <br>
                            No Punto de Venta 0 <br>
                            AV. TARAPACA NRO. S/N<br>
                            ZONA BARRIO:VILLA BOLIVAR YKK <br>
                            Telefono: {{$datos_empresa->telefono}} <br>
                            {{$datos_empresa->municipio}}
                        </center>
                    </div>
                
                </td>
                <td class = "t1col2">
                    <table  style = "width: 100px;">
                        <tr>
                            <td>NIT</td>
                            <td> {{$datos_empresa->nit}}</td>
                        </tr>
                        <tr>
                            <td>FACTURA Nº</td>
                            <td>{{$factura->id}}</td>
                        </tr>
                        <tr>
                            <td>COD. AUTORIZACIÓN</td>
                            <td>     
                                <?php
                                    $str = "Hello world!";
                                    echo chunk_split($factura->cuf,16, "<br>");
                                ?>  
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan = "2" class = "titfac"> <div  class = "espacio"><center>FACTURA COMERCIAL DE EXPORTACIÓN</center> </div></td>
            </tr>

            <tr>
                <td colspan ="2" class = "titfac"><center>(COMERCIAL INVOCE)</center></td>
            </tr>

            <tr>
                <td colspan = "2"><center>(Sin Derecho a Crédito Fiscal)</center></td>
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
                <td class = "datosnegrita">Fecha(Date):</td>
                <td class = "margenizq">{{$factura->fecha_hora}}</td>
                <td class = "t2col3 datosnegrita">NIT/CI/CEX:</td>
                <td class = "margenizq">{{$factura->nro_documento}}</td>
            </tr>
            <tr>
                <td class = "datosnegrita">Nombre/Razón Social:</td>
                <td class = "margenizq">{{$factura->razon_social}}</td>
                <td class = "t2col3 datosnegrita">Cod. Cliente:</td>
                <td class = "margenizq"> {{$factura->codigo_cliente}}</td>
            </tr>
            <tr>
                <td class = "datosnegrita">INCOTERM:</td>
                <td class = "margenizq">{{$factura->incoterm}}</td>
                <td class = "t2col3 datosnegrita">Dirección Comprador:</td>
                <td class = "margenizq">{{$factura->direccion_cliente}}</td>
            </tr>
            <tr>
                <td class = "datosnegrita">Monenda de la Transacción Comercial:<br> (Comercial Transaction Currency) </td>
                <td class = "margenizq">{{$cliente->descripcion_moneda}}</td>
                <td class = "t2col3 datosnegrita">Puerto Destino:</td>
                <td class = "margenizq">{{$factura->puerto_destino}}</td>
            </tr>
            <tr>
                <td class = "datosnegrita"> </td>
                <td></td>
                <td class = "t2col3 datosnegrita">Lugar Destino:</td>
                <td class = "margenizq"> {{$factura->lugar_destino}}</td>
            </tr>
            <tr>
                <td class = "datosnegrita"> </td>
                <td></td>
                <td class = "t2col3 datosnegrita">Tipo de Cambio:</td>
                <td class = "margenizq"> {{$factura->tipo_cambio}}</td>
            </tr>
            
        </table>
    </div>
    
    <div class = "espacio tablapro">
        <table>
            <tr> 
                <th class = " t3col1"></th>
                <th class = " t3col2"></th>
                <th class = " t3col3"></th>
                <th class = " t3col4"></th>
                <th class = " t3col56"></th>
                <th class = " t3col56"></th>
            </tr>
            <tr class = "datosnegrita top izq der">
                <td> <center>NANDINA</center> </center></td>
                <td><center>CANTIDAD <br> (Quantity)</center></td>
                <td><center>DESCRIPCIÓN <br> (Description)</center></td>
                <td><center>UNIDAD DE MEDIDA  <br> (Unit of Measurement)</center></td>
                <td><center>PRECIO UNITARIO  <br> (Unit Value)</center></td>
                <td><center>SUBTOTAL</center></td>
            </tr>
            
            @foreach($detalle as $d)
                <tr class = " top izq der">
                    <td class = "margendatos">{{$d->nandina}}</td>
                    <td class = "margendatos">{{ number_format($d->cantidad,5) }}</td>
                    <td class = "margendatos">{{$d->descripcion}}</td>
                    <td class = "margendatos">{{$d->unidad_medida}}</td>
                    <td class = "margendatos textoizq">{{number_format($d->precio_unitario,5)}}</td>
                    <td class = "margendatos textoizq">{{number_format($d->subtotal,5)}}</td>
                </tr>
            @endforeach
            <tr class = " top izq der">
                <td colspan  ="5" class ="margendatos textoizq datosnegrita"> TOTAL DETALLE ({{$cliente->descripcion_moneda}}) (Total Detail)</td>
                <td  class ="margendatos textoizq datosnegrita">{{$factura->monto_total}}</td>
            </tr>
            <tr class = " top izq der">
                <td colspan  ="5" class ="margendatos datosnegrita"> INCOTERM Y alcance de Total detalle de la transacción ( INCOTERM and scope of the Total)</td>
                <td class ="margendatos textoizq datosnegrita"> {{$factura->incoterm_detalle}}</td>
            </tr>
            <tr class = "top">
                <td colspan = "6"  class ="margendatos datosnegrita"> <div class ="espacio">Desglose de Costos y Gastos Nacionales</div> </td>
            </tr>
            <tr>
                <td colspan ="6 "  class ="margendatos "> (National Cost and Expenses Detail)</td>
            </tr>
            <tr class = "top bot izq der">
                <td colspan = "5"  class ="margendatos datosnegrita">SUBTOTAL FOB</td>
                <td class ="margendatos textoizq datosnegrita"> {{$factura->monto_total}}</td>
            </tr>
            <tr>
                <td colspan = "6"  class ="margendatos datosnegrita"> <div class ="espacio">Desglose de Costos y Gastos Internacionales</div> </td>
            </tr>
            <tr>
                <td colspan ="6 "  class ="margendatos "> (International Cost and Expenses Detail)</td>
            </tr>
            <tr class = "top bot izq der">
                <td colspan = "5"  class ="margendatos datosnegrita ">TOTAL {{$factura->incoterm}}</td>
                <td class ="margendatos textoizq datosnegrita"> 0.00</td>
            </tr>
            <tr >
                <td colspan ="3"></td>
                <td colspan = "2" class ="margendatos datosnegrita top1 bot1 izq1 der1">SUBTOTAL ({{$cliente->descripcion_moneda}})</td>
                <td class ="margendatos textoizq datosnegrita top1 bot1 izq1 der1">{{$factura->monto_total}}</td>
            </tr>
            <tr>
                <td colspan ="3"></td>
                <td colspan = "2" class ="margendatos datosnegrita top1 bot1 izq1 der1">DESCUENTO ({{$cliente->descripcion_moneda}})</td>
                <td class ="margendatos textoizq datosnegrita top1 bot1 izq1 der1">0.00</td>
            </tr>
            <tr>
                <td colspan ="3"></td>
                <td colspan = "2" class ="margendatos datosnegrita top1 bot1 izq1 der1">TOTAL GENERAL ({{$cliente->descripcion_moneda}})</td>
                <td class ="margendatos textoizq datosnegrita top1 bot1 izq1 der1">{{$factura->monto_total}}</td>
            </tr>
            <tr>
                <td colspan ="3" class = ""></td>
                <td colspan = "2" class ="margendatos datosnegrita top1 bot1 izq1 der1">TOTAL GENERAL (BOLIVIANOS)</td>
                <td class ="margendatos textoizq datosnegrita top1 bot1 izq1 der1">{{$factura->monto_total_moneda}}</td>
            </tr>
        </table>
    </div>

    <div class = "espacio">
        <table class = "tabla3">
            <tr>
                <td class = "margendatos">Son : {{$literal}} ({{$cliente->descripcion_moneda}})</td>
            </tr>
            <tr>
                <td class = "margendatos">Son : {{$literal}} (BOLIVIANOS)</td>
            </tr>
            <tr>
                <td class = "margendatos datosnegrita"><div class ="espacio2"> Número y Descripción de Paquetes (Bultos)</div></td>
            </tr>
            <tr>
                <td class = "margendatos">(Number and Description of Boxes)</td>
            </tr>
            <tr>
                <td class = "margendatos top1 bot1 izq1 der1"> {{$factura->numero_descripcion_paquetes_bultos}}</td>
            </tr>
            <tr>
                <td class = "margendatos datosnegrita"><div class ="espacio2">Información Adicional</div></td>
            </tr>
            <tr>
                <td class = "margendatos">(Additional Informacion)</td>
            </tr>
            <tr>
                <td class = "margendatos top1 bot1 izq1 der1">{{$factura->informacion_adicional}}</td>
            </tr>
        </table>
    </div>

    <div class = "espacio">
        <table class = "tabla3">
            <tr>
                <th class ="t4col1"></th>
                <th></th>
            </tr>
            <tr>
                <td> </td>
                <td ROWSPAN=3 ><div><center><img  src="data:image/png;base64,'.{{$qr}}.'"></center></div> </td>
            </tr>
            <tr>
                <td><p><center> <h1> FACTURA ANULADA </h1> </center></p></td>
            </tr>
            <tr>
                <td> </td>
            </tr>
        </table>
    </div>
</body>
</html>