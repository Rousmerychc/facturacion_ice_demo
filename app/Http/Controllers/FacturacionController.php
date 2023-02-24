<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use SoapFault;
use App\Datos;
use DOMDocument;

use App\Facturacion;
use App\FacturaDetalle;

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
use App\Grupo;
use App\Grupo2;
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

use App\Mail\FacEnviada;
use App\Mail\FacAnulada;

class FacturacionController extends Controller
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
        $this->fac_compra_venta = $datos_em->fac_compra_venta;
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

    //funcion si verifica si es cliente para la vista
    public function id_cliente(Request $request)    {
        $id=$request->get('dato');
        $cliente11 = Clientes::where('id','=',$id)->first();
        return response(json_encode(array('cliente11' =>$cliente11)),200)->header('Content-type','text/plain');
        
    }

    //FUNCION AJAX PARA EL INDEX
    public function ajaxfactura(){
        
        $data = Facturacion::where('id_sucursal','=',auth()->user()->id_sucursal)
        ->where('punto_venta','=',auth()->user()->punto_venta)
        ->where('nro_nota_venta', '=',0)
        
        ->orderBy('id', 'DESC')->get();
        return Datatables::of($data)
            ->addColumn('btn','facturacion.actions')
            ->addColumn('pdf','facturacion.pdf')
            ->rawColumns(['btn','pdf'])
            ->make(true);
        
    }
    //FUNCION ENVIO CORREO
    public function correo_anular($id){
        $msj = "hola";
        
        $factura = Facturacion::findOrFail($id);
       
       
        $cliente = Clientes::where('id', '=', $factura->codigo_cliente)->first();
        $email = $cliente->email;

        if($email == null){
            $cliente->email= "sibelis.fact@gmail.com";
        }
       
        Mail::to($cliente->email)->send(new FacAnulada($factura));
       
        return;
    }
    //FUNCION ENVIO CORREO
    public function correo($id,$tipo_fac){
        $msj = "hola";
        
        $factura = Facturacion::findOrFail($id);
        $cliente = Clientes::where('id', '=', $factura->codigo_cliente)->first();
        $email = $cliente->email;
        if($email == null){
            $cliente->email = "sibelis.fact@gmail.com";
        }
        
        //dd($cliente->email);
        
        Mail::to($cliente->email)->send(new FacEnviada($tipo_fac));
        
        return;
    }
    //FUNCION PARA GENERAR QR PARA EL PDF
    public function codigoQR($id){

        $datos_empresa = Datos::first();

        $factura = Facturacion::findOrFail($id);
        //dd($factura);

        if($factura->id_factura == 0){
            $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->nro_fac_manual.'&t=2';
            // $url = 'https://siat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->nro_fac_manual.'&t=2';

        }else{
            $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id_factura.'&t=2';
            // $url = 'https://siat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id_factura.'&t=2';
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

        $factura = Facturacion::findOrFail($id);
        

        if($factura->id_factura == 0){
            $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->nro_fac_manual.'&t=2';
            // $url = 'https://siat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->nro_fac_manual.'&t=2';

        }else{
            $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id_factura.'&t=2';
            // $url = 'https://siat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id_factura.'&t=2';
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
        $pv = auth()->user()->punto_venta;
        $s = auth()->user()->id_sucursal;

        $datos_empresa = Datos::first();
        $factura = Facturacion::where('factura.id','=',$id)
        ->join('sucursal','sucursal.nro_sucursal','=','factura.id_sucursal')
        ->select('factura.*','sucursal.municipio','sucursal.direccion','sucursal.telefono','sucursal.descripcion')
        ->first();

        // dd($factura);
    
        $leyenda2 =  LeyendasFacturacion::findOrFail($factura->id_leyenda);         
        
        $detalle = FacturaDetalle::where('id_tabla_factura','=',$factura->id)
        ->join('productos','productos.id', '=','codigo_producto_empresa')
        ->select('factura_detalle.*','productos.id')
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
        $tipo = 1;
        if($reb==1){
            $literalb = 'UN '.$literalb;
        }
        
        $data =['factura'=>$factura, 'detalle'=>$detalle,  'datos_empresa'=>$datos_empresa ,
                 'qr'=>$qr , 'leyenda2'=> $leyenda2, 'leyenda3'=>$leyenda3, 'literalb'=>$literalb, 'tipo' => $tipo,
                ];
        
      
        
            $pdf = PDF::loadView('facturacion.pdf_cliente',$data);
     
        $pdf->setPaper("letter", "portrait");
        $pdf->save(storage_path('/facturas/factura'.$s.$pv.'.pdf'));
        //return $pdf->stream('Factura.pdf');
        
    }
   
    //FUNCION PARA GENERAR PDF PARA LOS CLIENTES
    public function pdf_clientes($id, $tipo){
        //dd($tipo);
        $datos_empresa = Datos::first();
        $factura = Facturacion::where('factura.id','=',$id)
        ->join('sucursal','sucursal.nro_sucursal','=','factura.id_sucursal')
        ->select('factura.*','sucursal.municipio','sucursal.direccion','sucursal.telefono','sucursal.descripcion')
        ->first();
        //dd($factura);

        // dd($factura);
    
        $leyenda2 =  LeyendasFacturacion::findOrFail($factura->id_leyenda);         
        
        $detalle = FacturaDetalle::where('id_tabla_factura','=',$factura->id)
        ->join('productos','productos.id', '=','codigo_producto_empresa')
        ->select('factura_detalle.*','productos.id')
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
        //   dd($tipo);
        $data =['factura'=>$factura, 'detalle'=>$detalle,  'datos_empresa'=>$datos_empresa ,
                 'qr'=>$qr , 'leyenda2'=> $leyenda2, 'leyenda3'=>$leyenda3, 'literalb'=>$literalb, 'tipo'=>$tipo];
        $pdf = PDF::loadView('facturacion.pdf_cliente',$data); 
        //return view('facturacion.pdf_vista',$data);
        $pdf->setPaper("letter", "portrait");
        return $pdf->stream('Factura.pdf');
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
            $codigo_comunicacion = $res1->{'codigo'};
            $respuesta = $res1->{'descripcion'};
            //dd($respuesta);
             return($codigo_comunicacion);
        } catch(SoapFault $e) {
            //dd('entro');
            //return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: SE EMITA LA FACTURA FUERA DE LINEA ');
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
            $factura = Facturacion::findOrFail($request->{'id_factura'});
    
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
                'codigoDocumentoSector' => 14,
                'codigoEmision'         => 1, //$factura->tipo_emision_n -- siempre sera 1
                'codigoModalidad'       => $this->modalidad,
                'codigoPuntoVenta'      => auth()->user()->punto_venta, //por usuario
                'codigoSistema'         => $this->codigo_sistema,
                'codigoSucursal'        =>  auth()->user()->id_sucursal, // por usuario
                'cufd'                  => $cufd->codigo_cufd,
                'cuis'                  => $cuis->codigo_cuis, //por usuario
                // 'cuis'                  => 'EED58C49',
                'nit'                   => $this->nit,
                'tipoFacturaDocumento'  => 1,
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
            return redirect('facturacion')->with('status', $descrip);
        }   catch(SoapFault $e) {
           
            return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE ANULACION DE FACTURA -  '. $e);
            //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
        }
       
       // dd($anularFac);
    }
   
    //funcion para borrar archibos de envio por paquete
    function pruebaborrado($tipo_fac){
        // Borramos los xml que se crearon y tambien el archivo .tar.gz
        $dato = auth()->user()->id;
        $pv = auth()->user()->punto_venta;
        $s = auth()->user()->id_sucursal;

        $i = 0;
        for($i=1;$i<=500;$i++){
            $dir = storage_path('/facturas/'.$tipo_fac.'F'.$s.$pv.$i.'.xml');
            if(file_exists($dir)){
                unlink($dir);
            }
            else{
                break;
            }
        }

        $dir = storage_path('/facturas/miprueba'.$s.$pv.'.tar');
        $dirgz = storage_path('/facturas/miprueba'.$s.$pv.'.tar.gz');
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
        $pv = auth()->user()->punto_venta;
        $s = auth()->user()->id_sucursal;

        $dir = storage_path('/facturas/miprueba'.$s.$pv.'.tar');
        $dirgz = storage_path('/facturas/miprueba'.$s.$pv.'.tar.gz');
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
       //dd($nitEmisor,$codPtoVenta,$codSucursal);
        // convertimos a cadena y completamos la longitud de cada variable según anexo técnico
        $nit = "$nitEmisor";
        $fecha = preg_replace("/[^0-9]/", "", $fecha_hora3);
        $sucursal = "$codSucursal";
        $modalidad = "2";
        $tipoEmision = "$tipoEmisionN";
        $tipoFac = "1";
        $tipoDocSector = "14";
        $numFac = $nroFactura;
        $ptoVenta = "$codPtoVenta";
        $cero = "0";

        $sw1 = 0; $sw2 = 0; $sw3 = 0; $sw4 = 0; $sw5 = 0; $sw6 = 0;

        while($sw1==0 || $sw2==0 || $sw3==0 || $sw4==0 || $sw5==0 || $sw6==0){
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
             if(strlen($fecha)<17){ $fecha = $fecha.$cero; } 
             else{ $sw6 = 1; }
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

    public function ultimo_cufd(){
        $dato = auth()->user()->id;
        $id_sucu = auth()->user()->id_sucursal;
        $punto_v = auth()->user()->punto_venta;
        
        $fecha_hora =$this->fechaHora(true);

        $cufd_bd = Cufd::where('id_usuario','=',$dato)
        ->where('id_sucursal','=',$id_sucu)
        ->where('punto_venta','=',$punto_v)
        ->where('fecha_hora','>',$fecha_hora)
        ->orderBy('id','DESC')
        ->first();
        $cufd = $cufd_bd->codigo_cufd;
        
        return $cufd; 
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
        $id_sucu = $s =auth()->user()->id_sucursal;
        $punto_v = $pv = auth()->user()->punto_venta;

        $datos_empresa = Datos::first();

        $factura_c = Facturacion::where('fuera_linea','=',1)
        ->where('id_sucursal','=',$id_sucu)
        ->where('punto_venta','=',$punto_v)
        ->get();
        $factura_c1 = Facturacion::where('fuera_linea','=',1)
        ->where('id_sucursal','=',$id_sucu)
        ->where('punto_venta','=',$punto_v)
        ->first();
        //
        
        if($factura_c->isEmpty()){
            //return redirect('facturacion')->with('status', 'NO HAY PAQUETES PARA ENVIAR');
            return 1 ;
        }else{
            $factura = Facturacion::where('fuera_linea','=',1)
            ->where('id_sucursal','=',$id_sucu)
            ->where('punto_venta','=',$punto_v)
            ->select('id','hora_impuestos','cufd')
            ->orderby('id')
            ->first();

            //dd($factura);
            $horaIni = $factura->hora_impuestos;

            $num_fac1 = DB::select(DB::raw('SELECT COUNT(*) as num, cufd FROM factura 
            WHERE factura.fuera_linea = 1 AND factura.id_sucursal = ? AND factura.punto_venta = ? GROUP BY cufd'),[$id_sucu,$punto_v]);
            $num_fac=$num_fac1[0]->{'num'};
            $cufd_ant = $num_fac1[0]->{'cufd'};
            //dd($num_fac1);
            $cuis = $this->cuis_usuario();
            $fecha_hora3 = $this->fechaHora(true);
            //dd($fecha_hora3);
            //--------------------------------------------------------------------------
            //comprimir el paquete de facturas en el archivo .tar.gz para el envío por paquetes
            $dir = storage_path('/facturas/miprueba'.$s.$pv.'.tar');
            if(file_exists($dir) == false){            
                
                $p = new \PharData($dir);
                $i = 1;
                for( $i = 1; $i <= 500; $i++){
                    $origen = storage_path('/facturas/lineaF'.$s.$pv.$i.'.xml');
                    if(file_exists($origen))
                    {
                        // echo ("existe $origen");
                        $factura = file_get_contents($origen);
                        $p['factura'.$s.$pv.$i.'.xml'] = $factura;
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

                $archivoArray = file(storage_path('/facturas/miprueba'.$s.$pv.'.tar.gz'));
                $hash = hash_file("sha256",storage_path('/facturas/miprueba'.$s.$pv.'.tar.gz'), $raw_output = false);
                $archivo = implode($archivoArray);
            }

            
            //dd($factura_c);
            if($factura_c1->codigo_evento == 0){
                //var_dump('entro al evento');
                //nuevo
                $cufd1 = $this->cufd();
                if(is_soap_fault($cufd1)){
                    $this->borradoarchivos();
                    return ('OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: NO SE PUDO SOLISITAR UN NUEVO CUFD PARA EL ENVIO DE PAQUETE: '.$cufd1 );
                }
                $cufd = $cufd1->codigo_cufd;
                //dd($cufd1);

                try {
                    
                    $wsdlOperaciones= $this->fac_operaciones;
                    $token = $this->token;
                    $wsdl = $this->fac_compra_venta;
                    $codigo_ambiente = $this->codigo_ambiente;
                    $codigo_sistema = $this->codigo_sistema;
                    $nit_empresa = $this->nit;

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
                // var_dump($cufd, $cufd_ant);

                    $solicitudEventoSignificativo = array(
                        'codigoAmbiente'        => $codigo_ambiente,
                        'codigoMotivoEvento'    => 2,
                        'codigoPuntoVenta'      => $punto_v,
                        'codigoSistema'         => $codigo_sistema,
                        'codigoSucursal'        => $id_sucu,
                        'cufd'                  => $cufd,
                        'cufdEvento'            => $cufd_ant,
                        'cuis'                  => $cuis->codigo_cuis,
                        // 'cuis'                  => 'EED58C49',
                        'descripcion'           => 'INACCESIBILIDAD AL SERVICIO WEB DE LA ADMINISTRACIÓN TRIBUTARIA',
                        'fechaHoraFinEvento'    => $fecha_hora3,
                        'fechaHoraInicioEvento' => $horaIni,
                        'nit'                   => $nit_empresa,
                    );

                    $objEventoSignificativo = $clientOperaciones->registroEventoSignificativo(
                        array(
                            "SolicitudEventoSignificativo" => $solicitudEventoSignificativo,
                        )
                    );
                    
                    // var_dump($objEventoSignificativo,$horaIni,$fecha_hora3);
                    //dd($objEventoSignificativo,$horaIni,$fecha_hora3);
                    $respEventos = $objEventoSignificativo->{'RespuestaListaEventos'};
                    $evento = $respEventos->{'codigoRecepcionEventoSignificativo'};
                    $res = $respEventos->{'transaccion'};
                    if($res == true){
                        $fac = Facturacion::where('fuera_linea','=',1)
                        ->where('id_sucursal','=',$id_sucu)
                        ->where('punto_venta','=',$punto_v)
                        ->get();
                        foreach($fac as $fac){
                            $fac->codigo_evento = $evento;
                            $fac->save();
                        }
                    }else{
                        $res1 = $respEventos->{'mensajesList'};
                        $evento  = $res1->{'descripcion'};
                        return ( 'OCURRIO UN INCONVENIENTE EN EVENTO SIGNIFICATIVOs -  '. $evento);
                    }

                } catch(SoapFault $e) {
                    //dd('entro');
                    $this->borradoarchivos();
                    return ( 'OCURRIO UN INCONVENIENTE EN EVENTO SIGNIFICATIVOs -  '. $e);
                    //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
                }
                catch(exception $e) {
                    //dd('entro');
                $this->borradoarchivos();
                    return ('OCURRIO UN INCONVENIENTE EN EVENTO SIGNIFICATIVOS -  '. $e);
                    //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
                }
            }
            
           
            // ------------------------------------------------------------------------
            // Envío de factura por paquetes
           
            if($factura_c1->codigo_recepcion == 0){
              // var_dump('entro a codigo recepcion');
                if($factura_c1->codigo_evento != 0){
                    $evento = $factura_c1->codigo_evento;
                }
                try {

                    $wsdlOperaciones= $this->fac_operaciones;
                    $token = $this->token;
                    $wsdl = $this->fac_compra_venta;
                    $codigo_ambiente = $this->codigo_ambiente;
                    $codigo_sistema = $this->codigo_sistema;
                    $nit_empresa = $this->nit;
                    $cufd = $this->ultimo_cufd();

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
                        'codigoAmbiente'        => $codigo_ambiente,
                        'codigoDocumentoSector' => 14,
                        'codigoEmision'         => 2,
                        'codigoModalidad'       => 2, //pendiente
                        'codigoPuntoVenta'      => $punto_v,
                        'codigoSistema'         => $codigo_sistema,
                        'codigoSucursal'        => $id_sucu,
                        'cufd'                  => $cufd,
                        'cuis'                  => $cuis->codigo_cuis,
                        // 'cuis'                  => 'EED58C49',
                        'nit'                   => $nit_empresa,
                        'tipoFacturaDocumento'  => 1,
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
                    // dd($recepPaq);
                    $servicioPaq = $recepPaq->{'RespuestaServicioFacturacion'};
                    $codDescrip = $servicioPaq->{'codigoDescripcion'};
                    $codEstado = $servicioPaq->{'codigoEstado'};
                    if($codEstado == 901){
                        $codRecep = $servicioPaq->{'codigoRecepcion'};
                        $fac = Facturacion::where('fuera_linea','=',1)
                        ->where('id_sucursal','=',$id_sucu)
                        ->where('punto_venta','=',$punto_v)
                        ->get();
                        foreach($fac as $fac){
                            $fac->codigo_recepcion = $codRecep;
                            $fac->save();
                        }
                        //dd('entro codifgo recepcion');
                    }else{
                        $this->borradoarchivos();
                        $mensajelist = $servicioPaq->{'mensajesList'};
                        
                        if (is_array($mensajelist)) {
                            $cont = 0;
                            $descripcion_error = "";
                            while($cont < count($mensajelist)){
                                $mensaje = $mensajelist[$cont];
                                $descripcion_error = $descripcion_error. ($cont+1).' : ' .$mensaje->{'codigo'} .' - '.$mensaje->{'descripcion'}.' *** ';
                                $cont++;
                            } 
                        }else{
                            $codEstado =  $mensajelist->{'codigo'};
                            $codDescrip = $mensajelist->{'descripcion'};
                            $descripcion_error = $codEstado. '-'.$codDescrip;
                        }
                        return ('OCURRIO UN INCONVENIENTE AL ENVIAR PAQUETE -  '. $descripcion_error);
                    }
                } catch(SoapFault $e) {
                    //dd('entro');
                    $this->borradoarchivos();
                    return ('OCURRIO UN INCONVENIENTE AL ENVIARPAQUETE EL PAQUETE -  '. $e);
                   
                }
                catch(exception $e) {
                    //dd('entro');
                $this->borradoarchivos();
                    return ('OCURRIO UN INCONVENIENTE AL ENVIARPAQUETE EL PAQUETE -  '. $e);
                   
                }
            }else{
              
                $codRecep = $factura_c1->codigo_recepcion;

            }
            //dd('entro3',$codRecep);
           
            //dd($recepPaq);
                // --------------------------------------------------------------------------
                // Validar la recepcion del paquete de facturas
                try {
                    $wsdlOperaciones= $this->fac_operaciones;
                    $token = $this->token;
                    $wsdl = $this->fac_compra_venta;
                    $codigo_ambiente = $this->codigo_ambiente;
                    $codigo_sistema = $this->codigo_sistema;
                    $nit_empresa = $this->nit;
                    $cufd = $this->ultimo_cufd();

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
                    'codigoAmbiente'        => $codigo_ambiente,
                    'codigoDocumentoSector' => 14,
                    'codigoEmision'         => 2,
                    'codigoModalidad'       => 2, //pendiente
                    'codigoPuntoVenta'      => $punto_v,
                    'codigoSistema'         => $codigo_sistema,
                    'codigoSucursal'        => $id_sucu,
                    'cufd'                  => $cufd,
                    'cuis'                  => $cuis->codigo_cuis,
                    // 'cuis'                  => 'EED58C49',
                    'nit'                   => $nit_empresa,
                    'tipoFacturaDocumento'  => 1,
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

                 //dd($servicioPaq);

                if($codEstado == 908 || $codEstado == 901){
                    $fac = Facturacion::where('fuera_linea','=',1)
                    ->where('id_sucursal','=',$id_sucu)
                    ->where('punto_venta','=',$punto_v)
                    ->get();
                    foreach($fac as $fac){
                        $fac->fuera_linea = 0;
                        $fac->save();
                    }
                    $tipo_fac = "linea";
                    $bora = $this->pruebaborrado($tipo_fac);
                    $sw_p = 0;
                    while($sw_p ==0 && $codEstado == 901){
                        sleep(3);
                        $pendiente = $this->pendiente($codRecep);
                        $codDescrip = $pendiente[1];
                        $codEstado = $pendiente[0];
                    
                        if($codEstado == 908){
                            $sw_p = 1;
                        }
                    }
                    
                    return redirect('facturacion')->with('status','ENVIO DE PAQUETES - '.$codDescrip);
                    //return 1;
                }else{
                    return ('ERROR EN ENVIO DE PAQUETE '.$codDescrip);
                }
                
            } catch(SoapFault $e) {
                //dd('entro');
                $this->borradoarchivos();
                return  ('OCURRIO UN INCONVENIENTE EN ENVIARPAQUETE Y/O VALIDAR EL PAQUETE -  '. $e);
                //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            }
            catch(exception $e) {
                //dd('entro');
            $this->borradoarchivos();
                return  ('OCURRIO UN INCONVENIENTE EN ENVIARPAQUETE Y/O VALIDAR EL PAQUETE -  '. $e);
                //throw new SoapFault("https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl", "asaasdasd");
            }
            // ------------------------------------------------------------------------ 
        }
    }
   
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
                'codigoDocumentoSector' => 14,
                'codigoEmision'         => 2,
                'codigoModalidad'       => $this->modalidad,
                'codigoPuntoVenta'      => $punto_v,
                'codigoSistema'         => $this->codigo_sistema,
                'codigoSucursal'        => $id_sucu,
                'cufd'                  => $cufd->codigo_cufd,
                'cuis'                  => $cuis->codigo_cuis,
                // 'cuis'                  => 'EED58C49',
                'nit'                   => $this->nit,
                'tipoFacturaDocumento'  => 1,
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
    
    //producto por grupo
    public function porducto_grupo(Request $request){
        $id=$request->get('dato');
        $dato_sucu = auth()->user()->id_sucursal;
        $prueba = Productos::join('productos_precio','productos_precio.id_producto','=','productos.id')
                    ->where('productos.id_grupo','=',$id)
                    ->where('productos_precio.id_sucursal','=',$dato_sucu)
                    ->select('productos.*','productos_precio.precio1','productos_precio.precio2','productos_precio.precio3')
                    ->get();
 
        return response(json_encode(array('prueba'=>$prueba)),200)->header('Content-type','text/plain');
    }

    //FUNCION SEGUNDO PLANO PARA AGREGAR PRODUCTO
    public function producto_fac(Request $request){
        $p=$request->get('dato');
        $dato_sucu = auth()->user()->id_sucursal;
        $prueba = Productos::join('grupos','grupos.id','=','productos.id_grupo_porcentual')
                    ->join('productos_precio','productos_precio.id_producto','=','productos.id')
                    ->where('productos.id','=',$p)
                    ->where('productos_precio.id_sucursal','=',$dato_sucu)
                    ->select('productos.*','grupos.ice_porcentual','grupos.ice_especifico','productos_precio.precio1','productos_precio.precio2','productos_precio.precio3', 'productos_precio.precio_unitario1','productos_precio.precio_unitario2','productos_precio.precio_unitario3')
                    ->first();
 
        return response(json_encode(array('prueba'=>$prueba)),200)->header('Content-type','text/plain');
     }
    
     //FUNCIONES DE MANEJO DE PAGINA
    
     public function index(){
        $evento_significativo = ParametricasEventosSignificativos::where('paquete_manual','=',1)->get();
        $evento_significativo2 = ParametricasEventosSignificativos::where('paquete_manual','=',0)->get();
        $motivo_anulacion = ParametricaMotivoAnulacion::all();
        return view('facturacion.index',['motivo_anulacion'=>$motivo_anulacion, 'evento_significativo' =>$evento_significativo, 'evento_significativo2' =>$evento_significativo2]);
    }
     public function create(){   

        $conexion = $this->prueba_veri_conec();
        $conexion2 =$this->verificarComunucacion();
        //dd($conexion);
        $datos_paquete = Datos::first();
        $envio_paquete_sw = $datos_paquete->envio_paquete;

        if($envio_paquete_sw == 1){
            return redirect('facturacion')->with('status', 'SE ESTA ENVIANDO UN PAQUETE DE FACTURA FUERA DE LINEA, ESPERE UN MOMENTO Y VUELVA A PRESIONAR NUEVA FACTURA');
        }

        if($envio_paquete_sw != 1){
            if($conexion == 1 && $conexion2 == 926){
                $envio_paquete = $this->emisionFueraLinea();
                if (is_string($envio_paquete)) {
                    return redirect('facturacion')->with('status', 'OCURRIO UN INCONVENIENTE AL ENVIAR PAQUETE DE FACTURAS FUERA DE LINEA -  '. $envio_paquete);
                }
            }
        }

        $dato_estado_descuento = auth()->user()->estado_descuento;

        $clientes = Clientes::all();
        $grupos = Grupo2::all();
        $tipo_doc = ParametricaDocumentoTipoIdentidad::all();
        $fecha=Carbon::now(-4)->format('Y-m-d');
        $tipo_pago = ParametricaTipoMetodoPago::where('estado','=',1)->get();
       
        $id_fac = Facturacion::where('id_sucursal','=',auth()->user()->id_sucursal)
        ->where('punto_venta','=',auth()->user()->punto_venta)
        ->where('nro_nota_venta', '=',0)
        ->where('nro_fac_manual', '=',0)
        ->orderby('id','DESC')
        ->get();
        if($id_fac->isEmpty()){
            $id_fac->id_factura = 0;
        }else{
            $id_fac = Facturacion::where('id_sucursal','=',auth()->user()->id_sucursal)
            ->where('punto_venta','=',auth()->user()->punto_venta)
            ->where('nro_nota_venta', '=',0)
            ->where('nro_fac_manual', '=',0)
            ->orderby('id','DESC')
            ->first();
        }
        $sucu = Sucursal::where('nro_sucursal','=',auth()->user()->id_sucursal)
        ->first();
        // dd($sucu);
         return view('facturacion.facturacion',['id_fac'=>$id_fac, 'sucu'=>$sucu,'tipo_doc'=>$tipo_doc, 'fecha'=>$fecha, 'tipo_pago'=>$tipo_pago, 'grupos'=>$grupos, 'clientes'=>$clientes, 'dato_estado_descuento'=> $dato_estado_descuento]);     
    }
    
    public function store(Request $request)
    {
      
        $conexion = $this->prueba_veri_conec();
        //dd($conexion);
        //     if($conexion == 0){
        //         return redirect('facturacion')->with('status', 'NO HAY CONEXION A SERVICIO DE IMPUESTOS INTERNOS');
        //     }
            
        //NUMERO DE FACTURA QUE SE ENVIARA EN LINEA Y FUERA DE LINEA

        $factura = Facturacion::where('id_sucursal','=',auth()->user()->id_sucursal)
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
        if($conexion == 1){
            
            $fecha_hora3 = $this->fechaHora(true);
            if(is_soap_fault($fecha_hora3)){
                return redirect('facturacion/create')->withInput()->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: NO SE PUDO HORA: '.$fecha_hora3 );
            }
            $fecha_hora = Carbon::now(-4)->format('Y-m-d H:i:s');

            $tipoEmisionN = 1; //TRUE - ENVIO INDIVIDUAL 
            $validanit = $request->get('validanit'); // codigo (valor 1) para ver si se emite factura con nit errado
            // dd($validanit);  
            $cuis = $this->cuis();
            //cufd solicitud una vez al dia o en caso de contigencia al finalizar la misma.
            $cufdbd = Cufd::where('fecha_hora','>=',$fecha_hora)
            ->where('id_sucursal','=',auth()->user()->id_sucursal)
            ->where('punto_venta','=',auth()->user()->punto_venta)
            ->orderby('id','DESC')
            ->get();
            //dd($cufdbd);
            if($cufdbd->isEmpty() == true){
                $cufd = $this->cufd();
                if(is_soap_fault($cufd)){
                      //var_dump('sin conexion');
                    $fecha_hora3 = Carbon::now(-4)->format('Y-m-d/H:i:s.z');
                    $fecha_hora3 = str_replace("/", "T", $fecha_hora3);
                    $tipoEmisionN = 2; //TRUE - ENVIO INDIVIDUAL 
                    $validanit = 1; 

                    $cufd = Cufd::where('id_sucursal','=',auth()->user()->id_sucursal)
                    ->where('punto_venta','=',auth()->user()->punto_venta)
                    ->orderby('id','DESC')
                    ->first();
                    //dd($cufd);

                    $cuis = Cuis::where('id_sucursal','=',auth()->user()->id_sucursal)
                    ->where('punto_venta','=',auth()->user()->punto_venta)
                    ->orderby('id','DESC')
                    ->first();
                    //dd($fecha_hora3);
                    //dd($cufd, $cuis);
                    //var_dump('Entro hasta el final');
                }
               
            }else{
                $cufd = Cufd::where('id_sucursal','=',auth()->user()->id_sucursal)
                ->where('punto_venta','=',auth()->user()->punto_venta)
                ->orderby('id','DESC')
                ->first();
                //dd($cufd);y
            }
        }else{

            $fecha_hora3 = Carbon::now(-4)->format('Y-m-d/H:i:s.z');
            $fecha_hora3 = str_replace("/", "T", $fecha_hora3);
            $tipoEmisionN = 2; //TRUE - ENVIO INDIVIDUAL 
            $validanit = 1; 

            $cufd = Cufd::where('id_sucursal','=',auth()->user()->id_sucursal)
            ->where('punto_venta','=',auth()->user()->punto_venta)
            ->orderby('id','DESC')
            ->first();
            //dd($cufd);

            $cuis = Cuis::where('id_sucursal','=',auth()->user()->id_sucursal)
            ->where('punto_venta','=',auth()->user()->punto_venta)
            ->orderby('id','DESC')
            ->first();
            //dd($fecha_hora3);
            //dd($cufd, $cuis);
            //var_dump('Entro hasta el final');
        }
        $cufd_codigo = $cufd->codigo_cufd;
        $cufd_codigoControl = $cufd->codigo_control;
  
                       
        // funcion para obtener cuf
        //dd($fecha_hora3,$tipoEmisionN,$cufd_codigoControl,$numFac);
         $cuf = $this->cuf($fecha_hora3,$tipoEmisionN,$cufd_codigoControl,$numFac);
        //dd($fecha_hora3,$tipoEmisionN,$cufd_codigoControl,$numFac,$cuf);
        // -----------------------------------------------------------------------------------------------
        // VARIABLES PARA EL XML y BD
        // DATOS DE EMPRESA sacado de BD tabla datos_empresa, sucursal y ususario
        $dato = auth()->user()->id;
        $pv = auth()->user()->punto_venta;
        $s = auth()->user()->id_sucursal;

        $datos_empresa = Datos::first();
        $sucursal = Sucursal::where('nro_sucursal','=',auth()->user()->id_sucursal)->first();

        $nitEmisor = $datos_empresa->nit; 
        $razonSocialEmisor = $datos_empresa->razon_social;
        $municipio = $sucursal->municipio;
        $telefono = $sucursal->telefono;
        $direccion = $sucursal->direccion;
        $codPtoVenta = auth()->user()->punto_venta;
        $codSucursal = $sucursal->nro_sucursal;
        $codSector1 = 14; //no cambia -- factura alcanzado por ICE

        //LEYENDA 2
        //randon de leyendas que puede sacar
       
        $randon = rand(1,17);

        //dd($id_leyenda_actividad,$randon,$idley); 
         $leyenda = LeyendasFacturacion::where('id','=',$randon)->first();
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
   
        $id_cliente = Clientes::where('id','=',$request->get('id_cliente2'))->get();
        //dd($request->get('id_cliente2'));

        if($id_cliente->isEmpty()){
            $clie = new Clientes;
            $clie->nro_documento = $nroDocID;
            $clie->razon_social = $razonSocialCli;
            $clie->email = $request->get('email');
            $clie->save();
            $codCli1 = $clie->id;
        }
        else{
            $id_cliente = Clientes::where('id','=',$request->get('id_cliente2'))->first();
            $codCli1 = $id_cliente->id;
            $id_cliente->nro_documento = $nroDocID;
            $id_cliente->razon_social =  $request->get('razon_social');
            $id_cliente->email = $request->get('email');
            $id_cliente->save();
        }

        $codMetodoPago = $request->get('id_tipo_pago');
        if($codMetodoPago == 2){
            $nro_tarjeta = $request->get('nro_tarjeta').'00000000'.$request->get('nro_tarjeta2');
        }
        else{
            $nro_tarjeta = 0;
        }
        
        $codDocID = $request->get('id_tipo_documento');
        $complemento = $request->get('complemento'); //si no tiene en xml 
        
        //dd($complemento);
        $codCli = $codCli1;
        $usuario = $dato;
        $codMoneda1 = 1;
       
        // montos  TOTALGENERAL
        $total_final  = (double) str_replace(',', '', $request->get('total_detalle'));

        $ice_porcentual_total  = (double) str_replace(',', '', $request->get('ice_porcentual_total'));
        $ice_especifico_total  = (double) str_replace(',', '', $request->get('ice_especifico_total'));
        //$cantidad_l = round(0.33*6);        
        $tipoCambio = 1; //taza de cambio
        $descuentoAdicional = 0; //no cambia   
        $montoTotalMoneda =  $total_final;
        $montoTotal =  $total_final;
        $montoTotalSujetoIva =   (double) str_replace(',', '', $request->get('subtotal_para_iva'));  

        //detalle productos
        $id_p = $request->get('id');//codigo_empresa
        $codigo_impuestos_p = $request->get('codigo_impuestos');//codigo_impuestos
        $codigo_actividad_p = $request->get('codigo_actividad');
        $id_medida_p = $request->get('id_medida');
        $unidad_medida_p = $request->get('unidad_medida');
        $ice_porcentual_p = $request->get('ice_porcentual'); //fijo %
        $ice_especifico_p = $request->get('ice_especifico'); // fijo        
        $cantidad_litros_x_unidad_p = $request->get('cantidad_litros_x_unidad');
        $unidad_por_paquete_p = $request->get('unidad_por_paquete');//dato interno
        $subtot_linea_v_p = $request->get('subtot_linea_v');
        $alicuota_linea_v_p = $request->get('alicuota_linea_v');
        $neto_ice_linea_v_p = $request->get('neto_ice_linea_v');
        $cantidad_ice_litros_v_p = $request->get('cantidad_ice_litros_v'); //cantidad * litros
        $cantidad_p = $request->get('cantidad');
        $descripcion_producto_p = $request->get('descripcion_producto');
        $cantidad_paquete_p = $request->get('cantidad_paquete');//dato para calcular -- no se usa
        $cantidad_unidad_p = $request->get('cantidad_unidad');//dato para calcular -- no se usa
        $precio_unitario_p = $request->get('precio_unitario');
        //dd($precio_unitario_p);
        $descuento_p = $request->get('descuento');
        $ice_porcentual_calculado_p = $request->get('ice_porcentual_calculado');
        $ice_especifico_calculado_p = $request->get('ice_especifico_calculado');
        //dd($ice_especifico_calculado_p);
        $subtotal_p = $request->get('subtotal');

        //--- 5 decimales para facura
        $ice_porcentual_calculado_cinco_p = $request->get('ice_porcentual_calculado_cinco_v');
        $ice_especifico_calculado_cinco_p = $request->get('ice_especifico_calculado_cinco_v');
        $subtotal_cinco_p = $request->get('subtotal_cinco_v');
        
        //dd($ice_porcentual_calculado_cinco_p,$ice_especifico_calculado_cinco_p,$subtotal_cinco_p);
        //---
        //dd($razonSocialCli,$codDocID,$complemento,$codMetodoPago,$codigo_p, $codigo_producto_p,$cantidad_p,$unidad_medida_p,$id_unidad_medida_p,$descripcion_p, $precio_unitario_p, $subtotal_p, $total_final);

        //NO TOCAR FORMATO -- SI ES NECESARIO CAMBIAR NOMBRE DE VARIABLE
        //-----------------------------------------------------------------------------
        //  Fragmento de código para generar el XML a partir de los datos del formulario.

        $doc = new DOMDocument('1.0', 'utf-8');

        $doc->formatOutput = true;

        $xmlFactura = $doc->appendChild($doc->createElement('facturaComputarizadaAlcanzadaIce'));
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
            $cabecera->appendChild($doc->createElement('numeroTarjeta',$nro_tarjeta));
        else
        {
            $nroTarjeta = $cabecera->appendChild($doc->createElement('numeroTarjeta'));
            $nroTarjeta->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',"xsi:nil", "true");
        }        
        $cabecera->appendChild($doc->createElement('montoTotal',round($montoTotal,2)));
        $cabecera->appendChild($doc->createElement('montoIceEspecifico',round($ice_especifico_total,5)));
        $cabecera->appendChild($doc->createElement('montoIcePorcentual', round($ice_porcentual_total,5)));
        $cabecera->appendChild($doc->createElement('montoTotalSujetoIva',round($montoTotalSujetoIva,2)));
        $cabecera->appendChild($doc->createElement('codigoMoneda',$codMoneda1));
        $cabecera->appendChild($doc->createElement('tipoCambio',$tipoCambio));
        $cabecera->appendChild($doc->createElement('montoTotalMoneda',round($montoTotalMoneda,2)));       
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
        $cabecera->appendChild($doc->createElement('codigoDocumentoSector',$codSector1));

        $cont = 0; 
                
        while($cont < count($id_p)){

            $cantidad = (double) str_replace(',', '', $cantidad_p[$cont]);
            $precio_u = (double) str_replace(',', '', $precio_unitario_p[$cont]);
            $subtotal = (double) str_replace(',', '', $subtotal_cinco_p[$cont]);
            $descuento = (double) str_replace(',', '', $descuento_p[$cont]);
            $alicuotaIva = (double) str_replace(',', '', $alicuota_linea_v_p[$cont]);
            $netoIce = (double) str_replace(',', '', $neto_ice_linea_v_p[$cont]);
            $alicuotaPorcent = (double) str_replace(',', '', $ice_porcentual_p[$cont]);
            $alicuotaEsp = (double) str_replace(',', '',  $ice_especifico_p[$cont]);
            $icePorcent = (double) str_replace(',', '',  $ice_porcentual_calculado_cinco_p[$cont]);
            $iceEsp = (double) str_replace(',', '',  $ice_especifico_calculado_cinco_p[$cont]);
            $cantidad_l = (double) str_replace(',', '',  $cantidad_ice_litros_v_p[$cont]);
           
            $detalle = $xmlFactura->appendChild($doc->createElement('detalle'));

            $detalle->appendChild($doc->createElement('actividadEconomica', $codigo_actividad_p[$cont])); 
            $detalle->appendChild($doc->createElement('codigoProductoSin',  $codigo_impuestos_p[$cont]));
            $detalle->appendChild($doc->createElement('codigoProducto',$id_p[$cont]));
            $detalle->appendChild($doc->createElement('descripcion',htmlspecialchars($descripcion_producto_p[$cont])));
            $detalle->appendChild($doc->createElement('cantidad',number_format($cantidad, 5, '.', '')));
            $detalle->appendChild($doc->createElement('unidadMedida', $id_medida_p[$cont]));
            $detalle->appendChild($doc->createElement('precioUnitario',number_format($precio_u, 5, '.', '')));
            $detalle->appendChild($doc->createElement('montoDescuento',number_format($descuento, 5, '.', ''))); //si tiene
            $detalle->appendChild($doc->createElement('subTotal',number_format($subtotal, 5, '.', '')));

            $detalle->appendChild($doc->createElement('marcaIce',1)); //si tiene ice 

            $detalle->appendChild($doc->createElement('alicuotaIva',number_format($alicuotaIva, 5, '.', '')));
            $detalle->appendChild($doc->createElement('precioNetoVentaIce',number_format($netoIce, 5, '.', '')));

            $detalle->appendChild($doc->createElement('alicuotaEspecifica',number_format($alicuotaEsp, 5, '.', '')));
            $detalle->appendChild($doc->createElement('alicuotaPorcentual',number_format($alicuotaPorcent, 5, '.', '')));

            $detalle->appendChild($doc->createElement('montoIceEspecifico',number_format($iceEsp, 5, '.', '')));
            $detalle->appendChild($doc->createElement('montoIcePorcentual',number_format($icePorcent, 5, '.', '')));

            $detalle->appendChild($doc->createElement('cantidadIce',number_format($cantidad_l, 5, '.', ''))); 


            $cont++;
        }        

        //para guardar storage  
        $dir = storage_path('facturas/linea'.$s.$pv.'.xml');
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
            $dir1 = storage_path('/facturas/lineaF'.$s.$pv.'.xml');
            $doc->save($dir1);
            for($i=1;$i<=500;$i++){
                $dir = storage_path('/facturas/lineaF'.$s.$pv.$i.'.xml');
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
            $doc->save(storage_path('/facturas/lineaF'.$s.$pv.'.xml'));
            
            //---------------------------------------------------------------------
            //  Fragmento de código para comprimir en un gzip el XML antes generado
            $origen =  storage_path('/facturas/lineaF'.$s.$pv.'.xml');
            $dest = storage_path('/facturas/lineaF'.$s.$pv.'.zip');
            $fp = fopen($origen, "r");
            $data = fread ($fp, filesize($origen));
            fclose($fp);
            $zp = gzopen($dest, "w9");
            gzwrite($zp, $data);
            gzclose($zp);

            // ------------------------------------------------------------------------
            // Encriptamos en un hash con SHA-256
            $hash = hash_file("sha256",storage_path('/facturas/lineaF'.$s.$pv.'.zip'), $raw_output = false);
            $archivoArray = file(storage_path('/facturas/lineaF'.$s.$pv.'.zip'));
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
                    'codigoDocumentoSector' => $codSector1,
                    'codigoEmision'         => $tipoEmisionN,
                    'codigoModalidad'       => $modalidad,
                    'codigoPuntoVenta'      => auth()->user()->punto_venta,
                    'codigoSistema'         => $this->codigo_sistema,
                    'codigoSucursal'        => auth()->user()->id_sucursal,
                    'cufd'                  => $cufd_codigo,
                    'cuis'                  => $cuis->codigo_cuis,
                    // 'cuis'                  => 'EED58C49',
                    'nit'                   => $this->nit,
                    'tipoFacturaDocumento'  => $codTipoFac,
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
                //dd($recepFac);//, $codSector1.' codigo_sector',$tipoEmisionN.' tipo emision',$modalidad.' modalidad',auth()->user()->punto_venta.' punto venta',auth()->user()->id_sucursal.' id sucursal',$cufd_codigo.' cufd_codigo',$codTipoFac.' codigoTipoFac',$cuis->codigo_cuis.' cuis');
                $respuesta = $recepFac->{'RespuestaServicioFacturacion'};
                $descrip = $respuesta->{'codigoDescripcion'};
                
                $codigo_verifica_fac = $respuesta->{'codigoEstado'};
                
                if( $codigo_verifica_fac != 902){
                   
                    $solicitudServicioVerificacionEstadoFactura = array(
                        'codigoAmbiente'        => $this->codigo_ambiente,
                        'codigoDocumentoSector' => $codSector1,
                        'codigoEmision'         => $tipoEmisionN,
                        'codigoModalidad'       => $modalidad,
                        'codigoPuntoVenta'      => auth()->user()->punto_venta,
                        'codigoSistema'         => $this->codigo_sistema,
                        'codigoSucursal'        => auth()->user()->id_sucursal,
                        'cufd'                  => $cufd_codigo,
                        'cuis'                  => $cuis->codigo_cuis,
                        // 'cuis'                  => 'EED58C49',
                        'nit'                   => $this->nit,
                        'tipoFacturaDocumento'  => $codTipoFac,
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
                    $codigo_recpecion = $verires->{'codigoRecepcion'};
                }
                else{
                    $detalledescrip = $respuesta->{'mensajesList'};
                    $detalled = $detalledescrip->{'descripcion'};
                    $codigo_verifica_des =  $descrip.' '.$detalled; 
                }
            } catch (SoapFault $e) {
                return redirect('facturacion/create')->withInput()->with('status', 'OCURRIO UN INCONVENIENTE CON IMPUESTOS INTERNOS: '.$e);
            }
                        
        }    
        //FIN DE METODO         
        //VERIFICANDO SI LA FACTURA EMITIDA ES VALIDA  Y EN LINEA PARA GUARDAR EN BD
        if(($tipoEmisionN == 1  && $codigo_verifica_fac == 690) || $tipoEmisionN == 2 ){
            $factura_bd = new Facturacion;

            //DATOS GUARDADOS EN BD TABLA FACTURA
            
            $fechabd =  str_replace('T', ' ', $fecha_hora3);
           
            $fecha_hora1 = substr($fechabd, 0, 19);
          
            $fecha1 = substr($fecha_hora1, 0, -9);
            //dd($fecha_hora3,$fechabd, $fecha_hora1,$fecha1);
            $factura_bd->id_factura = $numFac;     
            
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
            $factura_bd->monto_total_sujeto_iva =  $montoTotalSujetoIva;
            $factura_bd->ice_especial = $ice_especifico_total;
            $factura_bd->ice_porcentual = $ice_porcentual_total;
            
            $factura_bd->monto_total_moneda =   $montoTotalMoneda ; //suma de gastos nacionales + suma del total detalle -- particularmente el dato es igual al total detalle por que fob es cero
           
            $factura_bd->codigo_excepcion = $validanit ;
            $factura_bd->descuento_adicional = 0 ; // no cambia
            $factura_bd->cafc = $request->get('codigo_cafc'); //no cambia - no se emitira manuales
            
            //$factura_bd->codigo_documento_sector =  $codSector1 ;// no cambia
            $factura_bd->id_usuario = $dato;

            $factura_bd->hora_impuestos = $fecha_hora3;
            if($tipoEmisionN == 1){
                $factura_bd->codigo_recepcion = $codigo_recpecion;
            }
            if($tipoEmisionN == 2 && (int)$request->get('linea') == 0){
                $factura_bd->fuera_linea = 1;
            }
            
            $factura_bd->save();

           //variables necesarias declaradas mas arriba           
           $cont = 0; 
                
            while($cont < count($id_p)){

                $cantidad = (double) str_replace(',', '', $cantidad_p[$cont]);
                $precio_u = (double) str_replace(',', '', $precio_unitario_p[$cont]);
                $subtotal = (double) str_replace(',', '', $subtotal_cinco_p[$cont]);
                $descuento = (double) str_replace(',', '', $descuento_p[$cont]);
                $alicuotaIva = (double) str_replace(',', '', $alicuota_linea_v_p[$cont]);
                $netoIce = (double) str_replace(',', '', $neto_ice_linea_v_p[$cont]);
                $alicuotaPorcent = (double) str_replace(',', '', $ice_porcentual_p[$cont]);
                $alicuotaEsp = (double) str_replace(',', '',  $ice_especifico_p[$cont]);
                $icePorcent = (double) str_replace(',', '',  $ice_porcentual_calculado_cinco_p[$cont]);
                $iceEsp = (double) str_replace(',', '',  $ice_especifico_calculado_cinco_p[$cont]);
                $cantidad_l = (double) str_replace(',', '',  $cantidad_ice_litros_v_p[$cont]);
                    
                $producto_detalle = new FacturaDetalle;
                $producto_detalle->id_tabla_factura = $factura_bd->id;
                $producto_detalle->id_factura = $numFac;
                $producto_detalle->id_sucursal = auth()->user()->id_sucursal;
                $producto_detalle->punto_venta =auth()->user()->punto_venta;

                $producto_detalle->codigo_producto_sin = $codigo_impuestos_p[$cont];
                $producto_detalle->codigo_producto_empresa =$id_p[$cont];
                $producto_detalle->descripcion = $descripcion_producto_p[$cont];
                $producto_detalle->unidad_medida_des = $unidad_medida_p[$cont];
                $producto_detalle->cantidad = $cantidad;
                $producto_detalle->precio = $precio_u;
                $producto_detalle->descuento = $descuento;
                $producto_detalle->subtotal = $subtotal;
                $producto_detalle->alicouta_iva = $alicuotaIva;
                $producto_detalle->neto_ice = $netoIce;
                $producto_detalle->alicuota_esp = $alicuotaEsp;
                $producto_detalle->alicuota_por = $alicuotaPorcent;
                $producto_detalle->ice_esp = $iceEsp;
                $producto_detalle->ice_por = $icePorcent;
                $producto_detalle->cantidad_l = $cantidad_l;
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
        
        return redirect('facturacion')->with('status', $codigo_verifica_des);
        
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
