<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use SoapFault;
use App\Datos;
use DOMDocument;

use App\FacturaTasaCero;
use App\FacturaTasaCeroDetalle;

use App\ParametricaDocumentoTipoIdentidad;
use Carbon\Carbon;
use App\UnidadMedida;
use App\ParametricaTipoMetodoPago;
use App\ParametricasEventosSignificativos;

use App\Productos;
use App\Cuis;
use App\Cufd;
use App\Clientes;
use App\Sucursal;
use App\Cafc;
use App\CodigoPendiente;

use App\LeyendasFacturacion;
use App\ParametricaMotivoAnulacion;

use DataTables;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use Endroid\QrCode\QrCode;

use Illuminate\Support\Facades\Mail;
use PDF;

use App\Mail\FacturaEnviadaTasaCero;
use App\Mail\FacturaAnuladaTasaCero;

class FacturaTasaCeroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


     /**
      * $unidad = UnidadMedida::all();
      */
    //FUNCION PARA VALIDAR LOGIN
    public function __construct() {
        $this->middleware('auth');
        $datos_em =  Datos::first();
        $this->fac_codigo = $datos_em->fac_codigos; 
        $this->fac_sincronizacion = $datos_em->fac_sincronizacion;
        $this->fac_compra_venta = $datos_em->fac_tasa_cero;
        $this->fac_operaciones = $datos_em->fac_operaciones;
        $this->token = $datos_em->token;
        $this->codigo_ambiente= $datos_em->codigo_ambiente;
        $this->codigo_sistema = $datos_em->codigo_sistema;
        $this->nit = $datos_em->nit;
        $this->modalidad = $datos_em->modalidad;

    }
    //REGISTRAR PUNTO DE VENTA
    public function ResgistrarPuntoVenta()
    {
        $cuis = $this->cuis_usuario();
        //dd($cuis);

        $wsdl = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionOperaciones?wsdl";
        $token =  $this->token;

        $client = new \SoapClient($wsdl, [ 
            'stream_context' => stream_context_create([ 
                'http'=> [ 
                    'header' => "apikey: TokenApi $token",  
                ] 
            ]),

            'cache_wsdl' => WSDL_CACHE_NONE,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
        ]);

        $SolicitudRegistroPuntoVenta = array(
            'codigoAmbiente'        => $this->codigo_ambiente,
            'codigoModalidad'       => $this->modalidad,
            'codigoSistema'         => $this->codigo_sistema,
            'codigoSucursal'        => auth()->user()->id_sucursal,
            'codigoTipoPuntoVenta'  => 5,
            'cuis'                  => $cuis->codigo_cuis,
            // 'cuis'                  => '7E4B296A',
            'descripcion'           => 'Punto de Venta Cajero',
            'nit'                   => $this->nit,
            'nombrePuntoVenta'      => 'Punto de Venta 1',
        );
        $registrarPuntoVenta = $client->registroPuntoVenta(
            array(
                "SolicitudRegistroPuntoVenta" => $SolicitudRegistroPuntoVenta,
            )
        );
        dd($registrarPuntoVenta);

    }
    //FUNCION PARA OBTENER EL CUIS POR USUARIO
    public function cuis_usuario(){
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d H:i:s');
        $cuisbd = Cuis::where('fecha_hora','>',$fechahoyhora )
         ->where('id_sucursal','=',auth()->user()->id_sucursal)
         ->where('punto_venta','=',auth()->user()->punto_venta)
         ->orderby('id','DESC')
         ->first();
         return ($cuisbd);
    }
    public function verificar_nit(Request $request)     {
        $conexion = $this->prueba_veri_conec();
        if($conexion == 0){
            return response(json_encode(array('conexion'=>$conexion)),200)->header('Content-type','text/plain');
        }
        else{
            $cuis = $this->cuis_usuario();
            $nit=$request->get('dato');     
        
            $wsdl = $this->fac_codigo;
            $token =  $this->token;

            $client = new \SoapClient($wsdl, [ 
                'stream_context' => stream_context_create([ 
                    'http'=> [ 
                        'header' => "apikey: TokenApi $token",  
                    ] 
                ]),

                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            ]);

            $SolicitudVerificarNit = array(
                'codigoAmbiente'        => $this->codigo_ambiente,
                'codigoModalidad'       => $this->modalidad,
                'codigoSistema'         => $this->codigo_sistema,
                'codigoSucursal'        => auth()->user()->id_sucursal,
                // 'cuis'                  => '4218015C',
                'cuis'                  => $cuis->codigo_cuis,
                'nit'                   => $this->nit,
                'nitParaVerificacion'   => $nit,
            );
            $verificarNit = $client->verificarNit(
                array(
                    "SolicitudVerificarNit" => $SolicitudVerificarNit,
                )
            );

            //dd($verificarNit);
            $res = $verificarNit->{'RespuestaVerificarNit'};
            $res_nit = $res->{'mensajesList'};
            $prueba =  $res_nit->{'codigo'};
            $razon_social = Clientes::where('nro_documento','=',$nit)->first();
            return response(json_encode(array('prueba'=>$prueba, 'razon_social' =>$razon_social,'conexion'=>$conexion)),200)->header('Content-type','text/plain');
                    
        }
    }

    //funcion si verifica si es cliente para la vista
    public function es_cliente(Request $request)    {
        $nit=$request->get('dato');
        $razon_social = Clientes::where('nro_documento','=',$nit)->first();
        return response(json_encode(array('razon_social' =>$razon_social)),200)->header('Content-type','text/plain');
        
    }
    //FUNCION AJAX PARA EL INDEX
    public function ajaxfactura(){
        
        $data = FacturaTasaCero::where('id_sucursal','=',auth()->user()->id_sucursal)
        ->where('punto_venta','=',auth()->user()->punto_venta)
        ->where('nro_nota_venta', '=',0)
        
        ->orderBy('id', 'DESC')->get();
        return Datatables::of($data)
            ->addColumn('btn','facturacion_tasa_cero.actions')
            ->addColumn('pdf','facturacion_tasa_cero.pdf')
            ->rawColumns(['btn','pdf'])
            ->make(true);
        
    }
    //FUNCION ENVIO CORREO
    public function correo_anular($id){
        $msj = "hola";
        
        $factura = FacturaTasaCero::findOrFail($id);
       
       
        $cliente = Clientes::where('id', '=', $factura->codigo_cliente)->first();
        $email = $cliente->email;

        if($email == null){
            $cliente->email= "convar.fac@gmail.com";
        }
       
        Mail::to($cliente->email)->send(new FacturaAnuladaTasaCero($factura));
       
        return;
    }
    //FUNCION ENVIO CORREO
    public function correo($id,$tipo_fac){
        $msj = "hola";
        
        $factura = FacturaTasaCero::findOrFail($id);
        $cliente = Clientes::where('id', '=', $factura->codigo_cliente)->first();
        $email = $cliente->email;
        if($email == null){
            $cliente->email = "convar.fac@gmail.com";
        }
        
        //dd($cliente->email);
        
        Mail::to($cliente->email)->send(new FacturaEnviadaTasaCero($tipo_fac));
        
        return;
    }
    //FUNCION PARA GENERAR QR PARA EL PDF
    public function codigoQR($id){

        $datos_empresa = Datos::first();

        $factura = FacturaTasaCero::findOrFail($id);
        //dd($factura);

        if($factura->id_factura == 0){
            $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->nro_fac_manual.'&t=2';
            //$url = 'https://siat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->nro_fac_manual.'&t=2';
        }else{
            $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id_factura.'&t=2';
            //$url = 'https://siat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id_factura.'&t=2';
        }    
        $qrCode = new QrCode($url);//Creo una nueva instancia de la clase
        $qrCode->setSize(100);//Establece el tamaño del qr
        //header('Content-Type: '.$qrCode->getContentType());
        $image= $qrCode->writeString();//Salida en formato de texto 
        
        $imageData = base64_encode($image);//Codifico la imagen usando base64_encode
        
        //echo '<img src="data:image/png;base64,'.$imageData.'">';
        return $imageData;
    }
    //FUNCION QR PARA LA VISTA VISTA
    public function codigoQR_modal(Request $request){
        
        $id =  $request->get('dato');

        $datos_empresa = Datos::first();

        $factura = FacturaTasaCero::findOrFail($id);
        
        if($factura->id_factura == 0){
            $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->nro_fac_manual.'&t=2';
            //$url = 'https://siat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->nro_fac_manual.'&t=2';
        }else{
            $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id_factura.'&t=2';
            //$url = 'https://siat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id_factura.'&t=2';
        }   

                    
        $qrCode = new QrCode($url);//Creo una nueva instancia de la clase
        $qrCode->setSize(200);//Establece el tamaño del qr
        //header('Content-Type: '.$qrCode->getContentType());
        $image= $qrCode->writeString();//Salida en formato de texto 
        
        $imageData = base64_encode($image);//Codifico la imagen usando base64_encode
        
        //echo '<img src="data:image/png;base64,'.$imageData.'">';
        return response(json_encode(array('prueba'=>$imageData)),200)->header('Content-type','text/plain');
        
    }
    //FUNCION PAR AMANDAR AL CLIENTE
    public function pdf_en_servidor($id){
        $dato = auth()->user()->id;
        $datos_empresa = Datos::first();
        $factura = FacturaTasaCero::where('factura_tasa_cero.id','=',$id)
        ->join('sucursal','sucursal.nro_sucursal','=','factura_tasa_cero.id_sucursal')
        ->select('factura_tasa_cero.*','sucursal.municipio','sucursal.direccion','sucursal.telefono','sucursal.descripcion')
        ->first();

        //dd($factura);
    
        $leyenda2 =  LeyendasFacturacion::findOrFail($factura->id_leyenda);         
        
        $detalle = FacturaTasaCeroDetalle::where('id_tabla_factura','=',$factura->id)
        ->join('productos','productos.id', '=','codigo_producto_empresa')
        ->select('factura_detalle_tasa_cero.*','productos.id')
        ->get();
        //dd($detalle);

        $qr = $this->codigoQR($id);
     
       
        //PARA LA TERCERA LEYENDA - FACTURA EMITIDA EN LINEA O FUERA DE LINEA
        if($factura->tipo_emision_n == 1){
            $leyenda3 = "Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación en línea";
        }else{
            $leyenda3 = "Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web www.impuestos.gob.bo.";
        }

        //PARA LITERAL EN MONEDA BOLIVIANOS
        $formatterb = new NumeroALetras();
        $reb= (int) ($factura->monto_total / 1000);
        $literalb= $formatterb->toInvoice($factura->monto_total, 2,'');
        if($reb==1){
            $literalb = 'UN '.$literalb;
        }
      

        
        $data =['factura'=>$factura, 'detalle'=>$detalle,  'datos_empresa'=>$datos_empresa ,
                 'qr'=>$qr , 'leyenda2'=> $leyenda2, 'leyenda3'=>$leyenda3, 'literalb'=>$literalb,
                ];
        
      
        
            $pdf = PDF::loadView('facturacion_tasa_cero.pdf_cliente',$data);
     
        $pdf->setPaper("letter", "portrait");
        $pdf->save(storage_path('/facturasT/factura'.$dato.'.pdf'));
        //return $pdf->stream('Factura.pdf');
        
    }
   
    //FUNCION PARA GENERAR PDF PARA LOS CLIENTES
    public function pdf_clientes($id){
        
        $datos_empresa = Datos::first();
        $factura = FacturaTasaCero::where('factura_tasa_cero.id','=',$id)
        ->join('sucursal','sucursal.nro_sucursal','=','factura_tasa_cero.id_sucursal')
        ->select('factura_tasa_cero.*','sucursal.municipio','sucursal.direccion','sucursal.telefono','sucursal.descripcion')
        ->first();
        //dd($factura);

        //dd($factura);
    
        $leyenda2 =  LeyendasFacturacion::findOrFail($factura->id_leyenda);         
        
        $detalle = FacturaTasaCeroDetalle::where('id_tabla_factura','=',$factura->id)
        ->join('productos','productos.id', '=','codigo_producto_empresa')
        ->select('factura_detalle_tasa_cero.*','productos.id')
        ->get();
       // dd($detalle);

        $qr = $this->codigoQR($id);
     
       
        //PARA LA TERCERA LEYENDA - FACTURA EMITIDA EN LINEA O FUERA DE LINEA
        if($factura->tipo_emision_n == 1){
            $leyenda3 = "Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación en línea";
        }else{
            $leyenda3 = "Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web www.impuestos.gob.bo.";
        }

        //PARA LITERAL EN MONEDA BOLIVIANOS
        $formatterb = new NumeroALetras();
        $reb= (int) ($factura->monto_total / 1000);
        $literalb= $formatterb->toInvoice($factura->monto_total, 2,'');
        if($reb==1){
            $literalb = 'UN '.$literalb;
        }
      
        $data =['factura'=>$factura, 'detalle'=>$detalle,  'datos_empresa'=>$datos_empresa ,
                 'qr'=>$qr , 'leyenda2'=> $leyenda2, 'leyenda3'=>$leyenda3, 'literalb'=>$literalb,];
            $pdf = PDF::loadView('facturacion_tasa_cero.pdf_cliente',$data); 
        //return view('facturacion.pdf_vista',$data);
        $pdf->setPaper("letter", "portrait");
        return $pdf->stream('FacturaTasaCero.pdf');
    }
    //para verificar conexion
    public function prueba_veri_conec(){
         //set_time_limit(58);
        $serv = $this->fac_codigo;
        //$serv = "https://orbol.adsc-sistemas.com/";
        $a = @get_headers($serv);
        if (is_array($a)) {
            return 1; //echo 'ON';
        } else{
            return  0;
        }
        
    }
    
    //FUNCION PARA VERIFICAR COMUNICACION
    public function verificarComunucacion(){
        
        $wsdl =$this->fac_codigo;
        $token =  $this->token;
        //set_time_limit(5);
        try {
            $client = new \SoapClient($wsdl, [ 
  
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
                'exceptions' => true,
                'connection_timeout'=>5,
                'stream_context' => stream_context_create([ 
                    'http'=> [ 
                        'header' => "apikey: TokenApi $token",  
                    ]
                ]),
            ]);
            
            $comunicacion = $client->verificarComunicacion();
            $res = $comunicacion->{'RespuestaComunicacion'};
            $res1 = $res->{'mensajesList'};
            $respuesta = $res1->{'descripcion'};
            //dd($respuesta);
             return($respuesta);
        } catch(SoapFault $e) {
            //dd('entro');
            return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: ');
            //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
        }
           
    }
    //FUNCION CUIS UNA VEZ AL AÑO
    public function cuis(){

        // $conexion = $this->prueba_veri_conec();
        // $dato = auth()->user()->id;

        // if($conexion == 0){
        //     return redirect('facturacion')->with('status', 'NO HAY SERVICIO DE IMPUESTOS INTERNOS');
        // }

        $fechahoyhora=Carbon::now(-4)->format('Y-m-d H:i:s');

        //Verificando si existe cuis o crear uno nuevo
        $cuisbd = Cuis::where('fecha_hora','>',$fechahoyhora )
        ->where('id_sucursal','=',auth()->user()->id_sucursal)
        ->where('punto_venta','=',auth()->user()->punto_venta)
        ->orderby('id','DESC')
        ->get();
        
 
        if($cuisbd->isEmpty()){
            
            try {
                $wsdl =$this->fac_codigo;
                $token =  $this->token;
                
                $client = new \SoapClient($wsdl, [ 
                    'stream_context' => stream_context_create([ 
                    'http'=> [ 
                        'header' => "apikey: TokenApi $token",  
                    ] 
                ]),
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
                ]);
    
            //  Este fragmento de código sirve para solicitar el CUIS, ya que ya lo tenemos, no es necesario hacerlo correr.
            //  Extructura que se manda para crear cuis
            //dd($wsdl,$token,$this->codigo_ambiente,$this->modalidad,$this->codigo_sistema,$this->nit);
                $SolicitudCuis = array(
                    'codigoAmbiente'   => $this->codigo_ambiente,
                    'codigoModalidad'  => $this->modalidad,
                    'codigoPuntoVenta' => auth()->user()->punto_venta,//por usuario
                    'codigoSistema'    => $this->codigo_sistema,
                    'codigoSucursal'   => auth()->user()->id_sucursal,//por usuario
                    'nit'              => $this->nit,
                ); 
    
                $result = $client->cuis(
                    array(
                        "SolicitudCuis" => $SolicitudCuis,
                    )
                );
               //dd($result);
                $respCuis = $result->{'RespuestaCuis'};
                $cuis = $respCuis->{'codigo'};
    
                
                 // datos para llenar en BD
                $cuisbd = new Cuis;
                $cuisbd->codigo_cuis = $cuis;
                $cuisbd->fecha_vigencia = $respCuis->{'fechaVigencia'};
                
                //obteniendo fecha y hora de vigencia
                $fechavigencia=substr($respCuis->{'fechaVigencia'}, 0, 10); 
                $horavigencia = substr($respCuis->{'fechaVigencia'}, 11, 8); 
               
                $cuisbd->fecha_hora =$fechavigencia.' '.$horavigencia;
                $cuisbd->id_sucursal = auth()->user()->id_sucursal;
               
                $cuisbd->punto_venta = auth()->user()->punto_venta;
                $cuisbd->id_usuario =auth()->user()->id;
                $cuisbd->save();
            } catch(SoapFault $e) {
                return $e;    
            }

                      
         }
         else{
            $cuisbd = Cuis::where('fecha_hora','>',$fechahoyhora )
            ->where('id_sucursal','=',auth()->user()->id_sucursal)
            ->where('punto_venta','=',auth()->user()->punto_venta)
            ->orderby('id','DESC')
            ->first();
         }
         
        return ($cuisbd->codigo_cuis);
    }
    // FUNCION CUFD TODOS LOS DIAS -- EN CONTINGENCIA PEDIR OTRO
    public function cufd(){ 

       $conexion = $this->prueba_veri_conec();
       $dato = auth()->user()->id;
       $cuis = $this->cuis_usuario();
      

        // if($conexion == 0){
        //     return redirect('facturacion')->with('status', 'NO HAY SERVICIO DE IMPUESTOS INTERNOS');
        // }
        
        // try {
             $wsdl =$this->fac_codigo;
            $token =  $this->token;

            $client = new \SoapClient($wsdl, [ 
            'stream_context' => stream_context_create([ 
                'http'=> [ 
                    'header' => "apikey: TokenApi $token",  
                ] 
            ]),
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            ]);
            $SolicitudCufd = array(
                'codigoAmbiente'   => $this->codigo_ambiente,
                'codigoModalidad'  => $this->modalidad,
                'codigoPuntoVenta' => auth()->user()->punto_venta, // por suario
                'codigoSistema'    => $this->codigo_sistema,
                'codigoSucursal'   => auth()->user()->id_sucursal, // por usuario 
                'cuis'             =>  $cuis->codigo_cuis, //deacuerdo al usario sacar de tabla cuis
                // 'cuis'             => 'EED58C49',
                'nit'              => $this->nit,
            );

            $objCufd = $client->cufd(
                array(
                    "SolicitudCufd" => $SolicitudCufd,
                )
            );
        //  }
        //  catch(SoapFault $e) {
        //     return $e;    
        // }
        //dd($objCufd);
        $respCufd = $objCufd->{'RespuestaCufd'};
        $cufd = $respCufd->{'codigo'};
        $codigoControl = $respCufd->{'codigoControl'};
        $fechavigencia = $respCufd->{'fechaVigencia'};
        
        
        // file_put_contents("cufd.txt", $cufd);
        // file_put_contents("codigoControl.txt", $codigoControl);

        $cufdbd = new Cufd;
        $cufdbd->codigo_cufd = $cufd;
        $cufdbd->codigo_control = $codigoControl;
        $cufdbd->fecha_vigencia = $fechavigencia;
        
        //obteniendo fecha y hora de vigencia
        $fechavigencia1=substr($fechavigencia, 0, 10); 
        $horavigencia = substr($fechavigencia, 11, 8); 
        $cufdbd->fecha_hora = $fechavigencia1.' '.$horavigencia;
        $cufdbd->id_sucursal = auth()->user()->id_sucursal;
        $cufdbd->punto_venta = auth()->user()->punto_venta;
        $cufdbd->id_usuario =auth()->user()->id;
        $cufdbd->fecha = $fechavigencia1;
        $cufdbd->save();
        // dd($respCufd, $cufdbd);
                    
        return ($cufdbd);

    }
    //FUNCION FEHCA HORA FORMATO IMPUESTOS, CADA QUE SE IMITE FACTURA
    public function fechaHora($bool) {
        $conexion = $this->prueba_veri_conec();
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d H:i:s');
        $cuis = $this->cuis_usuario();
        // dd($cuis);
        if($conexion == 0){
            return redirect('facturacion')->with('status', 'NO HAY SERVICIO DE IMPUESTOS INTERNOS');
        }
        $wsdl = $this->fac_sincronizacion;
        $token =  $this->token;

        try {
                $client = new \SoapClient($wsdl, [ 
                    'stream_context' => stream_context_create([ 
                        'http'=> [ 
                            'header' => "apikey: TokenApi $token",  
                        ] 
                    ]),
        
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
                ]);
        
                $SolicitudSincronizacion = array(
                    'codigoAmbiente'   => $this->codigo_ambiente,
                    'codigoPuntoVenta' => auth()->user()->punto_venta, // por usuario
                    'codigoSistema'    => $this->codigo_sistema,
                    'codigoSucursal'   => auth()->user()->id_sucursal,
                    'cuis'             =>  $cuis->codigo_cuis, // por sucuarsal y usuario
                    // 'cuis'             => 'EED58C49',
                    'nit'              => $this->nit,
                );
        
                $fecha = $client->sincronizarFechaHora(
                    array(
                        "SolicitudSincronizacion" => $SolicitudSincronizacion,
                    )
                );
        
                $respFecHora = $fecha->{'RespuestaFechaHora'};
                $fechaHora = $respFecHora->{'fechaHora'};
        
                //$fecha_ant = file_get_contents("fecha.txt");
        
                if($bool)
                {
                    //file_put_contents("fecha_ant.txt", $fecha_ant);
                    echo("sigue entrando");
                }
            
                //$fecha_ant = file_get_contents("fecha_ant.txt");
                $arrayFecha[0] = $fechaHora;
                //dd($arrayFecha[0]);
                //$arrayFecha[1] = $fecha_ant;
           
            return ($arrayFecha[0]);
        } catch (SoapFault $e) {
            //dd('holi');
            return  $e;
        }
        

    }
    //FUNCION PARA ANULAR FACTURA
    public function anular_fac(Request $request){

        // $conexion = $this->prueba_veri_conec();

        // if($conexion == 0){
        //     return redirect('facturacion')->with('status', 'NO HAY SERVICIO DE IMPUESTOS INTERNOS');
        // }

        try {
            $fecha_hora = Carbon::now(-4)->format('Y-m-d H:i:s');
            //cufd solicitud una vez al dia o en caso de contigencia al finalizar la misma.
            $cufdbd = Cufd::where('fecha_hora','>',$fecha_hora)
            ->where('id_sucursal','=',auth()->user()->id_sucursal)
            ->where('punto_venta','=',auth()->user()->punto_venta)
            ->orderby('id','DESC')
            ->get();
    
            if($cufdbd->isEmpty() == true){
                 $cufd = $this->cufd();
            }else{
                $cufd = Cufd::where('fecha_hora','>',$fecha_hora)
                ->where('id_sucursal','=',auth()->user()->id_sucursal)
                ->where('punto_venta','=',auth()->user()->punto_venta)
                ->orderby('id','DESC')
                ->first();
            }
    
            $datos_empresa = Datos::first();
            $factura = FacturaTasaCero::findOrFail($request->{'id_factura'});
    
            $motivo_anulacion = (int)$request->{'codigo_motivo_anulacion'};
    
            //dd($factura,$motivo_anulacion);
            $wsdl = $this->fac_compra_venta;
            $token =  $this->token;
    
            $client1 = new \SoapClient($wsdl, [ 
                'stream_context' => stream_context_create([ 
                    'http'=> [ 
                        'header' => "apikey: TokenApi $token",  
                    ] 
                ]),
    
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            ]);
    
            $cuis=$this->cuis_usuario();
            $solicitudServicioAnulacionFactura = array(
                'codigoAmbiente'        => $this->codigo_ambiente,
                'codigoDocumentoSector' => 8,
                'codigoEmision'         => 1, //$factura->tipo_emision_n -- siempre sera 1
                'codigoModalidad'       => $this->modalidad,
                'codigoPuntoVenta'      => auth()->user()->punto_venta, //por usuario
                'codigoSistema'         => $this->codigo_sistema,
                'codigoSucursal'        =>  auth()->user()->id_sucursal, // por usuario
                'cufd'                  => $cufd->codigo_cufd,
                'cuis'                  => $cuis->codigo_cuis, //por usuario
                // 'cuis'                  => 'EED58C49',
                'nit'                   => $this->nit,
                'tipoFacturaDocumento'  => 2,
                'codigoMotivo'          => $motivo_anulacion,
                'cuf'                   => $factura->cuf,
            );
    
            $anularFac = $client1->anulacionFactura(
                array(
                    "SolicitudServicioAnulacionFactura" => $solicitudServicioAnulacionFactura,
                )
            );
            //dd($anularFac);
            $anularFacRes = $anularFac->{'RespuestaServicioFacturacion'};
            $codigores = $anularFacRes->{'codigoEstado'};
            $des ="";
            
            if($codigores == 905){
                $factura->codigo_motivo_anulacion = $motivo_anulacion;
                $factura->estado = 1;
                $factura->save();
                //$pdfanu = $this->pdf_anular($factura->id);
                $correo = $this->correo_anular($factura->id);
            }
            else{
                $mensajesList = $anularFacRes->{'mensajesList'};
                $des = $mensajesList->{'descripcion'};
            }
            
    
            //dd($anularFacRes);
            $descrip = $anularFacRes->{'codigoDescripcion'}.', '.$des;
            return redirect('facturacionT')->with('status', $descrip);
        }   catch(SoapFault $e) {
           
            return redirect('facturacionT')->with('status', 'OCURRIO UN INCONVENIENTE ANULACION DE FACTURA -  '. $e);
            //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
        }
       
       // dd($anularFac);
    }
   
    //funcion para borrar archibos de envio por paquete
    function pruebaborrado($tipo_fac){
        // Borramos los xml que se crearon y tambien el archivo .tar.gz
        $dato = auth()->user()->id;
        $i = 0;
        for($i=1;$i<=500;$i++){
            $dir = storage_path('/facturasT/'.$tipo_fac.'F'.$dato.$i.'.xml');
            if(file_exists($dir)){
                unlink($dir);
            }
            else{
                break;
            }
        }

        $dir = storage_path('/facturasT/miprueba'.$dato.'.tar');
        $dirgz = storage_path('/facturasT/miprueba'.$dato.'.tar.gz');
        var_dump($dir,$dirgz);
        if(file_exists($dir) && file_exists($dirgz) ){
            unlink($dir);
            unlink($dirgz);
            var_dump("se borraron bien");        
        }
        else{
            var_dump("no se borraron hagalo manualmente");
        }     
    }
    //FUNCION BORRADO DE TAR TAR.GZ
    public function borradoarchivos(){
        $dato = auth()->user()->id;
        $dir = storage_path('/facturasT/miprueba'.$dato.'.tar');
        $dirgz = storage_path('/facturasT/miprueba'.$dato.'.tar.gz');
        var_dump($dir,$dirgz);
        if(file_exists($dir) && file_exists($dirgz) ){
            unlink($dir);
            unlink($dirgz);
            var_dump("se borraron bien");        
        }
        else{
            var_dump("no se borraron hagalo manualmente");
        }   
    }
    //FUNCION GENERAR CUF CODIGO UNICO DE FACTURACION
    public function cuf($fecha_hora3,$tipoEmisionN,$cufd_codigoControl,$nroFactura){
        //ESTOS DATOS NO CAMBIAN
         //DATOS DE EMPRESA sacado de BD tabla datos_empresa
        $datos_empresa = Datos::first();
        $nitEmisor =$datos_empresa->nit; 

       //DATOS SACADOS DE POR EL USUARIO
        $codPtoVenta =  auth()->user()->punto_venta;
        $codSucursal =  auth()->user()->id_sucursal;
 
        // convertimos a cadena y completamos la longitud de cada variable según anexo técnico
        $nit = "$nitEmisor";
        $fecha = preg_replace("/[^0-9]/", "", $fecha_hora3);
        $sucursal = "$codSucursal";
        $modalidad = "2";
        $tipoEmision = "$tipoEmisionN";
        $tipoFac = "2";
        $tipoDocSector = "8";
        $numFac = $nroFactura;
        $ptoVenta = "$codPtoVenta";
        $cero = "0";

        $sw1 = 0; $sw2 = 0; $sw3 = 0; $sw4 = 0; $sw5 = 0;

        while($sw1==0 || $sw2==0 || $sw3==0 || $sw4==0 || $sw5==0){
             if(strlen($nit)<13){ $nit = $cero.$nit;} 
             else{ $sw1 = 1; }
             if(strlen($sucursal)<4){ $sucursal = $cero.$sucursal; } 
             else{ $sw2 = 1; }
             if(strlen($tipoDocSector)<2){ $tipoDocSector = $cero.$tipoDocSector; } 
             else{ $sw3 = 1; }
             if(strlen($numFac)<10){ $numFac = $cero.$numFac; } 
             else{ $sw4 = 1; }
             if(strlen($ptoVenta)<4){ $ptoVenta = $cero.$ptoVenta; } 
             else{ $sw5 = 1; }
        }

        //concatenamos las variables para formar una sola cadena.
        $campos = $nit.$fecha.$sucursal.$modalidad.$tipoEmision.$tipoFac.$tipoDocSector.$numFac.$ptoVenta;
        // aplicamos el modulo 11 y concatenamos el resultado para llegar a una longitud de 54 en $campos
        $bool = false;
        $sum = 0;
        $factor = 2;
        $i=strlen($campos)-1;
        for( $i = strlen($campos)-1; $i >= 0; $i--){
            $sum += intval($factor*intval($campos[$i]));
            $factor++;
            if ($factor>9)
            {
            $factor = 2;
            }
        }
        if($bool){
            $dv = (($sum*10)%11)%10;
        } else{
            $dv = $sum%11;
        }
        if($dv==10){
            $campos.="1";
        }
        if($dv==11){
            $campos.="0";
        }
        if($dv<10){
            $campos.=$dv;
        }
        //convertimos el resultado a base 16
        $prueba = bcadd($campos, '0');    
        $base16 = (strtoupper(gmp_strval($prueba,16)));   
        $cuf = $base16.$cufd_codigoControl;    
        return ($cuf);

    }
    //FUNCION PARA EMPAQUETAR Y MANDAR FACTURAS FUERA DE LINEA
    public function emisionFueraLinea(){
        //dd('entro a emision fuera linea');
        // $conexion = $this->prueba_veri_conec();
        // if($conexion == 0){
        //     return redirect('facturacion')->with('status', 'NO HAY CONEXION A SERVICIO DE IMPUESTOS INTERNOS');
        // }
        //dd('hola');

        $dato = auth()->user()->id;
        $id_sucu = auth()->user()->id_sucursal;
        $punto_v = auth()->user()->punto_venta;
        
        $datos_empresa = Datos::first();

        $factura = FacturaTasaCero::where('fuera_linea','=',1)
        ->where('id_sucursal','=',$id_sucu)
        ->where('punto_venta','=',$punto_v)
        ->get();

        //
        
        if($factura->isEmpty()){
            return redirect('facturacionT')->with('status', 'NO HAY PAQUETES PARA ENVIAR');
        }else{
            $factura = FacturaTasaCero::where('fuera_linea','=',1)
            ->where('id_sucursal','=',$id_sucu)
            ->where('punto_venta','=',$punto_v)
            ->select('id','hora_impuestos','cufd')
            ->orderby('id')
            ->first();

            //dd($factura);
            $horaIni = $factura->hora_impuestos;

            $num_fac1 = DB::select(DB::raw('SELECT COUNT(*) as num, cufd FROM factura_tasa_cero 
            WHERE factura_tasa_cero.fuera_linea = 1 AND factura_tasa_cero.id_sucursal = ? AND factura_tasa_cero.punto_venta = ? GROUP BY cufd'),[$id_sucu,$punto_v]);
            $num_fac=$num_fac1[0]->{'num'};
            $cufd_ant = $num_fac1[0]->{'cufd'};
            //dd($num_fac1);
            $cuis = $this->cuis_usuario();
            $fecha_hora3 = $this->fechaHora(true);
            //dd($fecha_hora3);
            //--------------------------------------------------------------------------
            //comprimir el paquete de facturas en el archivo .tar.gz para el envío por paquetes
            $dir = storage_path('/facturasT/miprueba'.$dato.'.tar');
            if(file_exists($dir) == false){            
                
                $p = new \PharData($dir);
                $i = 1;
                for( $i = 1; $i <= 500; $i++){
                    $origen = storage_path('/facturasT/lineaF'.$dato.$i.'.xml');
                    if(file_exists($origen))
                    {
                        // echo ("existe $origen");
                        $factura = file_get_contents($origen);
                        $p['factura'.$dato.$i.'.xml'] = $factura;
                    }
                    else{
                        break;
                    }
                }
                $p1 = $p->compress(\Phar::GZ); // copia a /ruta/a/mi.tar.gz
                // $p2 = $p->compress(\Phar::BZ2); // copia a /ruta/a/mi.tar.bz2
                // $p3 = $p2->compress(Phar::NONE); // excepción: /ruta/a/mi.tar ya existe
                unset($p);
                unset($p1);

                $archivoArray = file(storage_path('/facturasT/miprueba'.$dato.'.tar.gz'));
                $hash = hash_file("sha256",storage_path('/facturasT/miprueba'.$dato.'.tar.gz'), $raw_output = false);
                $archivo = implode($archivoArray);
            }

            
            //nuevo
            $cufd1 = $this->cufd();
            if(is_soap_fault($cufd1)){
                $this->borradoarchivos();
                return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: NO SE PUDO SOLISITAR UN NUEVO CUFD PARA EL ENVIO DE PAQUETE: '.$cufd1 );
            }
            $cufd = $cufd1->codigo_cufd;
            //dd($cufd1);

            try {
                
            $wsdlOperaciones = $this->fac_operaciones;
            
            $token =  $this->token;
            $wsdl = $this->fac_compra_venta;

            $clientOperaciones = new \SoapClient($wsdlOperaciones, [ 
                'stream_context' => stream_context_create([ 
                    'http'=> [ 
                        'header' => "apikey: TokenApi $token",
                    ] 
                ]),

                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            ]);
            // dd($clientOperaciones->__getTypes());
            var_dump($cufd, $cufd_ant);

            $solicitudEventoSignificativo = array(
                'codigoAmbiente'        => $this->codigo_ambiente,
                'codigoMotivoEvento'    => 2,
                'codigoPuntoVenta'      => $punto_v,
                'codigoSistema'         => $this->codigo_sistema,
                'codigoSucursal'        => $id_sucu,
                'cufd'                  => $cufd,
                'cufdEvento'            => $cufd_ant,
                'cuis'                  => $cuis->codigo_cuis,
                // 'cuis'                  => 'EED58C49',
                'descripcion'           => 'INACCESIBILIDAD AL SERVICIO WEB DE LA ADMINISTRACIÓN TRIBUTARIA',
                'fechaHoraFinEvento'    => $fecha_hora3,
                'fechaHoraInicioEvento' => $horaIni,
                'nit'                   => $this->nit,
            );

            $objEventoSignificativo = $clientOperaciones->registroEventoSignificativo(
                array(
                    "SolicitudEventoSignificativo" => $solicitudEventoSignificativo,
                )
            );
            
            var_dump($objEventoSignificativo,$horaIni,$fecha_hora3);
            //dd($objEventoSignificativo,$horaIni,$fecha_hora3);
            $respEventos = $objEventoSignificativo->{'RespuestaListaEventos'};
            $evento = $respEventos->{'codigoRecepcionEventoSignificativo'};

            } catch(SoapFault $e) {
                //dd('entro');
                $this->borradoarchivos();
                return redirect('facturacionT')->with('status', 'OCURRIO UN INCONVENIENTE EN EVENTO SIGNIFICATIVOs -  '. $e);
                //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            }
            catch(exception $e) {
                //dd('entro');
               $this->borradoarchivos();
                return redirect('facturacionT')->with('status', 'OCURRIO UN INCONVENIENTE EN EVENTO SIGNIFICATIVOS -  '. $e);
                //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            }
            
            // ------------------------------------------------------------------------
            // Envío de factura por paquetes

            try {
                $client = new \SoapClient($wsdl, [ 
                    'stream_context' => stream_context_create([ 
                        'http'=> [ 
                            'header' => "apikey: TokenApi $token",  
                        ] 
                    ]),
    
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
                ]);
    
                $solicitudRecepcionFactura = array(
                    'codigoAmbiente'        => $this->codigo_ambiente,
                    'codigoDocumentoSector' => 8,
                    'codigoEmision'         => 2,
                    'codigoModalidad'       => $this->modalidad,
                    'codigoPuntoVenta'      => $punto_v,
                    'codigoSistema'         => $this->codigo_sistema,
                    'codigoSucursal'        => $id_sucu,
                    'cufd'                  => $cufd,
                    'cuis'                  => $cuis->codigo_cuis,
                    // 'cuis'                  => 'EED58C49',
                    'nit'                   => $this->nit,
                    'tipoFacturaDocumento'  => 2,
                    'archivo'               => $archivo,
                    'fechaEnvio'            => $fecha_hora3,
                    'hashArchivo'           => $hash,
                    'cafc'                  => null,
                    'cantidadFacturas'      => $num_fac,
                    'codigoEvento'          => $evento,
                );
                $recepPaq = $client->recepcionPaqueteFactura(
                    array(
                        "SolicitudServicioRecepcionPaquete" => $solicitudRecepcionFactura,
                    )
                );
                //dd($recepPaq);
                $servicioPaq = $recepPaq->{'RespuestaServicioFacturacion'};
                $codDescrip = $servicioPaq->{'codigoDescripcion'};
                $codEstado = $servicioPaq->{'codigoEstado'};
                $codRecep = $servicioPaq->{'codigoRecepcion'};
            //dd($recepPaq);
                // --------------------------------------------------------------------------
                // Validar la recepcion del paquete de facturas
    
                $SolicitudServicioValidacionRecepcionPaquete = array(
                    'codigoAmbiente'        => $this->codigo_ambiente,
                    'codigoDocumentoSector' => 8,
                    'codigoEmision'         => 2,
                    'codigoModalidad'       => $this->modalidad,
                    'codigoPuntoVenta'      => $punto_v,
                    'codigoSistema'         => $this->codigo_sistema,
                    'codigoSucursal'        => $id_sucu,
                    'cufd'                  => $cufd,
                    'cuis'                  => $cuis->codigo_cuis,
                    // 'cuis'                  => 'EED58C49',
                    'nit'                   => $this->nit,
                    'tipoFacturaDocumento'  => 2,
                    'codigoRecepcion'       => $codRecep, 
                );
                $validarPaq = $client->validacionRecepcionPaqueteFactura(
                    array(
                        "SolicitudServicioValidacionRecepcionPaquete" => $SolicitudServicioValidacionRecepcionPaquete,
                    )
                );
                //dd($validarPaq);
    
                // var_dump("este es el resultado");
                $servicioPaq = $validarPaq->{'RespuestaServicioFacturacion'};
                $codDescrip = $servicioPaq->{'codigoDescripcion'};
                $codEstado = $servicioPaq->{'codigoEstado'};
                $codRecep = $servicioPaq->{'codigoRecepcion'};
                
            } catch(SoapFault $e) {
                //dd('entro');
                $this->borradoarchivos();
                return redirect('facturacionT')->with('status', 'OCURRIO UN INCONVENIENTE EN ENVIARPAQUETE Y/O VALIDAR EL PAQUETE -  '. $e);
                //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            }
            catch(exception $e) {
                //dd('entro');
            $this->borradoarchivos();
                return redirect('facturacionT')->with('status', 'OCURRIO UN INCONVENIENTE EN ENVIARPAQUETE Y/O VALIDAR EL PAQUETE -  '. $e);
                //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            }
            
            // ------------------------------------------------------------------------

            // dd($servicioPaq);

            if($codEstado == 908 || $codEstado == 901){
                $fac = FacturaTasaCero::where('fuera_linea','=',1)
                ->where('id_sucursal','=',$id_sucu)
                ->where('punto_venta','=',$punto_v)
                ->get();
                foreach($fac as $fac){
                    $fac->fuera_linea = 0;
                    $fac->save();
                }
                $tipo_fac = "linea";
                $bora = $this->pruebaborrado($tipo_fac);
                if($codEstado == 901){
                    sleep(3);
                    $pendiente = $this->pendiente($codRecep);
                    $codDescrip = $pendiente;
                }
                return redirect('facturacionT')->with('status', 'ENVIO DE PAQUETE '.$codDescrip);
            }else{
                return redirect('facturacionT')->with('status', 'ERROR EN ENVIO DE PAQUETE '.$codDescrip);
            }
            //dd($validarPaq);    
        }
    }
    // //FUNCION PARA ENVIAR PAQUETES DE FACTURAS MANUALES
            // public function emisionManuales(Request $request){

            //     // $conexion = $this->prueba_veri_conec();
            //     // if($conexion == 0){
            //     //     return redirect('facturacion')->with('status', 'NO HAY CONEXION A SERVICIO DE IMPUESTOS INTERNOS');
            //     // }
            //     $evento_significado = $request->get('evento_significado');
            
            //     $descrip_evento = ParametricasEventosSignificativos::findOrFail($evento_significado);
            //     //dd($evento_significado,$descrip_evento);
            //     $dato = auth()->user()->id;
            //     $id_sucu = auth()->user()->id_sucursal;
            //     $punto_v = auth()->user()->punto_venta;
                
            //     $datos_empresa = Datos::first();

            //     $factura = Facturacion::where('fac_manual','=',1)
            //     ->where('id_sucursal','=',$id_sucu)
            //     ->where('punto_venta','=',$punto_v)
            //     ->get();
                
            //     if($factura->isEmpty()){
            //         return redirect('facturacion')->with('status', 'NO HAY PAQUETES PARA ENVIAR');
            //     }else{
            //         $factura = Facturacion::where('fac_manual','=',1)
            //         ->where('id_sucursal','=',$id_sucu)
            //         ->where('punto_venta','=',$punto_v)
            //         ->select('id_factura','hora_impuestos','cufd','cafc')
            //         ->orderby('id')
            //         ->first();
                

            //         //dd($factura);
            //         $horaIni = $factura->hora_impuestos;
            //         $cafc_f = $factura->cafc;

            //         $num_fac1 = DB::select(DB::raw('SELECT COUNT(*) as num, cufd FROM factura WHERE fac_manual = 1 AND factura.id_sucursal = ? AND factura.punto_venta = ? GROUP BY cufd'),[$id_sucu,$punto_v]);
            //         $num_fac=$num_fac1[0]->{'num'};
            //         $cufd_ant = $num_fac1[0]->{'cufd'};
            //         //dd($num_fac1);
            //         $cuis = $this->cuis_usuario();
            //         $fecha_hora3 = $this->fechaHora(true);
            //         //dd($fecha_hora3);
            //         //--------------------------------------------------------------------------
            //         //comprimir el paquete de facturas en el archivo .tar.gz para el envío por paquetes
            //         $dir = storage_path('/facturas/miprueba'.$dato.'.tar');
            //         if(file_exists($dir) == false){            
                        
            //             $p = new \PharData($dir);
            //             $i = 1;
            //             for( $i = 1; $i <= 500; $i++){
            //                 $origen = storage_path('/facturas/manualF'.$dato.$i.'.xml');
            //                 if(file_exists($origen))
            //                 {
            //                     // echo ("existe $origen");
            //                     $factura = file_get_contents($origen);
            //                     $p['factura'.$dato.$i.'.xml'] = $factura;
            //                 }
            //                 else{
            //                     break;
            //                 }
            //             }
            //             $p1 = $p->compress(\Phar::GZ); // copia a /ruta/a/mi.tar.gz
            //             // $p2 = $p->compress(\Phar::BZ2); // copia a /ruta/a/mi.tar.bz2
            //             // $p3 = $p2->compress(Phar::NONE); // excepción: /ruta/a/mi.tar ya existe
            //             unset($p);
            //             unset($p1);

            //             $archivoArray = file(storage_path('/facturas/miprueba'.$dato.'.tar.gz'));
            //             $hash = hash_file("sha256",storage_path('/facturas/miprueba'.$dato.'.tar.gz'), $raw_output = false);
            //             $archivo = implode($archivoArray);
            //         }

                    
            //         //nuevo
            //         $cufd1 = $this->cufd();
            //         //dd( $cufd1);
                    
            //         if(is_soap_fault($cufd1)){
            //             $this->borradoarchivos();
            //             return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: NO SE PUDO SOLISITAR UN NUEVO CUFD PARA EL ENVIO DE PAQUETE: '.$cufd1 );
            //         }
            //         $cufd = $cufd1->codigo_cufd;
                
            //         try {
            //             $wsdlOperaciones=$this->fac_operaciones;
            //             $token =  $this->token;
            //             $wsdl = $this->fac_compra_venta;

                        
            //             $clientOperaciones = new \SoapClient($wsdlOperaciones, [ 
            //                 'stream_context' => stream_context_create([ 
            //                     'http'=> [ 
            //                         'header' => "apikey: TokenApi $token",
            //                     ] 
            //                 ]),

            //                 'cache_wsdl' => WSDL_CACHE_NONE,
            //                 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            //             ]);
            //             // dd($clientOperaciones->__getTypes());
            //             var_dump($cufd, $cufd_ant);

            //             $solicitudEventoSignificativo = array(
            //                 'codigoAmbiente'        => $this->codigo_ambiente,
            //                 'codigoMotivoEvento'    => $evento_significado,
            //                 'codigoPuntoVenta'      => $punto_v,
            //                 'codigoSistema'         => $this->codigo_sistema,
            //                 'codigoSucursal'        => $id_sucu,
            //                 'cufd'                  => $cufd,
            //                 'cufdEvento'            => $cufd_ant,
            //                 'cuis'                  => $cuis->codigo_cuis,
            //                 // 'cuis'                  => 'EED58C49',
            //                 'descripcion'           => $descrip_evento,
            //                 'fechaHoraFinEvento'    => $fecha_hora3,
            //                 'fechaHoraInicioEvento' => $horaIni,
            //                 'nit'                   => $this->nit,
            //             );

            //             $objEventoSignificativo = $clientOperaciones->registroEventoSignificativo(
            //                 array(
            //                     "SolicitudEventoSignificativo" => $solicitudEventoSignificativo,
            //                 )
            //             );

            //         //dd($objEventoSignificativo,$horaIni,$fecha_hora3);
            //             $respEventos = $objEventoSignificativo->{'RespuestaListaEventos'};
            //             $evento = $respEventos->{'codigoRecepcionEventoSignificativo'};
            //         } catch(SoapFault $e) {
            //             //dd('entro');
            //             $this->borradoarchivos();
            //             return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE EN EVENTO SIGNIFICATIVOs -  '. $e);
            //             //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            //         }
            //         catch(exception $e) {
            //             //dd('entro');
            //            $this->borradoarchivos();
            //             return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE EN EVENTO SIGNIFICATIVOS -  '. $e);
            //             //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            //         }
            //         // ------------------------------------------------------------------------
            //         // Envío de factura por paquetes

            //         try {
            //             $client = new \SoapClient($wsdl, [ 
            //                 'stream_context' => stream_context_create([ 
            //                     'http'=> [ 
            //                         'header' => "apikey: TokenApi $token",  
            //                     ] 
            //                 ]),

            //                 'cache_wsdl' => WSDL_CACHE_NONE,
            //                 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            //             ]);
                        
            //             $solicitudRecepcionFactura = array(
            //                 'codigoAmbiente'        => $this->codigo_ambiente,
            //                 'codigoDocumentoSector' => 1,
            //                 'codigoEmision'         => 2,
            //                 'codigoModalidad'       => $this->modalidad,
            //                 'codigoPuntoVenta'      => $punto_v,
            //                 'codigoSistema'         => $this->codigo_sistema,
            //                 'codigoSucursal'        => $id_sucu,
            //                 'cufd'                  => $cufd,
            //                 'cuis'                  => $cuis->codigo_cuis,
            //                 // 'cuis'                  => 'EED58C49',
            //                 'nit'                   => $this->nit,
            //                 'tipoFacturaDocumento'  => 1,
            //                 'archivo'               => $archivo,
            //                 'fechaEnvio'            => $fecha_hora3,
            //                 'hashArchivo'           => $hash,
            //                 'cafc'                  => $cafc_f,
            //                 'cantidadFacturas'      => $num_fac,
            //                 'codigoEvento'          => $evento,
            //             );

                        
            //             $recepPaq = $client->recepcionPaqueteFactura(
            //                 array(
            //                     "SolicitudServicioRecepcionPaquete" => $solicitudRecepcionFactura,
            //                 )
            //             );
            //             //dd($recepPaq);
            //             $servicioPaq = $recepPaq->{'RespuestaServicioFacturacion'};
            //             $codDescrip = $servicioPaq->{'codigoDescripcion'};
            //             $codEstado = $servicioPaq->{'codigoEstado'};
            //             $codRecep = $servicioPaq->{'codigoRecepcion'};
                    
            //         // --------------------------------------------------------------------------
            //         // Validar la recepcion del paquete de facturas

            //         $SolicitudServicioValidacionRecepcionPaquete = array(
            //             'codigoAmbiente'        => $this->codigo_ambiente,
            //             'codigoDocumentoSector' => 1,
            //             'codigoEmision'         => 2,
            //             'codigoModalidad'       => $this->modalidad,
            //             'codigoPuntoVenta'      => $punto_v,
            //             'codigoSistema'         => $this->codigo_sistema,
            //             'codigoSucursal'        => $id_sucu,
            //             'cufd'                  => $cufd,
            //             'cuis'                  => $cuis->codigo_cuis,
            //             // 'cuis'                  => 'EED58C49',
            //             'nit'                   => $this->nit,
            //             'tipoFacturaDocumento'  => 1,
            //             'codigoRecepcion'       => $codRecep, 
            //         );
            //         $validarPaq = $client->validacionRecepcionPaqueteFactura(
            //             array(
            //                 "SolicitudServicioValidacionRecepcionPaquete" => $SolicitudServicioValidacionRecepcionPaquete,
            //             )
            //         );
            //         //dd($validarPaq);
            //         // var_dump("este es el resultado");
            //         $servicioPaq = $validarPaq->{'RespuestaServicioFacturacion'};
            //         $codDescrip = $servicioPaq->{'codigoDescripcion'};
            //         $codEstado = $servicioPaq->{'codigoEstado'};
            //         $codRecep = $servicioPaq->{'codigoRecepcion'};
            //        // dd($servicioPaq);
            //         // ------------------------------------------------------------------------
            //         }catch(SoapFault $e) {
            //             //dd('entro');
            //             $this->borradoarchivos();
            //             return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE EN ENVIARPAQUETE Y/O VALIDAR EL PAQUETE -  '. $e);
            //             //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            //         }
            //         catch(exception $e) {
            //             //dd('entro');
            //         $this->borradoarchivos();
            //             return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE EN ENVIARPAQUETE Y/O VALIDAR EL PAQUETE -  '. $e);
            //             //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            //         }
            //          //dd($codRecep);

            //         if($codEstado == 908 || $codEstado == 901){
            //             $fac = Facturacion::where('fac_manual','=',1)->get();
            //             foreach($fac as $fac){
            //                 $fac->fac_manual = 0;
            //                 $fac->save();
            //             }
            //             $tipo_fac = "manual";
            //             $bora = $this->pruebaborrado($tipo_fac);
            //             if($codEstado == 901){
            //                 sleep(3);
            //                 $pendiente = $this->pendiente($codRecep);
            //                 $codDescrip = $pendiente;
            //             }
                    
            //             return redirect('facturacion')->with('status', 'ENVIO DE PAQUETE Codigo:'.$codEstado.' Descripcion:'.$codDescrip);
            //         }else{
            //             return redirect('facturacion')->with('status', 'ERROR EN ENVIO DE PAQUETE '.$codDescrip);
            //         }
                    
            //     }
            // }
    //FUNCION PARA VALIDAR PENDIENTE
    public function pendiente($codRecep){
        $codigo_pendiente=$codRecep;
        $fecha_hora = Carbon::now(-4)->format('Y-m-d H:i:s');
        $dato = auth()->user()->id;
        $id_sucu = auth()->user()->id_sucursal;
        $punto_v = auth()->user()->punto_venta;
        $cuis = $this->cuis_usuario();
        $cufd = Cufd::where('id_usuario','=',$dato)
        ->where('id_sucursal','=',$id_sucu)
        ->where('punto_venta','=',$punto_v)
        ->where('fecha_hora','>',$fecha_hora )
        ->orderBy('id','DESC')
        ->first();
       // dd($cufd);

        try {
            $wsdlOperaciones=$this->fac_operaciones;
            $token =  $this->token;
            $wsdl = $this->fac_compra_venta;
            
            $client = new \SoapClient($wsdl, [ 
                'stream_context' => stream_context_create([ 
                    'http'=> [ 
                        'header' => "apikey: TokenApi $token",  
                    ] 
                ]),

                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            ]);

            $SolicitudServicioValidacionRecepcionPaquete = array(
                'codigoAmbiente'        => $this->codigo_ambiente,
                'codigoDocumentoSector' => 8,
                'codigoEmision'         => 2,
                'codigoModalidad'       => $this->modalidad,
                'codigoPuntoVenta'      => $punto_v,
                'codigoSistema'         => $this->codigo_sistema,
                'codigoSucursal'        => $id_sucu,
                'cufd'                  => $cufd->codigo_cufd,
                'cuis'                  => $cuis->codigo_cuis,
                // 'cuis'                  => 'EED58C49',
                'nit'                   => $this->nit,
                'tipoFacturaDocumento'  => 2,
                'codigoRecepcion'       => $codigo_pendiente, 
            );
            $validarPaq = $client->validacionRecepcionPaqueteFactura(
                array(
                    "SolicitudServicioValidacionRecepcionPaquete" => $SolicitudServicioValidacionRecepcionPaquete,
                )
            );

            // var_dump("este es el resultado");
            //dd($validarPaq);
            $servicioPaq = $validarPaq->{'RespuestaServicioFacturacion'};
            $codDescrip = $servicioPaq->{'codigoDescripcion'};
            $codEstado = $servicioPaq->{'codigoEstado'};
            $codRecep = $servicioPaq->{'codigoRecepcion'};

            return($codDescrip);
        } catch(SoapFault $e) {
            return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE EN ENVIO DE PENDIENTE -  '. $e);
        }
    }
    
    //FUNCION SEGUNDO PLANO PARA AGREGAR PRODUCTO
    public function producto_fac(Request $request){
        $p=$request->get('dato');
        $prueba = DB::table('productos')
        ->where('id','=',$p)
        ->select('id','codigo_empresa','descripcion','codigo_impuestos','codigo_unidad_medida','unidad_medida','precio')
        ->get();
 
        return response(json_encode(array('prueba'=>$prueba)),200)->header('Content-type','text/plain');
     }
    //FUNCIONES DE MANEJO DE PAGINA
    public function index(){
        $evento_significativo = ParametricasEventosSignificativos::where('paquete_manual','=',1)->get();
        $evento_significativo2 = ParametricasEventosSignificativos::where('paquete_manual','=',0)->get();
        $motivo_anulacion = ParametricaMotivoAnulacion::all();
        return view('facturacion_tasa_cero.index',['motivo_anulacion'=>$motivo_anulacion, 'evento_significativo' =>$evento_significativo, 'evento_significativo2' =>$evento_significativo2]);
    }
     public function create(){   

        $productos = Productos::where('codigo_actividad','=',492331)->where('estado','=',1)->get();
        $tipo_doc = ParametricaDocumentoTipoIdentidad::all();
        $fecha=Carbon::now(-4)->format('Y-m-d');
        $tipo_pago = ParametricaTipoMetodoPago::where('estado','=',1)->get();
        $unidad_medida = UnidadMedida::where('estado','=',1)->get();    $unidad_medida = UnidadMedida::where('estado','=',1)->get();
        $id_fac = FacturaTasaCero::where('id_sucursal','=',auth()->user()->id_sucursal)
        ->where('punto_venta','=',auth()->user()->punto_venta)
        ->where('nro_nota_venta', '=',0)
        ->where('nro_fac_manual', '=',0)
        ->orderby('id','DESC')
        ->get();
        if($id_fac->isEmpty()){
            $id_fac->id_factura = 0;
        }else{
            $id_fac = FacturaTasaCero::where('id_sucursal','=',auth()->user()->id_sucursal)
            ->where('punto_venta','=',auth()->user()->punto_venta)
            ->where('nro_nota_venta', '=',0)
            ->where('nro_fac_manual', '=',0)
            ->orderby('id','DESC')
            ->first();
        }
        $sucu = Sucursal::where('nro_sucursal','=',auth()->user()->id_sucursal)
        ->first();
        //dd($sucu);
         return view('facturacion_tasa_cero.facturacion',['unidad_medida'=>$unidad_medida,'id_fac'=>$id_fac, 'sucu'=>$sucu,'productos'=>$productos,'tipo_doc'=>$tipo_doc, 'fecha'=>$fecha, 'tipo_pago'=>$tipo_pago]);     
    }
    
    // public function create2(){  
            //     $fecha = Carbon::now(-4)->format('Y-m-d');  
            //     $comprobando =Cafc::where('fecha_vigencia','>',$fecha)
            //     ->where('id_sucursal','=',auth()->user()->id_sucursal)
            //     ->where('id_punto_venta','=',auth()->user()->punto_venta)
            //     ->get();
            //     $codigo_cafc =Cafc::where('fecha_vigencia','>',$fecha)
            //     ->where('id_sucursal','=',auth()->user()->id_sucursal)
            //     ->where('id_punto_venta','=',auth()->user()->punto_venta)
            //     ->first();
            
            //     if($comprobando->isEmpty() || $codigo_cafc->nro_cafc_emitidas == $codigo_cafc->nro_final){
            //         return redirect('facturacion')->with('status','CAFC YA NO ESTA VIGENTE, INGRESE UNO NUEVO');
            //     }

            //     $productos = Productos::all();
            //     $tipo_doc = ParametricaDocumentoTipoIdentidad::all();
            //     $hora=Carbon::now(-4)->format('H:i');
            //     //dd($hora);
            //     $tipo_pago = ParametricaTipoMetodoPago::where('estado','=',1)->get();
            //     $id_fac = Facturacion::where('id_sucursal','=',auth()->user()->id_sucursal)
            //     ->where('punto_venta','=',auth()->user()->punto_venta)
            //     ->where('id_factura', '=',0)
            //     ->where('nro_nota_venta', '=',0)
            //     ->orderby('id','DESC')
            //     ->get();
            //     if($id_fac->isEmpty()){
            //         $id_fac->nro_fac_manual= 0;
            //     }else{
            //         $id_fac = Facturacion::where('id_sucursal','=',auth()->user()->id_sucursal)
            //         ->where('punto_venta','=',auth()->user()->punto_venta)
            //         ->orderby('id','DESC')
            //         ->where('id_factura', '=',0)
            //         ->where('nro_nota_venta', '=',0)
            //         ->first();
            //     }
            //     $sucu = Sucursal::where('nro_sucursal','=',auth()->user()->id_sucursal)
            //     ->first();
            //      return view('facturacion.facturacion_manual',['id_fac'=>$id_fac, 'sucu'=>$sucu,'productos'=>$productos,'tipo_doc'=>$tipo_doc, 'fecha'=>$fecha, 'tipo_pago'=>$tipo_pago,'codigo_cafc'=>$codigo_cafc, 'hora'=>$hora]);     
            // }
    //FUNCION PARA GUARDAR FACTURAS MANUALES
            // public function facturasManuales(Request $request){
            //     $fechamanual = $request{'fecha'};
            //     $hora = $request{'hora'};
            //     $fechahoy = Carbon::now(-4)->format('Y-m-d');
                
            //     $fecha_hora3 = $fechamanual."T".$hora.":51.159";

            //     $tipoEmisionN = 2; 
            //     $validanit = 1;

            //     //NUMERO DE FACTURA MANUAL
            //     $nro_cafc = Cafc::where('id_sucursal','=',auth()->user()->id_sucursal)
            //     ->where('id_punto_venta','=',auth()->user()->punto_venta)
            //     ->orderby('id','DESC')
            //     ->first();//aumentar que sea por sucursal mas

            //     if($nro_cafc->nro_cafc_emitidas == 0){
            //         $numFac = 1;
            //     }else{
            //         $numFac = $nro_cafc->nro_cafc_emitidas+1;
            //     }
                    
            //     if($fechamanual == $fechahoy){
            //         $fechamanual1 =  Carbon::parse($fechamanual);
            //         $fechamanual=$fechamanual1->addDay(1);
            //     }
            //     $cufd = Cufd::where('fecha','=', $fechamanual)
            //     ->where('id_sucursal','=',auth()->user()->id_sucursal)
            //     ->where('punto_venta','=',auth()->user()->punto_venta)
            //     ->orderby('id','DESC')
            //     ->get();
            //     //dd($fechamanual,$cufd);
                
            //     $f = $fechamanual;
            //     $sw = 1;
                
            //     $cont = 1;
            //     do {
                    
            //         if($cufd->isEmpty()){
            //             $fechac = Carbon::parse($fechamanual);
            //             $fechac = $fechac->subDay($cont);
            //             $cufd = Cufd::where('fecha','=', $fechac)
            //             ->where('id_sucursal','=',auth()->user()->id_sucursal)
            //             ->where('punto_venta','=',auth()->user()->punto_venta)
            //             ->orderby('id','DESC')
            //             ->get();
            //             $f = $fechac;  
            //             var_dump($cufd);             
            //         }
            //         else{
            //             $sw = 0;
            //         }
            //         $cont++;
            //     }
            //     while($sw == 1 && $cont<=10);
            //     //dd($cufd);

            //     $cufd = Cufd::where('fecha','=', $f)
            //     ->where('id_sucursal','=',auth()->user()->id_sucursal)
            //     ->where('punto_venta','=',auth()->user()->punto_venta)
            //     ->orderby('id','DESC')
            //     ->first();

            //     //dd($cufd);
            //     $cuis = $this->cuis();

            //     if(is_soap_fault($cuis)){
            //         return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: NO SE PUDO SOLISITAR UN NUEVO CUIS PARA EL ENVIO DE PAQUETE: '.$cuis );
            //     }
            //     $cufd_codigo = $cufd->codigo_cufd;
            //     $cufd_codigoControl = $cufd->codigo_control;
                                
            //     // funcion para obtener cuf
            //      $cuf = $this->cuf($fecha_hora3,$tipoEmisionN,$cufd_codigoControl,$numFac);
                
            //     // -----------------------------------------------------------------------------------------------
            //     // VARIABLES PARA EL XML y BD
            //     // DATOS DE EMPRESA sacado de BD tabla datos_empresa, sucursal y ususario
            //     $dato = auth()->user()->id;
            //     $datos_empresa = Datos::first();
            //     $sucursal = Sucursal::where('nro_sucursal','=',auth()->user()->id_sucursal)->first();

            //     $nitEmisor = $datos_empresa->nit; 
            //     $razonSocialEmisor = $datos_empresa->razon_social;
            //     $municipio = $sucursal->municipio;
            //     $telefono = $sucursal->telefono;
            //     $direccion = $sucursal->direccion;
            //     $codPtoVenta = auth()->user()->punto_venta;
            //     $codSucursal = $sucursal->nro_sucursal;
            //     $codSector1 = 1; //no cambia

            //     //LEYENDA 2
            //     //randon de leyendas que puede sacar
            //     $sw_leyenda = 0;
            //     while($sw_leyenda == 0){
            //         $randon = rand(1,15);
            //         if($randon%2 != 0){
            //             $sw_leyenda = 1;
            //         }    
            //     }
                
            //     $leyenda = LeyendasFacturacion::where('id','=',$randon)
            //     ->where('codigo_actividad','=',522001)
            //     ->first();
            //     $descrip_leyenda = $leyenda->descripcion_leyenda;

            //     //Datos del cliente
            //     $razonSocialCli =  $request->get('razon_social');
            //     if($request->get('nro_documento') == null ){
            //         $nroDocID = " ";
            //     }else{
            //         $nroDocID =$request->get('nro_documento');
            //     }

            //     $id_cliente = Clientes::where('nro_documento','=',$request->get('nro_documento'))->get();

            //     if($id_cliente->isEmpty()){
            //         $clie = new Clientes;
            //         $clie->nro_documento = $nroDocID;
            //         $clie->razon_social = $razonSocialCli;
            //         $clie->email = $request->get('email');
            //         $clie->save();
            //         $codCli1 = $clie->id;
            //     }
            //     else{
            //         $id_cliente = Clientes::where('nro_documento','=',$request->get('nro_documento'))->first();
            //         $codCli1 = $id_cliente->id;
            //         $id_cliente->razon_social =  $request->get('razon_social');
            //         $id_cliente->email = $request->get('email');
            //         $id_cliente->save();
            //     }

            //     $codMetodoPago = $request->get('id_tipo_pago');
                

            //     $codDocID = $request->get('id_tipo_documento');

            //     $complemento = $request->get('complemento'); //si no tiene en xml 
                
            //     //dd($complemento);
            //     $codCli = $codCli1;
            //     $usuario = $dato;
            //     $codMoneda1 = 1;
                
            //     // montos  TOTALGENERAL
            //     $total_final  = (double) str_replace(',', '', $request->get('total_detalle'));
            //     $tipoCambio = 1; //taza de cambio
            //     $descuentoAdicional = 0; //no cambia   
            //     $montoTotalMoneda =  $total_final;
            //     $montoTotal =  $total_final;
            //     $montoTotalSujetoIva =  $total_final;  

            //     //detalle productos
            //     $codigo_p = $request->get('codigo');//codigo_empresa
            //     $codigo_impuestos_p = $request->get('codigo_impuestos');//ide correlativo
            //     $cantidad_p = $request->get('cantidad');
            //     $unidad_medida_p = $request->get('unidad_medida');
            //     $id_unidad_medida_p = $request->get('codigo_unidad_medida'); // pendiente
            //     $descripcion_p = $request->get('descripcion');
            //     $precio_unitario_p = $request->get('precio_uni');
            //     $subtotal_p = $request->get('subtotal');


            //     //NO TOCAR FORMATO -- SI ES NECESARIO CAMBIAR NOMBRE DE VARIABLE
            //     //-----------------------------------------------------------------------------
            //     //  Fragmento de código para generar el XML a partir de los datos del formulario.

            //     $doc = new DOMDocument('1.0', 'utf-8');

            //     $doc->formatOutput = true;

            //     $xmlFactura = $doc->appendChild($doc->createElement('facturaComputarizadaCompraVenta'));
            //     $xmlFactura->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

            //     $cabecera = $xmlFactura->appendChild($doc->createElement('cabecera'));

            //     $cabecera->appendChild($doc->createElement('nitEmisor',$nitEmisor));
            //     $cabecera->appendChild($doc->createElement('razonSocialEmisor',$razonSocialEmisor));
            //     $cabecera->appendChild($doc->createElement('municipio',$municipio));
            //     $cabecera->appendChild($doc->createElement('telefono',$telefono));
            //     $cabecera->appendChild($doc->createElement('numeroFactura',$numFac));
            //     $cabecera->appendChild($doc->createElement('cuf',$cuf));
            //     $cabecera->appendChild($doc->createElement('cufd',$cufd_codigo));
            //     $cabecera->appendChild($doc->createElement('codigoSucursal',$codSucursal));
            //     $cabecera->appendChild($doc->createElement('direccion',$direccion));
            //     $cabecera->appendChild($doc->createElement('codigoPuntoVenta', auth()->user()->punto_venta));
            //     $cabecera->appendChild($doc->createElement('fechaEmision',$fecha_hora3));
            //     $cabecera->appendChild($doc->createElement('nombreRazonSocial',$razonSocialCli));
            //     $cabecera->appendChild($doc->createElement('codigoTipoDocumentoIdentidad',$codDocID));
            //     $cabecera->appendChild($doc->createElement('numeroDocumento',$nroDocID));
            //     if($codDocID == 1  && strlen($complemento) > 0){
            //         $cabecera->appendChild($doc->createElement('complemento',$complemento));}
            //     else{
            //         $complemento = $cabecera->appendChild($doc->createElement('complemento'));
            //         $complemento->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
            //     }
                    
            //     $cabecera->appendChild($doc->createElement('codigoCliente',$codCli));
            //     $cabecera->appendChild($doc->createElement('codigoMetodoPago',$codMetodoPago));
            //     if($codMetodoPago==2)
            //         $cabecera->appendChild($doc->createElement('numeroTarjeta',4074000000000559));
            //     else
            //     {
            //         $nroTarjeta = $cabecera->appendChild($doc->createElement('numeroTarjeta'));
            //         $nroTarjeta->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
            //     }        
            //     $cabecera->appendChild($doc->createElement('montoTotal',$montoTotal));
            //     $cabecera->appendChild($doc->createElement('montoTotalSujetoIva',$montoTotalSujetoIva));
            //     $cabecera->appendChild($doc->createElement('codigoMoneda',$codMoneda1));
            //     $cabecera->appendChild($doc->createElement('tipoCambio',$tipoCambio));
            //     $cabecera->appendChild($doc->createElement('montoTotalMoneda',$montoTotalMoneda));       
                
            //      $montoGiftCard = $cabecera->appendChild($doc->createElement('montoGiftCard'));
            //      $montoGiftCard->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
            //     $cabecera->appendChild($doc->createElement('descuentoAdicional',$descuentoAdicional));
            //     $cabecera->appendChild($doc->createElement('codigoExcepcion',1));
            //     $cabecera->appendChild($doc->createElement('cafc',$request->get('codigo_cafc')));     
            //     $cabecera->appendChild($doc->createElement('leyenda', $descrip_leyenda));
            //     $cabecera->appendChild($doc->createElement('usuario',$dato));
            //     $cabecera->appendChild($doc->createElement('codigoDocumentoSector',$codSector1));

            //     $cont = 0; 
                        
            //     while($cont < count($codigo_p)){

            //         $cantidad = (double) str_replace(',', '', $cantidad_p[$cont]);
            //         $precio_u = (double) str_replace(',', '', $precio_unitario_p[$cont]);
            //         $subtotal = (double) str_replace(',', '', $subtotal_p[$cont]);

            //         $detalle = $xmlFactura->appendChild($doc->createElement('detalle'));

            //         $detalle->appendChild($doc->createElement('actividadEconomica',522001)); // codigo de actividad secundaria
            //         $detalle->appendChild($doc->createElement('codigoProductoSin',$codigo_impuestos_p[$cont]));
            //         $detalle->appendChild($doc->createElement('codigoProducto',$codigo_p[$cont]));
            //         $detalle->appendChild($doc->createElement('descripcion',$descripcion_p[$cont]));
            //         $detalle->appendChild($doc->createElement('cantidad',number_format($cantidad, 2, '.', '')));
            //         $detalle->appendChild($doc->createElement('unidadMedida',$id_unidad_medida_p[$cont]));
            //         $detalle->appendChild($doc->createElement('precioUnitario',number_format($precio_u, 2, '.', '')));
            //         $detalle->appendChild($doc->createElement('montoDescuento',0));
            //         $detalle->appendChild($doc->createElement('subTotal',round($subtotal, 2)));
            //         $numSerie = $detalle->appendChild($doc->createElement('numeroSerie'));
            //         $numSerie->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
            //         $numeroImei = $detalle->appendChild($doc->createElement('numeroImei'));
            //         $numeroImei->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");

            //         $cont++;
            //     }        

            //     //para guardar storage  
            //     $dir = storage_path('facturas/manual'.$dato.'.xml');
            //     $doc->save($dir);               

            //     //METODO 
            //     // ----------------------------------------------------------------------------------
            //     // Firmado del archivo xml desde php

                
            //     //buscar valores corregir
            //     $modalidad = "2"; //modalidad de electronica en linea
            //     $codTipoFac = 1; //tipo de factura que se envia 

            //     $wsdl = $this->fac_compra_venta;
            //     $token =  $this->token;

            //     if($tipoEmisionN == 2){
            //         $i = 0;
            //         $dir1 = storage_path('/facturas/manualF'.$dato.'.xml');
            //         $doc->save($dir1);
            //         for($i=1;$i<=500;$i++){
            //             $dir = storage_path('/facturas/manualF'.$dato.$i.'.xml');
            //             if(!file_exists($dir)){
            //                 $doc->save($dir);
            //                 //dd($dir);
            //                 break;
            //             }
                        
            //         }
            //         //dd($i);
                    
            //         $codigo_verifica_des  ="FACTURA MANUAL EMITIDA";
            //         $nro_cafc = Cafc::where('codigo_cafc','=',$request->get('codigo_cafc'))->first();
            //         $nro_cafc->nro_cafc_emitidas = $nro_cafc->nro_cafc_emitidas + 1;
            //         $nro_cafc->save(); 
                    
            //     }    

            //     //FIN DE METODO  
                

            //     // GUARDAR EN BD
                
            //         $factura_bd = new Facturacion;

            //         //DATOS GUARDADOS EN BD TABLA FACTURA
                    
            //         $fechabd =  str_replace('T', ' ', $fecha_hora3);
            //         $fecha_hora1 = substr($fechabd, 0, -4);
            //         $fecha1 = substr($fecha_hora1, 0, -9); 
            //         $factura_bd->nro_fac_manual = $numFac;    
            //         $factura_bd->id_sucursal = auth()->user()->id_sucursal;
            //         $factura_bd->punto_venta = auth()->user()->punto_venta;
            //         $factura_bd->fecha = $fecha1;
            //         $factura_bd->fecha_hora = $fecha_hora1;
            //         $factura_bd->razon_social = $razonSocialCli;
            //         $factura_bd->cuf = $cuf;
            //         $factura_bd->cufd = $cufd_codigo;
            //         $factura_bd->tipo_documento_identidad = $codDocID; 
            //         $factura_bd->nro_documento = $nroDocID;

            //         $factura_bd->complemento = $request->get('complemento');
                    
            //         $factura_bd->codigo_cliente = $codCli;
                    
            //         $factura_bd->id_metodo_pago = $codMetodoPago;
                    
            //         $factura_bd->tipo_emision_n = $tipoEmisionN;
            //         $factura_bd->id_leyenda = $leyenda->id;

            //         $factura_bd->monto_total = $total_final;
            //         $factura_bd->monto_total_sujeto_iva =  $total_final;
            //         //$factura_bd->tipo_cambio = $tipoCambio; todo es en bolivianos
            //         $factura_bd->monto_total_moneda =   $total_final ; //suma de gastos nacionales + suma del total detalle -- particularmente el dato es igual al total detalle por que fob es cero
                    
            //         $factura_bd->codigo_excepcion = $validanit ;
            //         $factura_bd->descuento_adicional = 0 ; // no cambia
            //         $factura_bd->cafc = $request->get('codigo_cafc'); //no cambia - no se emitira manuales
                    
            //         //$factura_bd->codigo_documento_sector =  $codSector1 ;// no cambia
            //         $factura_bd->id_usuario = $dato;
            //         $factura_bd->hora_impuestos = $fecha_hora3;
            //         $factura_bd->fac_manual = 1;
            //         $factura_bd->fuera_linea = 0;
                    
            //         $factura_bd->save();

            //        //variables necesarias declaradas mas arriba           
            //        $cont = 0; 
            //        //dd($factura_bd->id);
                        
            //        while($cont < count($codigo_p)){

            //             $cantidad = (double) str_replace(',', '', $cantidad_p[$cont]);
            //             $precio_u = (double) str_replace(',', '', $precio_unitario_p[$cont]);
            //             $subtotal = (double) str_replace(',', '', $subtotal_p[$cont]);
                        
            //             $producto_detalle = new FacturaDetalle;
            //             $producto_detalle->id_tabla_factura = $factura_bd->id;
            //             $producto_detalle->id_factura = $numFac;
            //             $producto_detalle->id_sucursal = auth()->user()->id_sucursal;
            //             $producto_detalle->punto_venta =auth()->user()->punto_venta;

            //             $producto_detalle->codigo_producto_sin = $codigo_impuestos_p[$cont];
            //             $producto_detalle->codigo_producto_empresa =$codigo_p[$cont];
            //             $producto_detalle->descripcion = $descripcion_p[$cont];
            //             $producto_detalle->unidad_medida_des =$unidad_medida_p[$cont];
            //             $producto_detalle->cantidad = $cantidad;
            //             $producto_detalle->precio = $precio_u;

            //             $producto_detalle->subtotal = $subtotal;
                
            //             $producto_detalle->save();  

            //             $cont++;
                    
            //        }
            //         $tipo_fac = "manualF";
            //         $pdf = $this->pdf_en_servidor($factura_bd->id);
            //         $correo = $this->correo($factura_bd->id,$tipo_fac);

                
            //     return redirect('facturacion')->with('status', $codigo_verifica_des. 'GUARDADO CON EXITO');
            // }
    public function store(Request $request)
    {
        //     $conexion = $this->prueba_veri_conec();
        //     if($conexion == 0){
        //         return redirect('facturacion')->with('status', 'NO HAY CONEXION A SERVICIO DE IMPUESTOS INTERNOS');
        //     }
            
        //NUMERO DE FACTURA QUE SE ENVIARA EN LINEA Y FUERA DE LINEA

        $factura = FacturaTasaCero::where('id_sucursal','=',auth()->user()->id_sucursal)
        ->where('punto_venta','=',auth()->user()->punto_venta)
        ->where('id_factura','!=',0)
        ->orderby('id','DESC')
        ->first();       
        if($factura==null){
            $numFac = 1;
        }else{
            $numFac = $factura->id_factura+1;
        }
        

        //datos para mandar al xml 
        if($request->get('linea') == 1){
            
            $fecha_hora3 = $this->fechaHora(true);
            if(is_soap_fault($fecha_hora3)){
                return redirect('facturacionT/create')->withInput()->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: NO SE PUDO HORA: '.$fecha_hora3 );
            }
            $tipoEmisionN = 1; //TRUE - ENVIO INDIVIDUAL 
            $validanit = $request->get('validanit'); // codigo (valor 1) para ver si se emite factura con nit errado  
            //dd($validanit);
            if($validanit == null){
                $validanit = 0;
            }
        
        }else{
            $fecha_hora3 = Carbon::now(-4)->format('Y-m-d/H:i:s.z');
            $fecha_hora3 = str_replace("/", "T", $fecha_hora3);
            $tipoEmisionN = 2; //TRUE - ENVIO INDIVIDUAL 
            $validanit = 1;  
            //dd($fecha_hora3);
        }

        //fecha para preguntar vigencia del cufd y cuis
        $fecha_hora = Carbon::now(-4)->format('Y-m-d H:i:s');
        //cuis solicitud una vez al año. por usuario.
        $cuis = $this->cuis();
        if(is_soap_fault($cuis)){
            return redirect('facturacionT')->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: NO SE PUDO SOLISITAR UN NUEVO CUIS PARA EL ENVIO DE PAQUETE: '.$cuis );
        }
        //cufd solicitud una vez al dia o en caso de contigencia al finalizar la misma.
        $cufdbd = Cufd::where('fecha_hora','>=',$fecha_hora)
        ->where('id_sucursal','=',auth()->user()->id_sucursal)
        ->where('punto_venta','=',auth()->user()->punto_venta)
        ->orderby('id','DESC')
        ->get();
         //dd($cufdbd);
        if($cufdbd->isEmpty() == true){
             $cufd = $this->cufd();
        }

        $cufd = Cufd::where('id_sucursal','=',auth()->user()->id_sucursal)
        ->where('punto_venta','=',auth()->user()->punto_venta)
        ->orderby('id','DESC')
        ->first();
        //dd($cufd);
        $cufd_codigo = $cufd->codigo_cufd;
        $cufd_codigoControl = $cufd->codigo_control;
                       
        // funcion para obtener cuf
        //dd($fecha_hora3,$tipoEmisionN,$cufd_codigoControl,$numFac);
         $cuf = $this->cuf($fecha_hora3,$tipoEmisionN,$cufd_codigoControl,$numFac);
      
        // -----------------------------------------------------------------------------------------------
        // VARIABLES PARA EL XML y BD
        // DATOS DE EMPRESA sacado de BD tabla datos_empresa, sucursal y ususario
        $dato = auth()->user()->id;
        $datos_empresa = Datos::first();
        $sucursal = Sucursal::where('nro_sucursal','=',auth()->user()->id_sucursal)->first();

        $nitEmisor = $datos_empresa->nit; 
        $razonSocialEmisor = $datos_empresa->razon_social;
        $municipio = $sucursal->municipio;
        $telefono = $sucursal->telefono;
        $direccion = $sucursal->direccion;
        $codPtoVenta = auth()->user()->punto_venta;
        $codSucursal = $sucursal->nro_sucursal;
        $codSector1 = 1; //no cambia

        //LEYENDA 2
        //randon de leyendas que puede sacar
        $id_leyenda_actividad = LeyendasFacturacion::where('codigo_actividad','=',492331)->select('id')->get();
         $sw_leyenda = 0;
         while($sw_leyenda == 0){
             $randon = rand(1,21);
            
             $idley = LeyendasFacturacion::where('codigo_actividad','=',492331)->where('id','=',$randon)->get();
             if($idley->isEmpty() == false){
                 $sw_leyenda = 1;
             }
               
         }
        //dd($id_leyenda_actividad,$randon,$idley); 
         $leyenda = LeyendasFacturacion::where('id','=',$randon)
         ->where('codigo_actividad','=',492331)
         ->first();
         //dd( $leyenda);
         $descrip_leyenda = $leyenda->descripcion_leyenda;

        //Datos del cliente
        $razonSocialCli =  $request->get('razon_social');
        //dd($razonSocialCli);
        if($request->get('nro_documento') == null ){
            $nroDocID = " ";
        }else{
            $nroDocID =$request->get('nro_documento');
        }
   
        $id_cliente = Clientes::where('nro_documento','=',$request->get('nro_documento'))->get();

        if($id_cliente->isEmpty()){
            $clie = new Clientes;
            $clie->nro_documento = $nroDocID;
            $clie->razon_social = $razonSocialCli;
            $clie->email = $request->get('email');
            $clie->save();
            $codCli1 = $clie->id;
        }
        else{
            $id_cliente = Clientes::where('nro_documento','=',$request->get('nro_documento'))->first();
            $codCli1 = $id_cliente->id;
            $id_cliente->razon_social =  $request->get('razon_social');
            $id_cliente->email = $request->get('email');
            $id_cliente->save();
        }

        $codMetodoPago = $request->get('id_tipo_pago');
        

        $codDocID = $request->get('id_tipo_documento');
    
        $complemento = $request->get('complemento'); //si no tiene en xml 
        
        //dd($complemento);
        $codCli = $codCli1;
        $usuario = $dato;
        $codMoneda1 = 1;
       
        // montos  TOTALGENERAL
        $total_final  = (double) str_replace(',', '', $request->get('total_detalle'));
        $tipoCambio = 1; //taza de cambio
        $descuentoAdicional = 0; //no cambia   
        $montoTotalMoneda =  $total_final;
        $montoTotal =  $total_final;
        $montoTotalSujetoIva =  $total_final;  

        //detalle productos
        $codigo_p = $request->get('codigo');//codigo_empresa
       
        $codigo_producto_p = $request->get('codigo_producto');
        $producto_datos = Productos::findOrFail($codigo_producto_p[0]);

        $codigo_impuestos = $producto_datos->codigo_producto;
        // $codigo_unidad_medida = $producto_datos->codigo_unidad_medida;
        // $descripcion_medidad = $producto_datos->unidad_medida;

        $cantidad_p = $request->get('cantidad');
        $unidad_medida_p = $request->get('unidad_medida');
        $id_unidad_medida_p = $request->get('codigo_unidad_medida'); // pendiente
        $descripcion_p = $request->get('descripcion');
        $precio_unitario_p = $request->get('precio_uni');
        $subtotal_p = $request->get('subtotal');

        $this->prueba =  $codigo_p;
        //dd($codigo_p , $this->prueba);

        //dd($razonSocialCli,$codDocID,$complemento,$codMetodoPago,$codigo_p, $codigo_producto_p,$cantidad_p,$unidad_medida_p,$id_unidad_medida_p,$descripcion_p, $precio_unitario_p, $subtotal_p, $total_final);


        //NO TOCAR FORMATO -- SI ES NECESARIO CAMBIAR NOMBRE DE VARIABLE
        //-----------------------------------------------------------------------------
        //  Fragmento de código para generar el XML a partir de los datos del formulario.

        $doc = new DOMDocument('1.0', 'utf-8');

        $doc->formatOutput = true;

        $xmlFactura = $doc->appendChild($doc->createElement('facturaComputarizadaTasaCero'));
        $xmlFactura->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        $cabecera = $xmlFactura->appendChild($doc->createElement('cabecera'));

        $cabecera->appendChild($doc->createElement('nitEmisor',$nitEmisor));
        $cabecera->appendChild($doc->createElement('razonSocialEmisor',$razonSocialEmisor));
        $cabecera->appendChild($doc->createElement('municipio',$municipio));
        $cabecera->appendChild($doc->createElement('telefono',$telefono));
        $cabecera->appendChild($doc->createElement('numeroFactura',$numFac));
        $cabecera->appendChild($doc->createElement('cuf',$cuf));
        $cabecera->appendChild($doc->createElement('cufd',$cufd_codigo));
        $cabecera->appendChild($doc->createElement('codigoSucursal',$codSucursal));
        $cabecera->appendChild($doc->createElement('direccion',$direccion));
        $cabecera->appendChild($doc->createElement('codigoPuntoVenta', auth()->user()->punto_venta));
        $cabecera->appendChild($doc->createElement('fechaEmision',$fecha_hora3));
        $cabecera->appendChild($doc->createElement('nombreRazonSocial',htmlspecialchars($razonSocialCli)));
        $cabecera->appendChild($doc->createElement('codigoTipoDocumentoIdentidad',$codDocID));
        $cabecera->appendChild($doc->createElement('numeroDocumento',$nroDocID));
        if($codDocID == 1  && strlen($complemento) > 0){
            $cabecera->appendChild($doc->createElement('complemento',$complemento));}
        else{
            $complemento = $cabecera->appendChild($doc->createElement('complemento'));
            $complemento->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
        }
            
        $cabecera->appendChild($doc->createElement('codigoCliente',$codCli));
        $cabecera->appendChild($doc->createElement('codigoMetodoPago',$codMetodoPago));
        if($codMetodoPago==2)
            $cabecera->appendChild($doc->createElement('numeroTarjeta',4074000000000559));
        else
        {
            $nroTarjeta = $cabecera->appendChild($doc->createElement('numeroTarjeta'));
            $nroTarjeta->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
        }        
        $cabecera->appendChild($doc->createElement('montoTotal',$montoTotal));
        $cabecera->appendChild($doc->createElement('montoTotalSujetoIva',0));
        $cabecera->appendChild($doc->createElement('codigoMoneda',$codMoneda1));
        $cabecera->appendChild($doc->createElement('tipoCambio',$tipoCambio));
        $cabecera->appendChild($doc->createElement('montoTotalMoneda',$montoTotalMoneda));       
        
         $montoGiftCard = $cabecera->appendChild($doc->createElement('montoGiftCard'));
         $montoGiftCard->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
        $cabecera->appendChild($doc->createElement('descuentoAdicional',$descuentoAdicional));
        if($validanit == 1 && $codDocID == 5){
            $cabecera->appendChild($doc->createElement('codigoExcepcion',1));
        }
        else{
            $codigoExcepcion = $cabecera->appendChild($doc->createElement('codigoExcepcion'));
            $codigoExcepcion->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
        }
      
        $cafc = $cabecera->appendChild($doc->createElement('cafc'));
        $cafc->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
        // $cabecera->appendChild($doc->createElement('cafc','101E2CA2AE82D'));        
        $cabecera->appendChild($doc->createElement('leyenda', $descrip_leyenda));
        $cabecera->appendChild($doc->createElement('usuario',$dato));
        $cabecera->appendChild($doc->createElement('codigoDocumentoSector',8));

        $cont = 0; 
                
        while($cont < count($codigo_p)){

            $cantidad = (double) str_replace(',', '', $cantidad_p[$cont]);
            $precio_u = (double) str_replace(',', '', $precio_unitario_p[$cont]);
            $subtotal = (double) str_replace(',', '', $subtotal_p[$cont]);

            $detalle = $xmlFactura->appendChild($doc->createElement('detalle'));

            $detalle->appendChild($doc->createElement('actividadEconomica',492331)); // codigo de actividad secundaria
            $detalle->appendChild($doc->createElement('codigoProductoSin', $codigo_impuestos));
            $detalle->appendChild($doc->createElement('codigoProducto',$codigo_p[$cont]));
            $detalle->appendChild($doc->createElement('descripcion',$descripcion_p[$cont]));
            $detalle->appendChild($doc->createElement('cantidad',number_format($cantidad, 2, '.', '')));
            $detalle->appendChild($doc->createElement('unidadMedida',$id_unidad_medida_p[$cont]));
            $detalle->appendChild($doc->createElement('precioUnitario',number_format($precio_u, 2, '.', '')));
            $detalle->appendChild($doc->createElement('montoDescuento',0));
            $detalle->appendChild($doc->createElement('subTotal',round($subtotal, 2)));


            $cont++;
        }        

        //para guardar storage  
        $dir = storage_path('facturasT/linea'.$dato.'.xml');
        $doc->save($dir);  
        //dd($doc);             

        //METODO 
        // ----------------------------------------------------------------------------------
        // Firmado del archivo xml desde php

        //buscar valores corregir
        $modalidad = "2"; //modalidad de electronica en linea
        $codTipoFac = 1; //tipo de factura que se envia 

        $wsdl = $this->fac_compra_venta;
        $token =  $this->token;

        if($tipoEmisionN == 2){
            $i = 0;
            $dir1 = storage_path('/facturasT/lineaF'.$dato.'.xml');
            $doc->save($dir1);
            for($i=1;$i<=500;$i++){
                $dir = storage_path('/facturasT/lineaF'.$dato.$i.'.xml');
                if(!file_exists($dir)){
                    $doc->save($dir);
                    //dd($dir);
                    break;
                }
               
            }
            //dd($i);
            
                $codigo_verifica_des  ="FACTURA EMITIDA FUERA DE LINEA";
        }else{
            
            // Guarde el XML firmado 
            $doc->save(storage_path('/facturasT/lineaF'.$dato.'.xml'));
            
            //---------------------------------------------------------------------
            //  Fragmento de código para comprimir en un gzip el XML antes generado
            $origen =  storage_path('/facturasT/lineaF'.$dato.'.xml');
            $dest = storage_path('/facturasT/lineaF'.$dato.'.zip');
            $fp = fopen($origen, "r");
            $data = fread ($fp, filesize($origen));
            fclose($fp);
            $zp = gzopen($dest, "w9");
            gzwrite($zp, $data);
            gzclose($zp);

            // ------------------------------------------------------------------------
            // Encriptamos en un hash con SHA-256
            $hash = hash_file("sha256",storage_path('/facturasT/lineaF'.$dato.'.zip'), $raw_output = false);
            $archivoArray = file(storage_path('/facturasT/lineaF'.$dato.'.zip'));
            $archivo = implode($archivoArray);

            //----------------------------------------------------------------------------
            // Envío de la factura al servicio RecepcionFactura
            $cuis=$this->cuis_usuario();
            try {
                $client1 = new \SoapClient($wsdl, [ 
                    'stream_context' => stream_context_create([ 
                        'http'=> [ 
                            'header' => "apikey: TokenApi $token",  
                        ] 
                    ]),
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
                ]);
                $solicitudRecepcionFactura = array(
                    'codigoAmbiente'        => $this->codigo_ambiente,
                    'codigoDocumentoSector' => 8,
                    'codigoEmision'         => $tipoEmisionN,
                    'codigoModalidad'       => $modalidad,
                    'codigoPuntoVenta'      => auth()->user()->punto_venta,
                    'codigoSistema'         => $this->codigo_sistema,
                    'codigoSucursal'        => auth()->user()->id_sucursal,
                    'cufd'                  => $cufd_codigo,
                    'cuis'                  => $cuis->codigo_cuis,
                    // 'cuis'                  => 'EED58C49',
                    'nit'                   => $this->nit,
                    'tipoFacturaDocumento'  => 2,
                    'archivo'               => $archivo,
                    'fechaEnvio'            => $fecha_hora3,
                    
                    'hashArchivo'           => $hash,
                );
                // dd($client1->__getTypes());
        
                $recepFac = $client1->recepcionFactura(
                    array(
                        "SolicitudServicioRecepcionFactura" => $solicitudRecepcionFactura,
                    )
                );
                //dd($recepFac, $codSector1.' codigo_sector',$tipoEmisionN.' tipo emision',$modalidad.' modalidad',auth()->user()->punto_venta.' punto venta',auth()->user()->id_sucursal.' id sucursal',$cufd_codigo.' cufd_codigo',$codTipoFac.' codigoTipoFac',$cuis->codigo_cuis.' cuis');
                $respuesta = $recepFac->{'RespuestaServicioFacturacion'};
                $descrip = $respuesta->{'codigoDescripcion'};
                
                $codigo_verifica_fac = $respuesta->{'codigoEstado'};
                
                if( $codigo_verifica_fac != 902){
                   
                    $solicitudServicioVerificacionEstadoFactura = array(
                        'codigoAmbiente'        => $this->codigo_ambiente,
                        'codigoDocumentoSector' => 8,
                        'codigoEmision'         => $tipoEmisionN,
                        'codigoModalidad'       => $modalidad,
                        'codigoPuntoVenta'      => auth()->user()->punto_venta,
                        'codigoSistema'         => $this->codigo_sistema,
                        'codigoSucursal'        => auth()->user()->id_sucursal,
                        'cufd'                  => $cufd_codigo,
                        'cuis'                  => $cuis->codigo_cuis,
                        // 'cuis'                  => 'EED58C49',
                        'nit'                   => $this->nit,
                        'tipoFacturaDocumento'  => 2,
                        'cuf'                   => $cuf,
                    );
        
                    $verificarFac = $client1->verificacionEstadoFactura(
                        array(
                            "SolicitudServicioVerificacionEstadoFactura" => $solicitudServicioVerificacionEstadoFactura,
                        )
                    );
        
                    $verires = $verificarFac->{'RespuestaServicioFacturacion'};
                    $codigo_verifica_fac = $verires->{'codigoEstado'};
                    $codigo_verifica_des = $verires->{'codigoDescripcion'};
                }
                else{
                    $detalledescrip = $respuesta->{'mensajesList'};
                    $detalled = $detalledescrip->{'descripcion'};
                    $codigo_verifica_des =  $descrip.' '.$detalled; 
                }
            } catch (SoapFault $e) {
                return redirect('facturacionT/create')->withInput()->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: '.$e);
            }

                        
        }    
        //FIN DE METODO         
        //VERIFICANDO SI LA FACTURA EMITIDA ES VALIDA  Y EN LINEA PARA GUARDAR EN BD
        if(($tipoEmisionN == 1  && $codigo_verifica_fac == 690) || $tipoEmisionN == 2 ){
            $factura_bd = new FacturaTasaCero;

            //DATOS GUARDADOS EN BD TABLA FACTURA
            
            $fechabd =  str_replace('T', ' ', $fecha_hora3);
            $fecha_hora1 = substr($fechabd, 0, -4);
            $fecha1 = substr($fecha_hora1, 0, -9);
            if($request->get('manual') == 1){
                $factura_bd->nro_fac_manual = $numFac;    
            }else{
                $factura_bd->id_factura = $numFac;     
            }
            $factura_bd->id_sucursal = auth()->user()->id_sucursal;
            $factura_bd->punto_venta = auth()->user()->punto_venta;
            $factura_bd->fecha = $fecha1;
            $factura_bd->fecha_hora = $fecha_hora1;
            $factura_bd->razon_social = $razonSocialCli;
            $factura_bd->cuf = $cuf;
            $factura_bd->cufd = $cufd_codigo;
            $factura_bd->tipo_documento_identidad = $codDocID; 
            $factura_bd->nro_documento = $nroDocID;

            $factura_bd->complemento = $request->get('complemento');
            
            $factura_bd->codigo_cliente = $codCli;
           
            $factura_bd->id_metodo_pago = $codMetodoPago;
            //$factura_bd->nro_tarjeta = $nro_tarjeta;
            //$factura_bd->codigo_moneda = $codMoneda1; // todo es bolivianos
            $factura_bd->tipo_emision_n = $tipoEmisionN;
            $factura_bd->id_leyenda = $leyenda->id;

            $factura_bd->monto_total = $total_final;
            $factura_bd->monto_total_sujeto_iva =  $total_final;
            //$factura_bd->tipo_cambio = $tipoCambio; todo es en bolivianos
            $factura_bd->monto_total_moneda =   $total_final ; //suma de gastos nacionales + suma del total detalle -- particularmente el dato es igual al total detalle por que fob es cero
           
            $factura_bd->codigo_excepcion = $validanit ;
            $factura_bd->descuento_adicional = 0 ; // no cambia
            $factura_bd->cafc = $request->get('codigo_cafc'); //no cambia - no se emitira manuales
            
            //$factura_bd->codigo_documento_sector =  $codSector1 ;// no cambia
            $factura_bd->id_usuario = $dato;

            $factura_bd->hora_impuestos = $fecha_hora3;
            
            if($tipoEmisionN == 2 && (int)$request->get('linea') == 0){
                $factura_bd->fuera_linea = 1;
            }
            if($tipoEmisionN == 2 && (int)$request->get('manual') == 1){
                $factura_bd->fac_manual = 1;
                $factura_bd->fuera_linea = 0;
            }
            $factura_bd->save();

           //variables necesarias declaradas mas arriba           
           $cont = 0; 
           //dd($factura_bd->id);
                
           while($cont < count($codigo_p)){

                $cantidad = (double) str_replace(',', '', $cantidad_p[$cont]);
                $precio_u = (double) str_replace(',', '', $precio_unitario_p[$cont]);
                $subtotal = (double) str_replace(',', '', $subtotal_p[$cont]);
                
                $producto_detalle = new FacturaTasaCeroDetalle;
                $producto_detalle->id_tabla_factura = $factura_bd->id;
                $producto_detalle->id_factura = $numFac;
                $producto_detalle->id_sucursal = auth()->user()->id_sucursal;
                $producto_detalle->punto_venta =auth()->user()->punto_venta;

                $producto_detalle->codigo_producto_sin = $codigo_producto_p[$cont];
                $producto_detalle->codigo_producto_empresa =$codigo_p[$cont];
                $producto_detalle->descripcion = $descripcion_p[$cont];
                $producto_detalle->unidad_medida_des = $unidad_medida_p[$cont];
                $producto_detalle->cantidad = $cantidad;
                $producto_detalle->precio = $precio_u;
 
                $producto_detalle->subtotal = $subtotal;
     
                $producto_detalle->save();  

                $cont++;
            }

            $codigo_verifica_des = 'REGISTRO GUARDADO CON EXITO - '.$codigo_verifica_des;
            $tipo_fac = "lineaF";
            $pdf = $this->pdf_en_servidor($factura_bd->id);
            $correo = $this->correo($factura_bd->id,$tipo_fac);

        }else{
            $codigo_verifica_des = 'REGISTRO NO SE GUARDO - '.$codigo_verifica_des;
        }
        //dd($verificarFac);
        
        return redirect('facturacionT')->with('status', $codigo_verifica_des);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //
    }
}
