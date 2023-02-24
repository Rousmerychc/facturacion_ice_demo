<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\UnidadMedida;
use App\Actividades;
use App\ActiviadesSector;
use App\LeyendasFacturacion;
use App\MensajesServicios;
use App\ParametricaProductosServicios;
use App\ParametricasEventosSignificativos;
use App\ParametricaMotivoAnulacion;
use App\ParametricaPaisOrigen;
use App\ParametricaDocumentoTipoIdentidad;
use App\ParametricaTipoDocumentoSector;
use App\ParametricaTipoEmision;
use App\ParametricaTipoHabitacion;
use App\ParametricaTipoMetodoPago;
use App\ ParametricaTipoMoneda;
use App\ParametricaTipoPuntoVenta;
use App\ParametricaTiposFactura;

use App\Productos;
use App\Cuis;
use Carbon\Carbon;
use App\Datos;
use App\Exports\ProductoExcel;

class SincronizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
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
    public function cuis_usuario(){
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d H:i:s');
        $cuisbd = Cuis::where('fecha_hora','>',$fechahoyhora )
         ->where('id_sucursal','=',auth()->user()->id_sucursal)
         ->where('punto_venta','=',auth()->user()->punto_venta)
         ->orderby('id','DESC')
         ->first();
         return ($cuisbd);
    }

    public function peticion(){
        $wsdl =$this->fac_sincronizacion;
        
        $token = $this->token;
        $cuis = $this->cuis_usuario();

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
            'codigoAmbiente'   => 2,
            'codigoPuntoVenta' => 0 ,
            'codigoSistema'    => $this->codigo_sistema,
            'codigoSucursal'   => 0,
            'cuis'             =>  $cuis->codigo_cuis,
            'nit'              => $this->nit, 
        );

        $peticion = [$client, $SolicitudSincronizacion];
        return  $peticion;

    }
     
    public function copia_a_excel(){

        $producto = ParametricaProductosServicios::all();
        return (new  ProductoExcel($producto))->download('Producto_deral.xlsx');
    }
    public function actividades()
    {
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $actividades = $client->sincronizarActividades(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $respListaActividades = $actividades->{'RespuestaListaActividades'};
        $lista_actividad=$respListaActividades->{'listaActividades'};
        $cont = 0;
        // dd($respListaActividades);
        foreach( $lista_actividad as $acti){
            $actividad = new Actividades;
            
            $acti1 = $respListaActividades->{'listaActividades'}[$cont];

            $actividad->codigo = $acti1->{'codigoCaeb'};
            $actividad->descripcion = $acti1->{'descripcion'};
            $actividad->tipo_actividad = $acti1->{'tipoActividad'};
            $actividad->save();
            $cont ++;
        }
       
    }

    public function ListaActividadesDocumentoSector()
    {
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $objActivDocSector  = $client->sincronizarListaActividadesDocumentoSector(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );

        $respActividadesSector = $objActivDocSector->{'RespuestaListaActividadesDocumentoSector'};
        $lista_respActividadesSector = $respActividadesSector->{'listaActividadesDocumentoSector'};

        $cont = 0;

        foreach( $lista_respActividadesSector as $actisector){
            $acti_docu_sector = new ActiviadesSector;

            $acti_sector = $respActividadesSector->{'listaActividadesDocumentoSector'}[$cont];

            $acti_docu_sector->codigo_actividad = $acti_sector->{'codigoActividad'};
            $acti_docu_sector->codigo_documento_sector = $acti_sector->{'codigoDocumentoSector'};
            $acti_docu_sector->tipo_documento_sector = $acti_sector->{'tipoDocumentoSector'};

            $acti_docu_sector->save();

            $cont++;
        }
    }

    public function sincronizarListaLeyendasFactura(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $objLeyendas = $client->sincronizarListaLeyendasFactura(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaLeyendas = $objLeyendas->{'RespuestaListaParametricasLeyendas'};
        $leyenda1 = $parametricaLeyendas->{'listaLeyendas'};

        $cont = 0;

        foreach( $leyenda1 as $leye){
            $leyenda = new LeyendasFacturacion;

            $resleyenda = $parametricaLeyendas->{'listaLeyendas'}[$cont];

            $leyenda->codigo_actividad = $resleyenda->{'codigoActividad'};
            $leyenda->descripcion_leyenda = $resleyenda->{'descripcionLeyenda'};
            
            $leyenda->save();

            $cont++;
        }
    }

    public function ListaMensajesServicios(){

        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $objMensajes = $client->sincronizarListaMensajesServicios(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaMensajes = $objMensajes->{'RespuestaListaParametricas'};
        $listaMensajes = $parametricaMensajes->{'listaCodigos'};
        

        $cont = 0;

        foreach( $listaMensajes as $leye){
            $mensajes = new MensajesServicios;

            $resmensajes = $parametricaMensajes->{'listaCodigos'}[$cont];

            $mensajes->codigo_clasificador = $resmensajes->{'codigoClasificador'};
            $mensajes->descripcion = $resmensajes->{'descripcion'};
            
            $mensajes->save();

            $cont++;
        }
    }

    public function ListaProductosServicios(){

        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $productos = $client->sincronizarListaProductosServicios(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );

        $listaProductos = $productos->{'RespuestaListaProductos'};
        $codigosProd = $listaProductos->{'listaCodigos'};

        $cont = 0;
        //dd($codigosProd);
        $fecha=Carbon::now(-4)->format('Y-m-d');
        foreach( $codigosProd as $cdp){
            $productos = new ParametricaProductosServicios;
            //$productos = new Productos;
            $productos1 = $listaProductos->{'listaCodigos'}[$cont];
            $productos->codigo_actividad = $productos1->{'codigoActividad'};
            $productos->codigo_producto = $productos1->{'codigoProducto'};
            $productos->descripcion_producto = $productos1->{'descripcionProducto'};
            // $productos->codigo_unidad_medida = 58;
            // $productos->unidad_medida = 'UNIDAD (SERVICIOS)';
            // $productos->estado = 0;
            // $productos->usuario = auth()->user()->id;
            // $productos->fecha = $fecha;
            
            $productos->save();
            $cont++;
        }
    }

    public function ParametricaEventosSignificativos(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $objEventoSignificativo  = $client->sincronizarParametricaEventosSignificativos(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaEventos = $objEventoSignificativo->{'RespuestaListaParametricas'};
        $listaEventos = $parametricaEventos->{'listaCodigos'};
        

        $cont = 0;

        foreach( $listaEventos as $cdp){
            $eventos = new ParametricasEventosSignificativos;
            $listaEventos = $parametricaEventos->{'listaCodigos'}[$cont];

            $eventos->codigo_clasificador = $listaEventos->{'codigoClasificador'};
            $eventos->descripcion = $listaEventos->{'descripcion'};

            $eventos->save();

            $cont++;
        }
    }


    public function ParametricaMotivoAnulacion(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];
        $objMotivoAnulacion  = $client->sincronizarParametricaMotivoAnulacion(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );

        $parametricaMotivoAnulacion = $objMotivoAnulacion->{'RespuestaListaParametricas'};
        $listaMotivoAnulacion = $parametricaMotivoAnulacion->{'listaCodigos'};

        $cont = 0;

        foreach( $listaMotivoAnulacion as $lma){
            $motivoAnulacion = new ParametricaMotivoAnulacion;
            $listaMotivoAnulacion = $parametricaMotivoAnulacion->{'listaCodigos'}[$cont];

            $motivoAnulacion->codigo_clasificador = $listaMotivoAnulacion->{'codigoClasificador'};
            $motivoAnulacion->descripcion = $listaMotivoAnulacion->{'descripcion'};

            $motivoAnulacion->save();
            $cont++;

        }

    }

    public function ParametricaPaisOrigen(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $objPais  = $client->sincronizarParametricaPaisOrigen(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaPais = $objPais->{'RespuestaListaParametricas'};
        $listaPaises = $parametricaPais->{'listaCodigos'};
      

        $cont = 0;
        foreach($listaPaises as $lp){
            $pais = new ParametricaPaisOrigen;
            $listaPaises = $parametricaPais->{'listaCodigos'}[$cont];
            $pais->codigo_clasificador = $listaPaises ->{'codigoClasificador'};
            $pais->descripcion = $listaPaises->{'descripcion'};
            $pais->save();
            $cont++;
        }
    }

    public function ParametricaDocumentoTipoIdentidad(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];
        
        $tipoFacDocID = $client->sincronizarParametricaTipoDocumentoIdentidad(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $tipoDocID= $tipoFacDocID->{'RespuestaListaParametricas'};
        $listaDocID = $tipoDocID->{'listaCodigos'};

        $cont = 0;

        foreach($listaDocID as $ldi){
            $documentoidentidad = new ParametricaDocumentoTipoIdentidad;
            $listaDocID = $tipoDocID->{'listaCodigos'}[$cont];
            $documentoidentidad->codigo_clasificador = $listaDocID->{'codigoClasificador'};
            $documentoidentidad->descripcion = $listaDocID->{'descripcion'};
            $documentoidentidad->save();
            $cont++;
        }
    }

    public function ParametricaTipoDocumentoSector(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $doc_sector = $client->sincronizarParametricaTipoDocumentoSector(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaSectores = $doc_sector->{'RespuestaListaParametricas'};
        $Sector1 = $parametricaSectores->{'listaCodigos'};
    
        $cont = 0;

        foreach($Sector1 as $sec){
            $tipodocsector = new ParametricaTipoDocumentoSector;
            $Sector1 = $parametricaSectores->{'listaCodigos'}[$cont];
            $tipodocsector->codigo_clasificador = $Sector1->{'codigoClasificador'};
            $tipodocsector->descripcion = $Sector1->{'descripcion'};

            $tipodocsector->save();
            $cont++;
        }
    }

    public function ParametricaTipoEmision(){
        
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $tipoEmision = $client->sincronizarParametricaTipoEmision(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaEmision = $tipoEmision->{'RespuestaListaParametricas'};
        $emision1 = $parametricaEmision->{'listaCodigos'};
        $cont =0;
        foreach($emision1 as $emi){
            $tipoemision = new ParametricaTipoEmision;
            $emision1 = $parametricaEmision->{'listaCodigos'}[$cont];
            $tipoemision->codigo_clasificador = $emision1->{'codigoClasificador'};
            $tipoemision->descripcion = $emision1->{'descripcion'};
            $tipoemision->save();
            $cont++;
        }
    }

    public function ParametricaTipoHabitacion(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];
        
        $objHabitacion  = $client->sincronizarParametricaTipoHabitacion(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaHabitacion = $objHabitacion->{'RespuestaListaParametricas'};
        $listaHabitaciones = $parametricaHabitacion->{'listaCodigos'};
        $cont =0;
        foreach($listaHabitaciones as $lh){
            $habitacion = new ParametricaTipoHabitacion;

            $listaHabitaciones = $parametricaHabitacion->{'listaCodigos'}[$cont];
            $habitacion->codigo_clasificador = $listaHabitaciones->{'codigoClasificador'};
            $habitacion->descripcion = $listaHabitaciones->{'descripcion'};
            $habitacion->save();
            $cont++;
        }
    }

    public function ParametricaTipoMetodoPago(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];
        $objMetodoPago = $client->sincronizarParametricaTipoMetodoPago(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaMetodPago = $objMetodoPago->{'RespuestaListaParametricas'};
        $MetodoPago1 = $parametricaMetodPago->{'listaCodigos'};
        
        $cont = 0;

        foreach($MetodoPago1 as $mp){
            $tipopago = new ParametricaTipoMetodoPago;
            $MetodoPago1 = $parametricaMetodPago->{'listaCodigos'}[$cont];
            $tipopago->codigo_clasificador = $MetodoPago1->{'codigoClasificador'};
            $tipopago->descripcion = $MetodoPago1->{'descripcion'};
            $tipopago->save();
            $cont++;
        }
    }

    public function ParametricaTipoMoneda(){
        
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];
            $objTipoMoneda = $client->sincronizarParametricaTipoMoneda(
                array(
                    "SolicitudSincronizacion" => $SolicitudSincronizacion,
                )
            );
        $parametricaMonedas = $objTipoMoneda->{'RespuestaListaParametricas'};
        $Moneda1 = $parametricaMonedas->{'listaCodigos'};
        
        $cont = 0;
        foreach($Moneda1 as $m){
            $moneda = new ParametricaTipoMoneda;
            $Moneda1 = $parametricaMonedas->{'listaCodigos'}[$cont];
            $moneda->codigo_clasificador = $Moneda1->{'codigoClasificador'};
            $moneda->descripcion = $Moneda1->{'descripcion'};
            $moneda->save();
            $cont++;
        }
    }
    public function ParametricaTipoPuntoVenta(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];
        $objPtoVenta  = $client->sincronizarParametricaTipoPuntoVenta(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaPtoVenta = $objPtoVenta->{'RespuestaListaParametricas'};
        $listaPtoVenta = $parametricaPtoVenta->{'listaCodigos'};
        $cont = 0;

        foreach($listaPtoVenta as $lpv){
            $puntoventa = new ParametricaTipoPuntoVenta;
            $listaPtoVenta = $parametricaPtoVenta->{'listaCodigos'}[$cont];
            $puntoventa->codigo_clasificador = $listaPtoVenta->{'codigoClasificador'};
            $puntoventa->descripcion = $listaPtoVenta->{'descripcion'};
            $puntoventa->save();
            $cont++;
        }
    }

    public function ParametricaTiposFactura(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $objTipoFactura  = $client->sincronizarParametricaTiposFactura(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaTipoFac = $objTipoFactura->{'RespuestaListaParametricas'};
        $listaTipoFac = $parametricaTipoFac->{'listaCodigos'};

        $cont =0;
        foreach($listaTipoFac as $ltf){
            $tipofac = new ParametricaTiposFactura;
            $listaTipoFac = $parametricaTipoFac->{'listaCodigos'}[$cont];

            $tipofac->codigo_clasificador = $listaTipoFac->{'codigoClasificador'};
            $tipofac->descripcion = $listaTipoFac->{'descripcion'};

            $tipofac->save();

            $cont++;

        }
    }
    public function ParametricaUnidadMedida(){
        $client = $this->peticion()[0];
        $SolicitudSincronizacion = $this->peticion()[1];

        $objUnidadMedida = $client->sincronizarParametricaUnidadMedida(
            array(
                "SolicitudSincronizacion" => $SolicitudSincronizacion,
            )
        );
        $parametricaUnidadMedida = $objUnidadMedida->{'RespuestaListaParametricas'};
        $arrayUnidadMedida = $parametricaUnidadMedida->{'listaCodigos'};
        //dd($parametricaUnidadMedida);
        $cont = 0;
        
        foreach( $arrayUnidadMedida as $uni){
            $unidadmedida = new UnidadMedida;

            $unidMedida58 = $parametricaUnidadMedida->{'listaCodigos'}[$cont];

            $codUnidadMedida58 = $unidMedida58->{'codigoClasificador'};
            $codUnidadMedida581 = $unidMedida58->{'descripcion'};
            $unidadmedida->codigo = $codUnidadMedida58;
            $unidadmedida->descripcion = $codUnidadMedida581;
            $unidadmedida->save();
            $cont ++;

        }
    }
    public function actualizar(){
        // $consulta = DB::select('TRUNCATE parametricas_motivo_anulacion');
        // $consulta = DB::select('TRUNCATE parametrica_actividades');
        // $consulta = DB::select('TRUNCATE parametrica_eventos_significativos');
        // $consulta = DB::select('TRUNCATE parametrica_leyendas_factura');
        // $consulta = DB::select('TRUNCATE parametrica_lista_actividades_documento_sector');
        // $consulta = DB::select('TRUNCATE parametrica_mensajes_servicios');
        // $consulta = DB::select('TRUNCATE parametrica_pais_origen');
        // $consulta = DB::select('TRUNCATE parametrica_productos_servicios');
        // $consulta = DB::select('TRUNCATE parametrica_tipo_documento_identidad');
        // $consulta = DB::select('TRUNCATE parametrica_tipo_documento_sector');
        // $consulta = DB::select('TRUNCATE parametrica_tipo_emision');
        // $consulta = DB::select('TRUNCATE parametrica_tipo_factura');
        // $consulta = DB::select('TRUNCATE parametrica_tipo_habitacion');
        // $consulta = DB::select('TRUNCATE parametrica_tipo_moneda');
        // $consulta = DB::select('TRUNCATE parametrica_tipo_pago');
        // $consulta = DB::select('TRUNCATE parametrica_tipo_punto_venta');
        // $consulta = DB::select('TRUNCATE parametrica_unidad_medida');

        // $this->actividades();
        // $this->ListaActividadesDocumentoSector();
        // $this->sincronizarListaLeyendasFactura();
        // $this->ListaMensajesServicios();
        // $this->ListaProductosServicios();
        // $this->ParametricaEventosSignificativos();
        // $this->ParametricaMotivoAnulacion();
        // $this->ParametricaPaisOrigen();
        // $this->ParametricaDocumentoTipoIdentidad();
        // $this->ParametricaTipoDocumentoSector();
        // $this->ParametricaTipoEmision();
        // $this->ParametricaTipoHabitacion();
        // $this->ParametricaTipoMetodoPago();
        // $this->ParametricaTipoMoneda();
        // $this->ParametricaTipoPuntoVenta();
        // $this->ParametricaTiposFactura();
        // $this->ParametricaUnidadMedida();
        sleep(5);
        return redirect('sincronizacion')->with('status', 'Tablas Parametricas Actualizadas');
        var_dump("paso sin problemas");
    }

    public function mostrartabla(Request $request){
        $id=$request->get('dato');
        switch ($id) {
            case '1':
                $consulta = DB::select('select codigo, descripcion, tipo_actividad as tipotres from parametrica_actividades');
                break;
            case '2':
                $consulta = DB::select("select codigo_actividad as codigo, codigo_documento_sector as descripcion, tipo_documento_sector as tipotres from parametrica_lista_actividades_documento_sector");
                break;
            case '3':
                $consulta = DB::select("select codigo_actividad as codigo, descripcion_leyenda as descripcion, '' as tipotres from parametrica_leyendas_factura");
                break;
            case '4':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_mensajes_servicios");
                break;
            case '5':
                $consulta = DB::select("select codigo_actividad as codigo, codigo_producto as descripcion, descripcion_producto as tipotres from parametrica_productos_servicios");
                break; 
            case '6':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_eventos_significativos");
                break;
            case '7':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametricas_motivo_anulacion");
                break;
            case '8':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_pais_origen");
                break;
            case '9':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_tipo_documento_identidad");
                break;
            case '10':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_tipo_documento_sector");
                break;
            case '11':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_tipo_emision");
                break;
            case '12':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_tipo_habitacion");
                break;
            case '13':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_tipo_pago");
                break;
            case '14':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_tipo_moneda");
                break;
            case '15':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_tipo_punto_venta");
                break; 
            case '16':
                $consulta = DB::select("select codigo_clasificador as codigo, descripcion, '' as tipotres from parametrica_tipo_factura");
                break;
            case '17':
                $consulta = DB::select("select codigo, descripcion, '' as tipotres from parametrica_unidad_medida");
                break;
               
            default:
                # code...
                break;
        }
       
        return response(json_encode(array('consulta' =>$consulta)),200)->header('Content-type','text/plain');
    }
    public function index()
    {
        return view('sincronizacion.sincronizacion');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
