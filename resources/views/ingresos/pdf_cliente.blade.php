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
             
        }
        .medio{
            width:250px;
        }
        .t1col1{
             /* width:280px; */
             /* padding-top:20px;  */
        }
        .t1col2{
            width:280px; 
           
            margin-left:30px ; 
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
            width:65px;
        }
        .t3col2{
            width:70px;
        }
        .t3col3{
            width:70px;
        }
        
        .t3col4{
            width:80px;
        }
        .t3col5{
            width:180px !important;
        }
        .t3col6{
            width:100px !important;
        }
        .t3col7{
            width:100px !important;
        }
        
        table {
            border-collapse: collapse;
        }
        .tabla3{
            width:100%;
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
            font-size:0.8rem !important;
            font-weight: bold;
        }
        .encabezado_izq{
            padding: 10px  10px 10px 20px!important;
            margin:0px !important;   
            width: 150px !important;
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
        <table class = "tabla3">
            <tr>
                <td class = "t1col1">
                    <div class = "encabezado_izq">
                        <center>
                            <span class = "encabezado_datos">
                                {{$datos_empresa->razon_social}} <br>
                                Nro: {{$datos_empresa->nit}}
                            </span>
                        </center>
                    </div>
                </td>
            </tr>   
          
            <tr>
                <td class = "titfac"> <center><div >NOTA DE INGRESO Nro {{$ingreso->id}}  </div></center></td>
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
                <td class = "margenizq">{{$ingreso->fecha}}</td>
            </tr>
            <tr>
                <td class = "datosnegrita">Nombre/Razón Social: <br> </td>
                <td class = "margenizq">{{$ingreso->razon_social}}</td>
            </tr>           
            
        </table>
    </div>
    
    <div class = "espacio tablapro ">
        <table class = "tabla3">
        <tr> 
                <th class = " t3col0"> </th>
                <th class = " t3col1"></th>
                <th class = " t3col2"></th>
                <th class = " t3col3"></th>
                <th class = " t3col4"></th>
                <th class = " t3col5"></th>
                <th class = " t3col6"></th>
                <th class = " t3col7"></th>
               
            </tr>
            <tr class = "datosnegrita top izq der">
                <td class = "espaciotd fondo"><center>CODIGO PRODUCTO</center></td>
                <td class = "espaciotd fondo"><center>CANTIDAD PAQUETE</td>
                <td class = "espaciotd fondo"><center>CANTIDAD UNIDAD</td>
                <td class = "espaciotd fondo"><center>CANTIDAD TOTAL UNIDADES</td>
                <td class = "espaciotd fondo"><center>Unidad Medida </td>
                <td class = "espaciotd fondo"><center>DESCRIPCIÓN </center></td>
                <td class = "espaciotd fondo"><center>PRECIO PAQUETE </td>
                <td class = "espaciotd fondo"><center>SUBTOTAL</center></td> 
            
            </tr>
           
            @foreach($detalle as $d)
                <tr class = " top izq der">
                    <td class = "margendatos espaciotd">{{$d->id}}</td>
                    <td class = "margendatos espaciotd textoizq">{{ number_format($d->cantidad_paquete,2) }}</td>
                    <td class = "margendatos espaciotd textoizq">{{ number_format($d->cantidad_unidad,2) }}</td>
                    <td class = "margendatos espaciotd textoizq">{{ number_format((($d->cantidad_paquete * $d->unidad_por_paquete)+$d->cantidad_unidad),2) }}</td>
                    <td class = "margendatos espaciotd">{{$d->unidad_medida}}</td>  
                    <td class = "margendatos espaciotd">{{$d->descripcion_producto}}</td>    
                    <td class = "margendatos espaciotd textoizq">{{number_format($d->precio,2)}}</td>
                    <td class = "margendatos espaciotd textoizq">{{number_format($d->subtotal,2)}}</td>
                </tr>
            @endforeach
            <tr >
               
                <td colspan ="6" class = "top1"></td>
                <td class ="espaciotd1 fondo margendatos   top1 bot1 izq1 der1">TOTAL Bs</td>
                <td class ="margendatos espaciotd1 fondo textoizq  top1 bot1 izq1 der1">{{number_format($ingreso->monto_total,2)}}</td>
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