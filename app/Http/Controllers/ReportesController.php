<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Datos;


use Carbon\Carbon;
use App\UnidadMedida;

use App\Productos;
use App\ProductoPrecio;
use App\Facturacion;
use App\FacturacionDetalle;
use App\Sucursal;

use Illuminate\Support\Facades\DB;

use PDF;
use Maatwebsite\Excel\Facades\Excel;



class ReportesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //FUNCION PARA VALIDAR LOGIN
    public function __construct() {
        $this->middleware('auth');
    }
    public function index()
    {
        //
        $fecha = Carbon::now(-4)->format('Y-m-d');
        $sucursal = Sucursal::all();
        $productos = Productos::all();

        return view('reportes.reporte_index',['fecha'=>$fecha, 'sucursal'=>$sucursal, 'productos'=>$productos]);
    }
    public function libro_ventas(Request $request){
        $fechaini = $request->{'fechai1'};
        $fechafin = $request->{'fechaf1'};
        $documento = $request->{'proceso1'};
        $sucursal = $request->{'sucursal1'};

        $factura = Facturacion::whereDate('factura.fecha','>=',$fechaini)
        ->whereDate('factura.fecha','<=',$fechafin)
        ->where('id_sucursal','=',$sucursal)
        ->orderBy('id','ASC')
        ->get();
       
        //dd($factura);
       
        if($documento == 0){
            $data =[ 'fechaini' =>$fechaini, 'fechafin' => $fechafin ,'factura'=>$factura];
            $pdf = PDF::loadView('reportes.libro_ventas',$data);
            $pdf->setPaper("letter", "portrait");
            return $pdf->stream('Reporte.pdf');
        }
        else{
            return (new  MovimientoDiario($fechaini,$fechafin,$factura,$detalle))->download('MovimientoDiario.xlsx');
        }
        

       // return view('reportes.movimiento_diario',['reporte'=>$reporte]);
       
    }

    public function ventas_litros(Request $request){
        
        //return ('hola');
        
        $fechaini = $request->{'fechai2'};
        $fechafin = $request->{'fechaf2'};
        $documento = $request->{'proceso2'};
        $sucursal = $request->{'sucursal'};

        $consulta = DB::select(DB::raw("SELECT f.id_factura,f.fecha,f.monto_total,f.monto_total_sujeto_iva,f.ice_porcentual,f.ice_especial,f.estado,f.id_sucursal,
        fd.*,p.cantidad_litros_x_unidad,g.id as id_grupo, g.descripcion as descripcion_grupo FROM factura as f 
        JOIN factura_detalle as fd ON f.id = fd.id_tabla_factura 
        JOIN productos as p ON p.id = fd.codigo_producto_empresa 
        JOIN grupos as g ON p.id_grupo_porcentual = g.id 
        WHERE f.fecha<=? AND f.fecha>=? AND f.estado = 0 AND f.id_sucursal = ? ORDER BY g.id, f.fecha"),[$fechafin,$fechaini,$sucursal]);
       
        // dd($fechafin, $fechaini, $consulta, $sucursal);
       
        if($documento == 0){
            $data =[ 'fechaini' =>$fechaini, 'fechafin' => $fechafin ,'consulta'=>$consulta];
            $pdf = PDF::loadView('reportes.ventas_litros',$data);
            $pdf->setPaper("letter", "portrait");
            return $pdf->stream('Reporte.pdf');
        }
        else{
            return (new  MovimientoDiario($fechaini,$fechafin,$factura,$detalle))->download('MovimientoDiario.xlsx');
        }
    }

    public function movimiento_diario_cliente(Request $request){
        
        //return ('hola');
        
        $fechaini = $request->{'fechai2'};
        $fechafin = $request->{'fechaf2'};
        $documento = $request->{'proceso2'};
        $cliente = $request->{'cliente'};

        $cliente_datos = Cliente::where('id','=',$cliente)->first();
    
        $factura = Facturacion::whereDate('factura.fecha','>=',$fechaini)
        ->whereDate('factura.fecha','<=',$fechafin)
        ->where('factura.codigo_cliente','=',$cliente)
        ->where('estado','=',0)
        ->orderBy('id','ASC')
        ->get();
       
        $de = array();
        foreach($factura as $fac){
            array_push($de, $fac->id);
        }

        $detalle = FacturaDetalle::join('productos','productos.id','=','factura_detalle.id_producto')       
        ->whereIn('id_factura',$de)
        ->get();
       
        if($documento == 0){
            $data =[ 'fechaini' =>$fechaini, 'fechafin' => $fechafin ,'factura'=>$factura, 'detalle'=>$detalle,'cliente_datos'=>$cliente_datos];
            $pdf = PDF::loadView('reportes.movimiento_diario_cliente',$data);
            $pdf->setPaper('a4', 'landscape');
           
            return $pdf->stream('Reporte.pdf');
        }
        else{
            return (new  MovimientoDiarioCliente($fechaini,$fechafin,$factura,$detalle,$cliente_datos))->download('MovimientoDiarioCliente.xlsx');
        }
        //dd($factura);
    }

    public function movimiento_diario_item(Request $request){
        $fechaini = $request->{'fechai3'};
        $fechafin = $request->{'fechaf3'};
        $documento = $request->{'proceso3'};
        $item = $request->{'item'};

        $datos_item = Productos::where('id','=',$item)->first();

        $detalle = FacturaDetalle::join('factura','factura.id','=','factura_detalle.id_factura')
        ->whereDate('factura.fecha','>=',$fechaini)
        ->whereDate('factura.fecha','<=',$fechafin)
        ->where('factura_detalle.id_producto','=',$item)  
        ->where('estado','=',0)   
        ->get();
      // dd ($detalle);

        if($documento == 0){
            $data =[ 'fechaini' =>$fechaini, 'fechafin' => $fechafin, 'detalle'=>$detalle,'datos_item'=>$datos_item];
            $pdf = PDF::loadView('reportes.movimiento_diario_item',$data);
            $pdf->setPaper("letter", "portrait");
            return $pdf->stream('Reporte.pdf');
        }
        else{
            return (new  MovimientoDiarioItem($fechaini,$fechafin,$detalle,$datos_item))->download('MovimientoDiarioItem.xlsx');
        }
    }

    public function resumen_item(Request $request){
        $fechaini = $request->{'fechai4'};
        $fechafin = $request->{'fechaf4'};
        $documento = $request->{'proceso4'};
       
        $consulta =  DB::select('SELECT productos.descripcion, t4.* FROM
        (SELECT t3.id_producto, SUM(cantidad) as cantidad, SUM(subtotal) as total FROM ( 
        SELECT t1.*, t2.fecha FROM
        (SELECT factura.id, factura.fecha FROM factura WHERE factura.fecha >= ? AND factura.fecha<= ? AND factura.estado =0) as t2
        JOIN 
        (SELECT factura_detalle.id_factura, factura_detalle.id_producto, factura_detalle.cantidad,factura_detalle.subtotal FROM factura_detalle) as t1
        ON t2.id = t1.id_factura) as t3
         GROUP BY id_producto) as t4
         JOIN
         productos ON productos.id = t4.id_producto' ,[$fechaini,$fechafin]);

        if($documento == 0){
            $data =[ 'fechaini' =>$fechaini, 'fechafin' => $fechafin, 'consulta' =>$consulta];
            $pdf = PDF::loadView('reportes.resumen_item',$data);
            $pdf->setPaper("letter", "portrait");
            return $pdf->stream('Reporte.pdf');
        }
        else{
            return (new ResumenItem($fechaini,$fechafin,$consulta))->download('ResumensItem.xlsx');
        }        
    }

    public function poliza_exportacion(Request $request){
        $fechaini = $request->{'fechai5'};
        $fechafin = $request->{'fechaf5'};      

        $consulta = DB::select(' SELECT * FROM 
        (
            SELECT 
            t1.id_factura,
            
            SUM(t1.diezKTc) as "diezKTc",
            SUM(t1.catorceKTc) as "catorceKTc",
            SUM(t1.dieciochoKTc) as "dieciochoKTc",
            SUM(t1.plata925c) as "plata925c",
            SUM(t1.nueveKTc) as "nueveKTc"
            FROM 
            (
                 SELECT productos.tipo_producto,factura_detalle.id_factura, 
                 CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.cantidad end as "diezKTc",
                 CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.cantidad end as "catorceKTc",
                 CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.cantidad end as "dieciochoKTc",
                 CASE WHEN productos.tipo_producto = "S925" then factura_detalle.cantidad end as "plata925c",
                 CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.cantidad end as "nueveKTc"
                 FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto ) as t1 
                 GROUP BY t1.id_factura
        ) as t3
         
        JOIN
        (
            SELECT 
            t2.id_factura,
            SUM(t2.diezKTm) as "diezKTm",
            SUM(t2.catorceKTm) as "catorceKTm",
            SUM(t2.dieciochoKTm) as "dieciochoKTm",
            SUM(t2.plata925m) as "plata925m",
            SUM(t2.nueveKTm) as "nueveKTm"
            FROM 
            (
             SELECT productos.tipo_producto,factura_detalle.id_factura, 
             CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.subtotal end as "diezKTm",
             CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.subtotal end as "catorceKTm",
             CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.subtotal end as "dieciochoKTm",
             CASE WHEN productos.tipo_producto = "S925" then factura_detalle.subtotal end as "plata925m",
             CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.subtotal end as "nueveKTm"
             FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
            ) as t2
             GROUP BY t2.id_factura
        ) as t4
        
         ON t3.id_factura = t4.id_factura
         
         JOIN
         (
              SELECT 
            t5.id_factura,
            SUM(t5.diezKTcon) as "diezKTcon",
            SUM(t5.catorceKTcon) as "catorceKTcon",
            SUM(t5.dieciochoKTcon) as "dieciochoKTcon",
            SUM(t5.plata925con) as "plata925con",
            SUM(t5.nueveKTcon) as "nueveKTcon"
            FROM
            (
             SELECT productos.tipo_producto,factura_detalle.id_factura, 
             CASE WHEN productos.tipo_producto = "10KT" then productos.conversion end as "diezKTcon",
             CASE WHEN productos.tipo_producto = "14KT" then productos.conversion end as "catorceKTcon",
             CASE WHEN productos.tipo_producto = "18KT" then productos.conversion end as "dieciochoKTcon",
             CASE WHEN productos.tipo_producto = "S925" then productos.conversion end as "plata925con",
             CASE WHEN productos.tipo_producto = "9KT" then productos.conversion end as "nueveKTcon"
             FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
            ) as t5
             GROUP BY t5.id_factura   
         ) as t6
         
         ON t3.id_factura = t6.id_factura
         
         JOIN
         (
             SELECT factura_detalle.id_factura, SUM(factura_detalle.valor_agregado) as suma 
             FROM factura_detalle 
            GROUP BY factura_detalle.id_factura
         ) as t8  
         
        ON t3.id_factura = t8.id_factura
        JOIN
        (
            SELECT id_factura,
            SUM(factura_detalle.valor_agregado) as vagre, SUM(factura_detalle.valor_re_exportacion) as vrex
            FROM factura_detalle GROUP BY factura_detalle.id_factura 
        )as t9
        ON 
        t9.id_factura = t3.id_factura
        JOIN
        factura ON factura.id = t3.id_factura
        JOIN
        informacion_adicional
        ON factura.id = informacion_adicional.id_factura
        WHERE informacion_adicional.descripcion = "EXPORTACION"
        AND 
        factura.fecha >= ? AND factura.fecha <=? ORDER BY factura.id , factura.fecha', [$fechaini,$fechafin]
        
        );
        //dd($consulta);
       
    // Carbon::setLocale('es');
    // $fecha = Carbon::parse($fechaini);
    // $mes = $fecha->formatLocalized('%B');
    // dd($mes);
       //dd($consulta);
        //return view('reportes.poliza_exportacion',['fechaini'=>$fechaini, 'fechafin'=>$fechafin, 'consulta'=>$consulta]);
        return (new PolizaExportacion($fechaini,$fechafin,$consulta))->download('PolizaExportacion.xlsx');

    }

    public function poliza_exportacio_resumen(Request $request){
        $año = $request->{'año'};
        $fechai = $año.'-04-01';
        $añoi = (int)$año +1;
        $fechaf = (string)$añoi.'-03-31';
       
        /****consulta para exportaciones totales */
            $consulta = DB::select(' SELECT 
            MONTH(t7.fecha) as fecha1, t7.fecha ,"" as prueba,
            SUM(t7.diezKTc) as diezc, SUM(t7.catorceKTc) as catorcec ,SUM(t7.dieciochoKTc) as dieciochoc, SUM(t7.plata925c) as platac, SUM(t7.nueveKTc) as nuevec,
            SUM(t7.diezKTm) as diezm, SUM(t7.catorceKTm) as catorcem ,SUM(t7.dieciochoKTm) as dieciochom, SUM(t7.plata925m) as platam, SUM(t7.nueveKTm) as nuevem
                    FROM(
                        SELECT t3.diezKTc,t3.catorceKTc,t3.dieciochoKTc,t3.plata925c,t3.nueveKTc, t4.diezKTm, t4.catorceKTm, t4.dieciochoKTm, t4.plata925m, t4.nueveKTm,
                        factura.fecha, factura.ritex FROM 
                            (
                                SELECT 
                                t1.id_factura,
                                SUM(t1.diezKTc) as "diezKTc",
                                SUM(t1.catorceKTc) as "catorceKTc",
                                SUM(t1.dieciochoKTc) as "dieciochoKTc",
                                SUM(t1.plata925c) as "plata925c",
                                SUM(t1.nueveKTc) as "nueveKTc"
                                FROM 
                                (
                                    SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                    CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.cantidad end as "diezKTc",
                                    CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.cantidad end as "catorceKTc",
                                    CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.cantidad end as "dieciochoKTc",
                                    CASE WHEN productos.tipo_producto = "S925" then factura_detalle.cantidad end as "plata925c",
                                    CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.cantidad end as "nueveKTc"
                                    FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto ) as t1 
                                    GROUP BY t1.id_factura
                            ) as t3
                            
                            JOIN
                            (
                                SELECT 
                                t2.id_factura,
                                SUM(t2.diezKTm) as "diezKTm",
                                SUM(t2.catorceKTm) as "catorceKTm",
                                SUM(t2.dieciochoKTm) as "dieciochoKTm",
                                SUM(t2.plata925m) as "plata925m",
                                SUM(t2.nueveKTm) as "nueveKTm"
                                FROM 
                                (
                                SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.subtotal end as "diezKTm",
                                CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.subtotal end as "catorceKTm",
                                CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.subtotal end as "dieciochoKTm",
                                CASE WHEN productos.tipo_producto = "S925" then factura_detalle.subtotal end as "plata925m",
                                CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.subtotal end as "nueveKTm"
                                FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
                                ) as t2
                                GROUP BY t2.id_factura
                            ) as t4
                            
                            ON t3.id_factura = t4.id_factura
                             
                            JOIN
                            factura ON factura.id = t3.id_factura
                            WHERE factura.estado = 0 AND factura.fecha >= ? AND factura.fecha <= ? 
                            ) as t7 GROUP BY MONTH(t7.fecha) ORDER BY t7.fecha'
                    
            , [$fechai,$fechaf]);
        $consulta2 = DB::select('SELECT MONTH(t7.fecha) as fecha,
                SUM(t7.diezKTc*t7.diezKtcon) as "diez",
                SUM(t7.catorceKTc*t7.catorceKTcon)  as "catorce",
                SUM(t7.dieciochoKTc*t7.dieciochoKTcon)  as "dieciocho"
                FROM(
                SELECT t3.diezKTc,t3.catorceKTc,t3.dieciochoKTc,t3.plata925c,t3.nueveKTc,
                    t6.diezKTcon,t6.catorceKTcon,t6.dieciochoKTcon,t6.plata925con,t6.nueveKTcon, factura.fecha, factura.ritex FROM 
                        (
                            SELECT 
                            t1.id_factura,
                            SUM(t1.diezKTc) as "diezKTc",
                            SUM(t1.catorceKTc) as "catorceKTc",
                            SUM(t1.dieciochoKTc) as "dieciochoKTc",
                            SUM(t1.plata925c) as "plata925c",
                            SUM(t1.nueveKTc) as "nueveKTc"
                            FROM 
                            (
                                    SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                    CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.cantidad end as "diezKTc",
                                    CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.cantidad end as "catorceKTc",
                                    CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.cantidad end as "dieciochoKTc",
                                    CASE WHEN productos.tipo_producto = "S925" then factura_detalle.cantidad end as "plata925c",
                                    CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.cantidad end as "nueveKTc"
                                    FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto ) as t1 
                                    GROUP BY t1.id_factura
                        ) as t3
                            
                        JOIN
                        (
                            SELECT 
                            t2.id_factura,
                            SUM(t2.diezKTm) as "diezKTm",
                            SUM(t2.catorceKTm) as "catorceKTm",
                            SUM(t2.dieciochoKTm) as "dieciochoKTm",
                            SUM(t2.plata925m) as "plata925m",
                            SUM(t2.nueveKTm) as "nueveKTm"
                            FROM 
                            (
                                SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.subtotal end as "diezKTm",
                                CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.subtotal end as "catorceKTm",
                                CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.subtotal end as "dieciochoKTm",
                                CASE WHEN productos.tipo_producto = "S925" then factura_detalle.subtotal end as "plata925m",
                                CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.subtotal end as "nueveKTm"
                                FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
                            ) as t2
                                GROUP BY t2.id_factura
                        ) as t4
                        
                            ON t3.id_factura = t4.id_factura
                            
                            JOIN
                            (
                                SELECT 
                            t5.id_factura,
                            SUM(t5.diezKTcon) as "diezKTcon",
                            SUM(t5.catorceKTcon) as "catorceKTcon",
                            SUM(t5.dieciochoKTcon) as "dieciochoKTcon",
                            SUM(t5.plata925con) as "plata925con",
                            SUM(t5.nueveKTcon) as "nueveKTcon"
                            FROM
                            (
                                SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                CASE WHEN productos.tipo_producto = "10KT" then productos.conversion end as "diezKTcon",
                                CASE WHEN productos.tipo_producto = "14KT" then productos.conversion end as "catorceKTcon",
                                CASE WHEN productos.tipo_producto = "18KT" then productos.conversion end as "dieciochoKTcon",
                                CASE WHEN productos.tipo_producto = "S925" then productos.conversion end as "plata925con",
                                CASE WHEN productos.tipo_producto = "9KT" then productos.conversion end as "nueveKTcon"
                                FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
                            ) as t5
                                GROUP BY t5.id_factura   
                            ) as t6
                            
                            ON t3.id_factura = t6.id_factura
                            
                            JOIN
                            factura ON factura.id = t3.id_factura
                            WHERE factura.ritex = 1 AND factura.estado = 0 AND factura.fecha >= ? AND factura.fecha <= ?
                            ) as t7 GROUP BY MONTH(t7.fecha) ORDER BY t7.fecha'  , [$fechai,$fechaf]);
        $cont = 1;
        $montocalculado = []; 
            foreach($consulta2 as $c2){ 
                $montocalculado[$c2->fecha]= $c2->diez + $c2->catorce + $c2->dieciocho;
            }
            //dd($montocalculado);
            
            foreach ($consulta as $c){
        
                switch ($c->fecha1) {
                    case 1:
                        $c->fecha1 = "Enero";
                        $c->prueba = $montocalculado[1];
                        break;
                    case 2:
                        $c->fecha1 = "Febrero";
                        $c->prueba = $montocalculado[2];
                        break;
                    case 3:
                        $c->fecha1 = "Marzo";
                        $c->prueba = $montocalculado[3];
                        break;
                    case 4:
                        $c->fecha1 = "Abril";
                        $c->prueba = $montocalculado[4];
                        break;
                    case 5:
                        $c->fecha1 = "Mayo";
                        $c->prueba = $montocalculado[5];
                        break; 
                    case 6:
                        $c->fecha1 = "Junio";
                        $c->prueba = $montocalculado[6];
                        break;
                    case 7:
                        $c->fecha1 = "Julio";
                        $c->prueba = $montocalculado[7];
                        break;
                    case 8:
                        $c->fecha1 = "Agosto";
                        $c->prueba = $montocalculado[8];
                        break;
                    case 9:
                        $c->fecha1 = "Septiembre";
                        $c->prueba = $montocalculado[9];
                        break; 
                    case 10:
                        $c->fecha1 = "Octubre";
                        $c->prueba = $montocalculado[10];
                        break; 
                    case 11:
                        $c->fecha1 = "Noviembre";
                        $c->prueba = $montocalculado[11];
                        break; 
                    case 12:
                        $c->fecha1 = "Diciembre";
                        $c->prueba = $montocalculado[12];
                        break;        
                }
            }
        
            /**** CONSULTA DEFINITIVAS */
            $consultadefinitivas =  DB::select('SELECT 
            MONTH(t7.fecha) as fecha1, t7.fecha ,"" as prueba,
            SUM(t7.diezKTc) as diezc, SUM(t7.catorceKTc) as catorcec ,SUM(t7.dieciochoKTc) as dieciochoc, SUM(t7.plata925c) as platac, SUM(t7.nueveKTc) as nuevec,
            SUM(t7.diezKTm) as diezm, SUM(t7.catorceKTm) as catorcem ,SUM(t7.dieciochoKTm) as dieciochom, SUM(t7.plata925m) as platam, SUM(t7.nueveKTm) as nuevem,
            SUM(t7.diezKTc*t7.diezKTcon) as diez, SUM(t7.catorceKTc*t7.catorceKTcon) as catorce ,SUM(t7.dieciochoKTc*t7.dieciochoKTcon) as dieciocho, t7.ritex
                    FROM(
                        SELECT t3.diezKTc,t3.catorceKTc,t3.dieciochoKTc,t3.plata925c,t3.nueveKTc, t4.diezKTm, t4.catorceKTm, t4.dieciochoKTm, t4.plata925m, 										t4.nueveKTm, t6.diezKTcon,t6.catorceKTcon,t6.dieciochoKTcon,t6.plata925con,t6.nueveKTcon,
                        factura.fecha, factura.ritex FROM 
                            (
                                SELECT 
                                t1.id_factura,
                                SUM(t1.diezKTc) as "diezKTc",
                                SUM(t1.catorceKTc) as "catorceKTc",
                                SUM(t1.dieciochoKTc) as "dieciochoKTc",
                                SUM(t1.plata925c) as "plata925c",
                                SUM(t1.nueveKTc) as "nueveKTc"
                                FROM 
                                (
                                    SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                    CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.cantidad end as "diezKTc",
                                    CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.cantidad end as "catorceKTc",
                                    CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.cantidad end as "dieciochoKTc",
                                    CASE WHEN productos.tipo_producto = "S925" then factura_detalle.cantidad end as "plata925c",
                                    CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.cantidad end as "nueveKTc"
                                    FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto ) as t1 
                                    GROUP BY t1.id_factura
                            ) as t3
                            
                            JOIN
                            (
                                SELECT 
                                t2.id_factura,
                                SUM(t2.diezKTm) as "diezKTm",
                                SUM(t2.catorceKTm) as "catorceKTm",
                                SUM(t2.dieciochoKTm) as "dieciochoKTm",
                                SUM(t2.plata925m) as "plata925m",
                                SUM(t2.nueveKTm) as "nueveKTm"
                                FROM 
                                (
                                SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.subtotal end as "diezKTm",
                                CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.subtotal end as "catorceKTm",
                                CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.subtotal end as "dieciochoKTm",
                                CASE WHEN productos.tipo_producto = "S925" then factura_detalle.subtotal end as "plata925m",
                                CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.subtotal end as "nueveKTm"
                                FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
                                ) as t2
                                GROUP BY t2.id_factura
                            ) as t4
                            
                            ON t3.id_factura = t4.id_factura
                             JOIN
                             (
                                SELECT 
                                t5.id_factura,
                                SUM(t5.diezKTcon) as "diezKTcon",
                                SUM(t5.catorceKTcon) as "catorceKTcon",
                                SUM(t5.dieciochoKTcon) as "dieciochoKTcon",
                                SUM(t5.plata925con) as "plata925con",
                                SUM(t5.nueveKTcon) as "nueveKTcon"
                                FROM
                                (
                                 SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                 CASE WHEN productos.tipo_producto = "10KT" then productos.conversion end as "diezKTcon",
                                 CASE WHEN productos.tipo_producto = "14KT" then productos.conversion end as "catorceKTcon",
                                 CASE WHEN productos.tipo_producto = "18KT" then productos.conversion end as "dieciochoKTcon",
                                 CASE WHEN productos.tipo_producto = "S925" then productos.conversion end as "plata925con",
                                 CASE WHEN productos.tipo_producto = "9KT" then productos.conversion end as "nueveKTcon"
                                 FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
                                ) as t5
                                GROUP BY t5.id_factura   
                             ) as t6

                             ON t3.id_factura = t6.id_factura
                            JOIN
                            factura ON factura.id = t3.id_factura
                            WHERE factura.estado = 0 AND factura.fecha >= ? AND factura.fecha <= ? AND factura.ritex=1
                            ) as t7 GROUP BY MONTH(t7.fecha) ORDER BY t7.fecha', [$fechai,$fechaf]);

         
            foreach ($consultadefinitivas as $c){
                    
                switch ($c->fecha1) {
                    case 1:
                        $c->fecha1 = "Enero";
                        $c->prueba = $montocalculado[1];
                        break;
                    case 2:
                        $c->fecha1 = "Febrero";
                        $c->prueba = $montocalculado[2];
                        break;
                    case 3:
                        $c->fecha1 = "Marzo";
                        $c->prueba = $montocalculado[3];
                        break;
                    case 4:
                        $c->fecha1 = "Abril";
                        $c->prueba = $montocalculado[4];
                        break;
                    case 5:
                        $c->fecha1 = "Mayo";
                        $c->prueba = $montocalculado[5];
                        break; 
                    case 6:
                        $c->fecha1 = "Junio";
                        $c->prueba = $montocalculado[6];
                        break;
                    case 7:
                        $c->fecha1 = "Julio";
                        $c->prueba = $montocalculado[7];
                        break;
                    case 8:
                        $c->fecha1 = "Agosto";
                        $c->prueba = $montocalculado[8];
                        break;
                    case 9:
                        $c->fecha1 = "Septiembre";
                        $c->prueba = $montocalculado[9];
                        break; 
                    case 10:
                        $c->fecha1 = "Octubre";
                        $c->prueba = $montocalculado[10];
                        break; 
                    case 11:
                        $c->fecha1 = "Noviembre";
                        $c->prueba = $montocalculado[11];
                        break; 
                    case 12:
                        $c->fecha1 = "Diciembre";
                        $c->prueba = $montocalculado[12];
                        break;        
                }
            }

            $consultaritex =  DB::select('SELECT 
            MONTH(t7.fecha) as fecha1, t7.fecha ,"" as prueba,
            SUM(t7.diezKTc) as diezc, SUM(t7.catorceKTc) as catorcec ,SUM(t7.dieciochoKTc) as dieciochoc, SUM(t7.plata925c) as platac, SUM(t7.nueveKTc) as nuevec,
            SUM(t7.diezKTm) as diezm, SUM(t7.catorceKTm) as catorcem ,SUM(t7.dieciochoKTm) as dieciochom, SUM(t7.plata925m) as platam, SUM(t7.nueveKTm) as nuevem,
            SUM(t7.diezKTc*t7.diezKTcon) as diez, SUM(t7.catorceKTc*t7.catorceKTcon) as catorce ,SUM(t7.dieciochoKTc*t7.dieciochoKTcon) as dieciocho, t7.ritex
                    FROM(
                        SELECT t3.diezKTc,t3.catorceKTc,t3.dieciochoKTc,t3.plata925c,t3.nueveKTc, t4.diezKTm, t4.catorceKTm, t4.dieciochoKTm, t4.plata925m, 										t4.nueveKTm, t6.diezKTcon,t6.catorceKTcon,t6.dieciochoKTcon,t6.plata925con,t6.nueveKTcon,
                        factura.fecha, factura.ritex FROM 
                            (
                                SELECT 
                                t1.id_factura,
                                SUM(t1.diezKTc) as "diezKTc",
                                SUM(t1.catorceKTc) as "catorceKTc",
                                SUM(t1.dieciochoKTc) as "dieciochoKTc",
                                SUM(t1.plata925c) as "plata925c",
                                SUM(t1.nueveKTc) as "nueveKTc"
                                FROM 
                                (
                                    SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                    CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.cantidad end as "diezKTc",
                                    CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.cantidad end as "catorceKTc",
                                    CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.cantidad end as "dieciochoKTc",
                                    CASE WHEN productos.tipo_producto = "S925" then factura_detalle.cantidad end as "plata925c",
                                    CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.cantidad end as "nueveKTc"
                                    FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto ) as t1 
                                    GROUP BY t1.id_factura
                            ) as t3
                            
                            JOIN
                            (
                                SELECT 
                                t2.id_factura,
                                SUM(t2.diezKTm) as "diezKTm",
                                SUM(t2.catorceKTm) as "catorceKTm",
                                SUM(t2.dieciochoKTm) as "dieciochoKTm",
                                SUM(t2.plata925m) as "plata925m",
                                SUM(t2.nueveKTm) as "nueveKTm"
                                FROM 
                                (
                                SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.subtotal end as "diezKTm",
                                CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.subtotal end as "catorceKTm",
                                CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.subtotal end as "dieciochoKTm",
                                CASE WHEN productos.tipo_producto = "S925" then factura_detalle.subtotal end as "plata925m",
                                CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.subtotal end as "nueveKTm"
                                FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
                                ) as t2
                                GROUP BY t2.id_factura
                            ) as t4
                            
                            ON t3.id_factura = t4.id_factura
                             JOIN
                             (
                                SELECT 
                                t5.id_factura,
                                SUM(t5.diezKTcon) as "diezKTcon",
                                SUM(t5.catorceKTcon) as "catorceKTcon",
                                SUM(t5.dieciochoKTcon) as "dieciochoKTcon",
                                SUM(t5.plata925con) as "plata925con",
                                SUM(t5.nueveKTcon) as "nueveKTcon"
                                FROM
                                (
                                 SELECT productos.tipo_producto,factura_detalle.id_factura, 
                                 CASE WHEN productos.tipo_producto = "10KT" then productos.conversion end as "diezKTcon",
                                 CASE WHEN productos.tipo_producto = "14KT" then productos.conversion end as "catorceKTcon",
                                 CASE WHEN productos.tipo_producto = "18KT" then productos.conversion end as "dieciochoKTcon",
                                 CASE WHEN productos.tipo_producto = "S925" then productos.conversion end as "plata925con",
                                 CASE WHEN productos.tipo_producto = "9KT" then productos.conversion end as "nueveKTcon"
                                 FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto
                                ) as t5
                                GROUP BY t5.id_factura   
                             ) as t6

                             ON t3.id_factura = t6.id_factura
                            JOIN
                            factura ON factura.id = t3.id_factura
                            WHERE factura.estado = 0 AND factura.fecha >= ? AND factura.fecha <= ? AND factura.ritex=2
                            ) as t7 GROUP BY MONTH(t7.fecha) ORDER BY t7.fecha', [$fechai,$fechaf]);

         
            foreach ($consultaritex as $c){
                    
                switch ($c->fecha1) {
                    case 1:
                        $c->fecha1 = "Enero";
                        $c->prueba = $montocalculado[1];
                        break;
                    case 2:
                        $c->fecha1 = "Febrero";
                        $c->prueba = $montocalculado[2];
                        break;
                    case 3:
                        $c->fecha1 = "Marzo";
                        $c->prueba = $montocalculado[3];
                        break;
                    case 4:
                        $c->fecha1 = "Abril";
                        $c->prueba = $montocalculado[4];
                        break;
                    case 5:
                        $c->fecha1 = "Mayo";
                        $c->prueba = $montocalculado[5];
                        break; 
                    case 6:
                        $c->fecha1 = "Junio";
                        $c->prueba = $montocalculado[6];
                        break;
                    case 7:
                        $c->fecha1 = "Julio";
                        $c->prueba = $montocalculado[7];
                        break;
                    case 8:
                        $c->fecha1 = "Agosto";
                        $c->prueba = $montocalculado[8];
                        break;
                    case 9:
                        $c->fecha1 = "Septiembre";
                        $c->prueba = $montocalculado[9];
                        break; 
                    case 10:
                        $c->fecha1 = "Octubre";
                        $c->prueba = $montocalculado[10];
                        break; 
                    case 11:
                        $c->fecha1 = "Noviembre";
                        $c->prueba = $montocalculado[11];
                        break; 
                    case 12:
                        $c->fecha1 = "Diciembre";
                        $c->prueba = $montocalculado[12];
                        break;        
                }
            }

        /***consulta valor agregado***/
        $consulta_va =   DB::select('SELECT MONTH(fecha) as fecha1, SUM(diezKTm) as diezm, SUM(catorceKTm) as catorcem ,SUM(dieciochoKTm) as dieciochom, SUM(plata925m) as platam, SUM(nueveKTm) as nuevem 
        FROM
              (
              SELECT 
                  t2.id_factura,
                  SUM(t2.diezKTm) as "diezKTm",
                  SUM(t2.catorceKTm) as "catorceKTm",
                  SUM(t2.dieciochoKTm) as "dieciochoKTm",
                  SUM(t2.plata925m) as "plata925m",
                  SUM(t2.nueveKTm) as "nueveKTm"
                  FROM 
                    (
                      SELECT productos.tipo_producto,factura_detalle.id_factura, 
                      CASE WHEN productos.tipo_producto = "10KT" then factura_detalle.subtotal end as "diezKTm",
                      CASE WHEN productos.tipo_producto = "14KT" then factura_detalle.subtotal end as "catorceKTm",
                      CASE WHEN productos.tipo_producto = "18KT" then factura_detalle.subtotal end as "dieciochoKTm",
                      CASE WHEN productos.tipo_producto = "S925" then factura_detalle.subtotal end as "plata925m",
                      CASE WHEN productos.tipo_producto = "9KT" then factura_detalle.subtotal end as "nueveKTm"
                      FROM productos JOIN factura_detalle ON productos.id = factura_detalle.id_producto) as t2 GROUP BY t2.id_factura
               )as t3
                  JOIN
                  factura ON factura.id = t3.id_factura
                  WHERE factura.estado = 0 AND factura.fecha >= ? AND factura.fecha <= ? AND factura.ritex = 1  GROUP BY MONTH(fecha)', [$fechai,$fechaf]);

        
        foreach ($consulta_va as $c){
                    
            switch ($c->fecha1) {
                case 1:
                    $c->fecha1 = "Enero";
                    $c->prueba = $montocalculado[1];
                    break;
                case 2:
                    $c->fecha1 = "Febrero";
                    $c->prueba = $montocalculado[2];
                    break;
                case 3:
                    $c->fecha1 = "Marzo";
                    $c->prueba = $montocalculado[3];
                    break;
                case 4:
                    $c->fecha1 = "Abril";
                    $c->prueba = $montocalculado[4];
                    break;
                case 5:
                    $c->fecha1 = "Mayo";
                    $c->prueba = $montocalculado[5];
                    break; 
                case 6:
                    $c->fecha1 = "Junio";
                    $c->prueba = $montocalculado[6];
                    break;
                case 7:
                    $c->fecha1 = "Julio";
                    $c->prueba = $montocalculado[7];
                    break;
                case 8:
                    $c->fecha1 = "Agosto";
                    $c->prueba = $montocalculado[8];
                    break;
                case 9:
                    $c->fecha1 = "Septiembre";
                    $c->prueba = $montocalculado[9];
                    break; 
                case 10:
                    $c->fecha1 = "Octubre";
                    $c->prueba = $montocalculado[10];
                    break; 
                case 11:
                    $c->fecha1 = "Noviembre";
                    $c->prueba = $montocalculado[11];
                    break; 
                case 12:
                    $c->fecha1 = "Diciembre";
                    $c->prueba = $montocalculado[12];
                    break;        
            }
        }

        //dd($consultaritex);
        return (new PolizaExportacionResumen($consulta,$año,$consultadefinitivas,$consultaritex,$consulta_va))->download('PolizaExportacionResumen.xlsx');

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
