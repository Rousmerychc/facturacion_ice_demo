<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Datos;
use App\PreFactura;
use App\PrePaqueteBultos;
use App\PreFacturaDetalle;
use App\PreSubProductosFactura;
use App\PreInformacionAdicional;

use Carbon\Carbon;
use App\UnidadMedida;
use App\Cliente;
use App\Productos;
use App\LeyendasFacturacion;
use App\SubProductos;

use DataTables;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

use PDF;
use Endroid\QrCode\QrCode;


class PreFacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
    }

    //FUNCION CODIGO QR
    public function codigoQR($id){

        $datos_empresa = Datos::first();

        $factura = PreFactura::findOrFail($id);
        //dd($factura);

        $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$datos_empresa->nit.'&cuf='.$factura->cuf.'&numero='.$factura->id.'&t=2';
            
        $qrCode = new QrCode($url);//Creo una nueva instancia de la clase
        $qrCode->setSize(100);//Establece el tamaño del qr
        //header('Content-Type: '.$qrCode->getContentType());
        $image= $qrCode->writeString();//Salida en formato de texto 
        
        $imageData = base64_encode($image);//Codifico la imagen usando base64_encode
        
        //echo '<img src="data:image/png;base64,'.$imageData.'">';
        return $imageData;
    }

    //FUNCION PDF PRE FACTURA
    public function pre_pdf_factura($id){

        $datos_empresa = Datos::first();
        $factura = PreFactura::findOrFail($id);
        //dd($factura);
        $detalle = PreFacturaDetalle::where('id_factura','=',$factura->id)
                    ->join('productos', 'productos.id', '=', 'pre_factura_detalle.id_producto')
                    ->get();
                   //dd($detalle);
        $leyenda2 =  LeyendasFacturacion::findOrFail($factura->id_leyenda);         
        $paquete_bulto = PrePaqueteBultos::where('id_factura','=',$factura->id)->get();
        $cliente = Cliente::where('id', '=', $factura->codigo_cliente)->first();
        $informacionAdicional = PreInformacionAdicional::where('id_factura','=',$factura->id)->get();
        //PARA LA TERCERA LEYENDA - FACTURA EMITIDA EN LINEA O FUERA DE LINEA
        if($factura->tipo_emision_n == 1){
            $leyenda3 = "Este documeto es la Representación Gráfica  de un Documento Fiscal Digital en una modalidad de facturación en linea";
        }else{
            $leyenda3 = "Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web www.impuestos.gob.bo.";
        }
        
        //PARA LITERAL EN MONEDA EXTRANJERA
        $qr = $this->codigoQR($id);
        $re= (int) ($factura->monto_total_moneda / 1000); //lo mismo que monto_detalle
        $formatter = new NumeroALetras();
        $literal= $formatter->toInvoice($factura->monto_total_moneda, 2,'');                                                   
        if($re==1){
            $literal = 'UN '.$literal;
        }

        //PARA LITERAL EN MONEDA BOLIVIANOS
        $formatterb = new NumeroALetras();
        $reb= (int) ($factura->monto_total / 1000);
        $literalb= $formatterb->toInvoice($factura->monto_total, 2,'');
        if($reb==1){
            $literalb = 'UN '.$literalb;
        }
 
        $data =['factura'=>$factura, 'detalle'=>$detalle, 'paquete_bulto'=>$paquete_bulto , 'datos_empresa'=>$datos_empresa ,'cliente'=>$cliente , 'qr'=>$qr , 'literal'=>$literal , 'leyenda2'=> $leyenda2, 'leyenda3'=>$leyenda3, 'literalb'=>$literalb, 'informacionAdicional' => $informacionAdicional ];
        
        $de_ritex = PreFacturaDetalle::where('id_factura','=',$factura->id)->where('valor_re_exportacion','>',0)->get();
        if($de_ritex->isEmpty()){
            $pdf = PDF::loadView('pre_facturacion.pdf_cliente',$data);
        }else{
            $pdf = PDF::loadView('pre_facturacion.pfd_cliente_ritex',$data);
        }
        $pdf->setPaper("letter", "portrait");
        return $pdf->stream('PreFactura.pdf');
        
    }
     //FUNCIONES EN SEGUNDO PLANO PARA VISTA CREATE
    public function cliente_fac(Request $request){
        $p=$request->get('dato');
        $prueba =DB::table('clientes') 
        ->where('id','=',$p)
        ->select('id','email','razon_social_cli','tipo_documento','nro_documento','complemento','direccion','incoterm','incoterm_detalle','puerto_destino','lugar_destino','codigo_pais','codigo_metodo_pago','tipo_moneda','descripcion_moneda','estado')
        ->get();
    
       return response(json_encode(array('prueba'=>$prueba)),200)->header('Content-type','text/plain');
    }
    public function producto_fac(Request $request){
       $p=$request->get('dato');
       $prueba = DB::table('productos')
       ->where('id','=',$p)
       ->select('id','codigo_empresa','descripcion','codigo_impuestos','id_nandina','nandina','codigo_unidad_medida','unidad_medida','precio','estado','usuario')
       ->get();

       return response(json_encode(array('prueba'=>$prueba)),200)->header('Content-type','text/plain');
    }
    public function facturaajax(Request $request){
       if ($request->ajax()) {
           $data = PreFactura::orderBy('id', 'DESC')->get();
           return Datatables::of($data)
               ->addColumn('btn','pre_facturacion.actions')
               ->addColumn('pdf','pre_facturacion.pdf')
               ->rawColumns(['btn','pdf'])
               ->make(true);
       }
    }
    function hasConnection() {  
        $ch = curl_init("https://siatrest.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl");  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
        curl_exec($ch);  
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
        curl_close($ch);  
        return ($httpcode>=200 && $httpcode<300) ? TRUE : FALSE;
    }

    public function index()
    {
        //
        //   $conexion = $this->hasConnection();
        //   dd($conexion);
        //  if($conexion){
        //     return ('hola');
        //  }else{ 
        //     return ('nooo');
        // }
       
        return view('pre_facturacion.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $nro = PreFactura::max('id');
        if($nro ==null)
        {$nro = 0;};
        //dd($nro);
        $fh=Carbon::now(-4)->format('Y-m-d');//,H:i:s');
        $clientes = Cliente::orderBy('nro_documento','DESC')->get();
        $productos = Productos::all();
        $subproducto = SubProductos::all();

        return view('pre_facturacion.facturacion',['nro'=>$nro, 'fh'=>$fh, 'clientes'=>$clientes, 'productos'=>$productos , 'subproducto'=>$subproducto]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $dato = auth()->user()->id;
        //Generando cadena bultos
        $nombre_paquete = $request->get('nombre_paquete');
        $detalle_paquete = $request->get('detalle_paquete');
        $cont = 0;
        $numeroPaqueteBultos="";
        while($cont < count($nombre_paquete)){
            
            if(is_null($nombre_paquete[$cont])==false){
                $numeroPaqueteBultos =  $numeroPaqueteBultos.$nombre_paquete[$cont].":".$detalle_paquete[$cont].",";
            }
            $cont++;
        }
        $numeroPaqueteBultos = strtoupper($numeroPaqueteBultos);

        //GENERANDO INFORMACION ADICIONAL
        $infoadicional = $request->get('nombre_informacion_adi');
        $detalleadicional =  $request->get('detalle_informacion_adi');
        $inforAdi = "";
        $contAdi = 0;

        while($contAdi < count($infoadicional)){
            
            if(is_null( $infoadicional[$contAdi])==false){
                $inforAdi =  $inforAdi. $infoadicional[$contAdi].":". $detalleadicional[$contAdi]."-";
            }
            $contAdi++;
        }

       

        //LEYENDA 2
        $randon = rand(4, 7);
        $leyenda = LeyendasFacturacion::where('id','=',$randon)
        ->where('codigo_actividad','=',321000)
        ->first();
        $descrip_leyenda = $leyenda->descripcion_leyenda;
       

        //Datos del cliente
        //OBTENCION DE DATOS CLIENTE -BD
        $cliente = Cliente::findOrFail($request->get('id_cliente'));
        $razonSocialCli = $cliente->razon_social_cli;
        $codDocID = $cliente->tipo_documento;
        $nroDocID = $cliente->nro_documento;
        $complemento = $cliente->complemento;
        $codDirCli = $cliente->direccion;
        $codCli = $cliente->id;
        $usuario = $dato;
        $incoterm = $cliente->incoterm;
        $incotermDetalle = $cliente->incoterm_detalle;
        $puertoDestino = $cliente->puerto_destino;
        $lugarDestino = $cliente->lugar_destino;
        $codPais = $cliente->codigo_pais;
        $codMetodoPago = $cliente->codigo_metodo_pago;
        $codMoneda = $cliente->tipo_moneda;

        $costoNacional = '{"":0}';
        $costosInternacionales = '{"":0}';
        $informacionAdicional = strtoupper($inforAdi); 
        
        //montos 
        $tipoCambio = $request->get('tipo_cambio');
        $descuentoAdicional = 0; //no cambia
        
        $total_detalle = str_replace(',', '', $request->{'total_detalle'});
        $totalGastosNacionalesFob =  (double)$total_detalle;
       
        $totalGastosInternacionales = 0;
        $montoTotalMoneda = $totalGastosInternacionales + $totalGastosNacionalesFob;
        
        $monto_total_bs = (double) str_replace(',', '', $request->{'monto_total_bs'});
        $montoTotal = $monto_total_bs; //round(($montoTotalMoneda*$tipoCambio),2);  //monto_total_bs
        
        $montoTotalSujetoIva = 0;  
        $montoDetalle = (double) $total_detalle;

        //------------------------------------------------------------------------------------------------------
        //GUARDANDO EN BD PREFACTURA
             
        $factura_bd = new PreFactura;
        //dd($factura_bd);
        $fecha_hora1 = Carbon::now(-4)->format('Y-m-d H:i:s');
        $fecha1 = Carbon::now(-4)->format('Y-m-d');
        
        $factura_bd->fecha = $fecha1;
        $factura_bd->fecha_hora = $fecha_hora1;
        $factura_bd->razon_social = $cliente->razon_social_cli;
        $factura_bd->tipo_documento_identidad = $cliente->tipo_documento; 
        $factura_bd->nro_documento = $cliente->nro_documento;
        $factura_bd->complemento = $cliente->complemento;
        $factura_bd->incoterm = $cliente->incoterm;
        $factura_bd->incoterm_detalle = $cliente->incoterm_detalle;
        $factura_bd->puerto_destino = $cliente->puerto_destino;
        $factura_bd->lugar_destino = $cliente->lugar_destino;
        $factura_bd->codigo_pais = $cliente->codigo_pais;
        $factura_bd->codigo_cliente = $cliente->id;
        $factura_bd->direccion_cliente = $cliente->direccion;
        $factura_bd->metodo_pago = $cliente->codigo_metodo_pago;
        $factura_bd->codigo_moneda = $cliente->tipo_moneda;
        $factura_bd->id_leyenda = $leyenda->id;
        
        //datos vista 
        $factura_bd->total_gastos_nacionales_fob = 0 ; //no cambia
        $factura_bd->total_gastos_internacionales = 0; // no cambia
        
        $factura_bd->monto_detalle =  $total_detalle;
        $factura_bd->total_menos_iva = 0;
        $factura_bd->tipo_cambio = (double)$request->{'tipo_cambio'};
        $factura_bd->monto_total_moneda =  round($total_detalle, 2) + 0 ; //suma de gastos nacionales + suma del total detalle -- particularmente el dato es igual al total detalle por que fob es cero
        $factura_bd->monto_total =  $monto_total_bs;//(double)$request->{'tipo_cambio'}* (double)$request->{'total_detalle'};
        $factura_bd->informacion_adicional = $informacionAdicional;
        $factura_bd->codigo_excepcion = 0 ; // no cambia
        $factura_bd->descuento_adicional = 0 ; // no cambia
        $factura_bd->cafc = 0; //no cambia - no se emitira manuales
        
        $factura_bd->codigo_documento_sector = 3 ;// no cambia
        $factura_bd->usuario = $dato;
        $factura_bd->numero_descripcion_paquetes_bultos = strtoupper($numeroPaqueteBultos);
        $factura_bd->forma_pago = $request->{'forma_pago_detalle'};
        $factura_bd->save();
        $cont = 0;

        while($cont < count($nombre_paquete)){
            
            if(is_null($nombre_paquete[$cont])==false){
                $paquetes = new PrePaqueteBultos;
                $paquetes->id_factura = $factura_bd->id;
                $paquetes->detalle = $nombre_paquete[$cont];
                $paquetes->valor_detalle = $detalle_paquete[$cont];
                $paquetes->save();
            }
            $cont++;
        }
    
        //Factura detalle de productos
        $codigo_producto = $request->{'codigo_pro'};
        $nandina_producto = $request->{'nandina'};
        $cantidad_producto = $request->{'cantidad'};  
        $descripcion_producto = $request->{'descripcion'};
        $unidad_medida_producto  = $request->{'unidad_medida'};
        $precio_unitario_producto = $request->{'precio_uni'};
        $valor_agregado = $request->{'valor_agregado'};
        $valor_re_exportacion = $request->{'valor_reexport'};
        $precio_unitario_sin = $request->{'precio_uni_sin'};
        $subtotal_producto = $request->{'subtotal'};
        
        $detallesubpro = $request->{'subproducto'};
        $subproductocodigo = $request->{"subproductocodigo"};
        $rit = 1;
        $cont_pro_bd = 0;
        while($cont_pro_bd < count($codigo_producto)){

            $cantidad = (double) str_replace(',', '', $cantidad_producto[$cont_pro_bd]);
            $precio_unitario = (double) str_replace(',', '', $precio_unitario_producto[$cont_pro_bd]);
            $subtotal_producto1 = (double) str_replace(',', '',$subtotal_producto[$cont_pro_bd]);
            $valor_agregado1 =(double) str_replace(',', '',$valor_agregado[$cont_pro_bd]);
            $valor_re_exportacion1 = (double) str_replace(',', '',$valor_re_exportacion[$cont_pro_bd]);
            $precio_unitario_sin1 = (double) str_replace(',', '',$precio_unitario_sin[$cont_pro_bd]);
           

            $producto_detalle = new PreFacturaDetalle;
            $producto_detalle->id_factura = $factura_bd->id;
            $producto_detalle->id_producto = $codigo_producto[$cont_pro_bd];
            $producto_detalle->cantidad = $cantidad;
            $producto_detalle->precio_unitario =  $precio_unitario;

            $producto_detalle->valor_agregado = $valor_agregado1;
            $producto_detalle->valor_re_exportacion = $valor_re_exportacion1;
            if( $valor_re_exportacion1 != 0.0){
                $rit = 2;
            }
            $producto_detalle->precio_unitario_sin = $precio_unitario_sin1;

            $producto_detalle->subtotal = $subtotal_producto1;
    
            $producto_detalle->dessubpro = $detallesubpro[$cont_pro_bd];
            $producto_detalle->codigosubpro = $subproductocodigo[$cont_pro_bd];
           
            $producto_detalle->save();
            
            $cont_pro_bd++;
        }

        $factura_bd->ritex = $rit;
        $factura_bd->save();    

        $cont_subpro = 0;
        while($cont_subpro < count($subproductocodigo)){
            
            $arraysubpro = explode(",", $detallesubpro[$cont_subpro]);
            $arraysubprocod = explode(",", $subproductocodigo[$cont_subpro]);
            //dd($arraysubpro, $arraysubprocod);
            foreach( $arraysubprocod as $asubprocod){
                if( $asubprocod != ""){
                    $subproducto_detalle = new PreSubProductosFactura;
                    $subproducto_detalle->id_factura = $factura_bd->id;
                    $subproducto_detalle->id_producto = $codigo_producto[$cont_subpro];
                    $subproducto_detalle->id_sub_producto = $asubprocod;
                    $subproducto_detalle->save();
                }
               
            }  
            $cont_subpro++; 
        }
        $contAdi1 = 0;
        while($contAdi1 < count( $infoadicional)){
            
            $info_adicional = new PreInformacionAdicional;
            $info_adicional->id_factura = $factura_bd->id;
            $info_adicional->descripcion =  $infoadicional[$contAdi1];
            $info_adicional->descripcion_detalle = $detalleadicional[$contAdi1];

            $info_adicional->save();
        
            $contAdi1++; 
            
        }
        return redirect('prefactura')->with('status','REGISTRO GUARDADO CON EXITO');
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
    public function edit($id)
    {
        //
        
        $fh=Carbon::now(-4)->format('Y-m-d');//,H:i:s');
        
        //DATOS RECUPERADOS DE PREFACTURA
        $factura = PreFactura ::findOrFail($id);
        $cliente =DB::table('clientes') 
        ->where('id','=',$factura->id)
        ->select('id','email','razon_social_cli','tipo_documento','nro_documento','complemento','direccion','incoterm','incoterm_detalle','puerto_destino','lugar_destino','codigo_pais','codigo_metodo_pago','tipo_moneda','descripcion_moneda','estado')
        ->first();
        $product = DB::table('pre_factura_detalle')
        ->where('id_factura','=',$factura->id)
        ->join('productos','productos.id','=','pre_factura_detalle.id_producto')
        ->get();
        $paquetebulto = DB::table('pre_paquete_bultos')
        ->where('pre_paquete_bultos.id_factura','=',$factura->id)
        ->get();
        $informacionadi = PreInformacionAdicional::where('id_factura','=',$factura->id)->get();

        //DATOS NECESARIOS PARA MODIFICAR LA PRE FACTURA
        $clientes = Cliente::orderBy('nro_documento','DESC')->get();
        $productos = Productos::all();
        $subproducto = SubProductos::all();
        return view('pre_facturacion.edit',['fh'=>$fh, 'clientes'=>$clientes, 'productos'=>$productos , 'subproducto'=>$subproducto,
                                                    'factura'=>$factura,'cliente'=>$cliente, 'product'=>$product , 'paquetebulto' => $paquetebulto,
                                                'informacionadi'=>$informacionadi]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return ('hola uptade');
        $dato = auth()->user()->id;
        //Generando cadena bultos
        $nombre_paquete = $request->get('nombre_paquete');
        $detalle_paquete = $request->get('detalle_paquete');
        $cont = 0;
        $numeroPaqueteBultos="";

        while($cont < count($nombre_paquete)){
            
            if(is_null($nombre_paquete[$cont])==false){
                $numeroPaqueteBultos =  $numeroPaqueteBultos.$nombre_paquete[$cont].":".$detalle_paquete[$cont].",";
            }
            $cont++;
        }
        $numeroPaqueteBultos = strtoupper($numeroPaqueteBultos);
        //GENERANDO INFORMACION ADICIONAL
        $infoadicional = $request->get('nombre_informacion_adi');
        $detalleadicional =  $request->get('detalle_informacion_adi');
        $inforAdi = "";
        $contAdi = 0;

        while($contAdi < count($infoadicional)){
            
            if(is_null( $infoadicional[$contAdi])==false){
                $inforAdi =  $inforAdi. $infoadicional[$contAdi].":". $detalleadicional[$contAdi]."-";
            }
            $contAdi++;
        }


        //LEYENDA 2
        // $randon = rand(4, 7);
        // $leyenda = LeyendasFacturacion::where('id','=',$randon)
        // ->where('codigo_actividad','=',321000)
        // ->first();
        // $descrip_leyenda = $leyenda->descripcion_leyenda;
       

        //Datos del cliente
        //OBTENCION DE DATOS CLIENTE -BD
        $cliente = Cliente::findOrFail($request->get('id_cliente'));
        $razonSocialCli = $cliente->razon_social_cli;
        $codDocID = $cliente->tipo_documento;
        $nroDocID = $cliente->nro_documento;
        $complemento = $cliente->complemento;
        $codDirCli = $cliente->direccion;
        $codCli = $cliente->id;
        $usuario = $dato;
        $incoterm = $cliente->incoterm;
        $incotermDetalle = $cliente->incoterm_detalle;
        $puertoDestino = $cliente->puerto_destino;
        $lugarDestino = $cliente->lugar_destino;
        $codPais = $cliente->codigo_pais;
        $codMetodoPago = $cliente->codigo_metodo_pago;
        $codMoneda = $cliente->tipo_moneda;

        $costoNacional = '{"":0}';
        $costosInternacionales = '{"":0}';
        $informacionAdicional = strtoupper($inforAdi);
        
        //montos 
        $tipoCambio = $request->get('tipo_cambio');
        $descuentoAdicional = 0; //no cambia
        
        $total_detalle = str_replace(',', '', $request->{'total_detalle'});
        $totalGastosNacionalesFob =  (double)$total_detalle;
       
        $totalGastosInternacionales = 0;
        $montoTotalMoneda = $totalGastosInternacionales + $totalGastosNacionalesFob;
        
        $monto_total_bs = (double) str_replace(',', '', $request->{'monto_total_bs'});
        $montoTotal = $monto_total_bs; //round(($montoTotalMoneda*$tipoCambio),2);  //monto_total_bs
        
        $montoTotalSujetoIva = 0;  
        $montoDetalle = (double) $total_detalle;

        //------------------------------------------------------------------------------------------------------
        //GUARDANDO EN BD PREFACTURA
             
        $factura_bd = PreFactura::findOrFail($request->{'id_factura'});
        //dd($factura_bd);
        $fecha_hora1 = Carbon::now(-4)->format('Y-m-d H:i:s');
        $fecha1 = Carbon::now(-4)->format('Y-m-d');
        
        $factura_bd->fecha = $fecha1;
        $factura_bd->fecha_hora = $fecha_hora1;
        $factura_bd->razon_social = $cliente->razon_social_cli;
        $factura_bd->tipo_documento_identidad = $cliente->tipo_documento; 
        $factura_bd->nro_documento = $cliente->nro_documento;
        $factura_bd->complemento = $cliente->complemento;
        $factura_bd->incoterm = $cliente->incoterm;
        $factura_bd->incoterm_detalle = $cliente->incoterm_detalle;
        $factura_bd->puerto_destino = $cliente->puerto_destino;
        $factura_bd->lugar_destino = $cliente->lugar_destino;
        $factura_bd->codigo_pais = $cliente->codigo_pais;
        $factura_bd->codigo_cliente = $cliente->id;
        $factura_bd->direccion_cliente = $cliente->direccion;
        $factura_bd->metodo_pago = $cliente->codigo_metodo_pago;
        $factura_bd->codigo_moneda = $cliente->tipo_moneda;
        //$factura_bd->id_leyenda = $leyenda->id;
        
        //datos vista 
        $factura_bd->total_gastos_nacionales_fob = 0 ; //no cambia
        $factura_bd->total_gastos_internacionales = 0; // no cambia
        
        $factura_bd->monto_detalle =  $total_detalle;
        $factura_bd->total_menos_iva = 0;
        $factura_bd->tipo_cambio = (double)$request->{'tipo_cambio'};
        $factura_bd->monto_total_moneda =  $total_detalle + 0 ; //suma de gastos nacionales + suma del total detalle -- particularmente el dato es igual al total detalle por que fob es cero
        $factura_bd->monto_total =  $monto_total_bs;//(double)$request->{'tipo_cambio'}* (double)$request->{'total_detalle'};
        $factura_bd->informacion_adicional = $informacionAdicional;
        $factura_bd->codigo_excepcion = 0 ; // no cambia
        $factura_bd->descuento_adicional = 0 ; // no cambia
        $factura_bd->cafc = 0; //no cambia - no se emitira manuales
        
        $factura_bd->codigo_documento_sector = 3 ;// no cambia
        $factura_bd->usuario = $dato;
        $factura_bd->numero_descripcion_paquetes_bultos = strtoupper($numeroPaqueteBultos);
        $factura_bd->forma_pago = $request->{'forma_pago_detalle'};
        $factura_bd->save();
        $cont = 0;

        //BORRADO DE DETALLE FACTURA, PAQUETES, DETALLE SUBPRO INFO ADICIONAL
        $paquetesborrados = DB::table('pre_paquete_bultos')
        ->where('id_factura','=',$factura_bd->id)
        ->delete();
        
        $detalleBorado = DB::table('pre_factura_detalle')
        ->where('id_factura','=',$factura_bd->id)
        ->delete();

        $detallesubpro = DB::table('pre_sub_productos_factura')
        ->where('id_factura','=',$factura_bd->id)
        ->delete();

        $infoadi = DB::table('pre_informacion_adicional')
        ->where('id_factura','=',$factura_bd->id)
        ->delete();

        while($cont < count($nombre_paquete)){
            
            if(is_null($nombre_paquete[$cont])==false){
                $paquetes = new PrePaqueteBultos;
                $paquetes->id_factura = $factura_bd->id;
                $paquetes->detalle = $nombre_paquete[$cont];
                $paquetes->valor_detalle = $detalle_paquete[$cont];
                $paquetes->save();
            }
            $cont++;
        }
    
        //Factura detalle de productos
        $codigo_producto = $request->{'codigo_pro'};
        $nandina_producto = $request->{'nandina'};
        $cantidad_producto = $request->{'cantidad'};  
        $descripcion_producto = $request->{'descripcion'};
        $unidad_medida_producto  = $request->{'unidad_medida'};
        $precio_unitario_producto = $request->{'precio_uni'};
        $valor_agregado = $request->{'valor_agregado'};
        $valor_re_exportacion = $request->{'valor_reexport'};
        $precio_unitario_sin = $request->{'precio_uni_sin'};
        $subtotal_producto = $request->{'subtotal'};
        
        $detallesubpro = $request->{'subproducto'};
        $subproductocodigo = $request->{"subproductocodigo"};

        $cont_pro_bd = 0;
        while($cont_pro_bd < count($codigo_producto)){

            $cantidad = (double) str_replace(',', '', $cantidad_producto[$cont_pro_bd]);
            $precio_unitario = (double) str_replace(',', '', $precio_unitario_producto[$cont_pro_bd]);
            $subtotal_producto1 = (double) str_replace(',', '',$subtotal_producto[$cont_pro_bd]);
            $valor_agregado1 =(double) str_replace(',', '',$valor_agregado[$cont_pro_bd]);
            $valor_re_exportacion1 = (double) str_replace(',', '',$valor_re_exportacion[$cont_pro_bd]);
            $precio_unitario_sin1 = (double) str_replace(',', '',$precio_unitario_sin[$cont_pro_bd]);
           

            $producto_detalle = new PreFacturaDetalle;
            $producto_detalle->id_factura = $factura_bd->id;
            $producto_detalle->id_producto = $codigo_producto[$cont_pro_bd];
            $producto_detalle->cantidad = $cantidad;
            $producto_detalle->precio_unitario =  $precio_unitario;
            
            $producto_detalle->valor_agregado = $valor_agregado1;
            $producto_detalle->valor_re_exportacion = $valor_re_exportacion1;
            $producto_detalle->precio_unitario_sin = $precio_unitario_sin1;

            $producto_detalle->subtotal = $subtotal_producto1;
    
            $producto_detalle->dessubpro = $detallesubpro[$cont_pro_bd];
            $producto_detalle->codigosubpro = $subproductocodigo[$cont_pro_bd];
           
            $producto_detalle->save();
            
            $cont_pro_bd++;
        }

        $cont_subpro = 0;
        while($cont_subpro < count($subproductocodigo)){
            
            $arraysubpro = explode(",", $detallesubpro[$cont_subpro]);
            $arraysubprocod = explode(",", $subproductocodigo[$cont_subpro]);
            //dd($arraysubpro, $arraysubprocod);
            foreach( $arraysubprocod as $asubprocod){
                if( $asubprocod != ""){
                    $subproducto_detalle = new PreSubProductosFactura;
                    $subproducto_detalle->id_factura = $factura_bd->id;
                    $subproducto_detalle->id_producto = $codigo_producto[$cont_subpro];
                    $subproducto_detalle->id_sub_producto = $asubprocod;
                    $subproducto_detalle->save();
                }
               
            }  
            $cont_subpro++; 
        }

        $contAdi1 = 0;
        while($contAdi1 < count( $infoadicional)){
            
            $info_adicional = new PreInformacionAdicional;
            $info_adicional->id_factura = $factura_bd->id;
            $info_adicional->descripcion =  $infoadicional[$contAdi1];
            $info_adicional->descripcion_detalle = $detalleadicional[$contAdi1];

            $info_adicional->save();
        
            $contAdi1++; 
            
        }


        return redirect('prefactura')->with('status','REGISTRO GUARDADO CON EXITO');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
