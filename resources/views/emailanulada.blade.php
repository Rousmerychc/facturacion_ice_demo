<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    body{
        font-family:sans-serif !important;
    }
    table {
            border-collapse: collapse;
        }
    table td{
        padding : 10px;
    }
    .texto{
        font-weight: bold;
    }   
</style>
<body>
    <center><h3>ANULACION DE FACTURA</h3></center>
    En este correo encontraras datos de la factura anulada. <br>
    Este correo se genera automaticamente al momento de anular la factura.
    <br>
    <center><h4> Factura Anulada</h4></center>
    <table border = "1px">
        <tr>
            <td class = "texto">NÚMERO DE FACTURA:</td>
           
            
                @if($factura->id_factura == 0)
                    <td>{{ $factura->nro_fac_manual }}</td>
                @else
                    <td>{{ $factura->id_factura }}</td>
                
            @endif
           
        </tr>
        <tr> 
            <td class = "texto">CÓDIGO DE AUTORIZACIÓN:</td>
            <td>{{ $factura->cuf }}</td>
        </tr>
        <tr>
            <td class = "texto">FECHA:</td>
            <td>{{ $factura->fecha_hora}}</td>
        </tr>
        
        <tr>
            <td class = "texto">MONTO DE IMPORTE Bs.:</td>
            <td>{{ number_format($factura->monto_total,2) }}</td>
        </tr>
        
    </table>
</body>
</html>