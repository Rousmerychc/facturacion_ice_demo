<html>
 <head>
  <title>Prueba de PHP</title>
 </head>
 <body>

 
    <?php

    /** Ejemplo de manejo de SOAP con libreria de PHP php-soap */

    $wsdl = "https://pilotosiatservicios.impuestos.gob.bo/v1/ServicioFacturacionComputarizada?wsdl";

    $token = 'VALOR_TOKEN';

    $opts = array(
        'http' => array(
            'header' => "Authorization: Token $token",
        )
    );

    $context = stream_context_create($opts);

    $client = new \SoapClient($wsdl, [
        'stream_context' => $context,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,

        // other options
    ]);


    $respons = $client->verificarComunicacion();
    var_dump( $respons->return);
    ?>
</body>
</html>