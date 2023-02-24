<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Productos;
use App\Datos;

use App\ParametricaDocumentoTipoIdentidad;
use Carbon\Carbon;
use App\UnidadMedida;
use App\ParametricaTipoMetodoPago;
use App\Clientes;
use App\Sucursal;
use App\Grupo;
use App\Grupo2;
use DataTables;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use PDF;
use App\Ingresos;
use App\IngresosDetalle;
use App\Proveedor;

class IngresosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
        $datos_em =  Datos::first();
    }

    //funcion si verifica si es cliente para la vista
    public function es_cliente(Request $request)    {
        $nit=$request->get('dato');
        $razon_social = Clientes::where('nro_documento','=',$nit)->first();
        return response(json_encode(array('razon_social' =>$razon_social)),200)->header('Content-type','text/plain');        
    }

    //pdf de ingresos
    public function pdf_ingresos($id){

        $datos_empresa = Datos::first();
        $ingreso = Ingresos::findOrFail($id);
        $detalle = IngresosDetalle::join('productos','productos.id','=','ingresos_detalle.id_producto')
                                    ->where('id_ingreso','=',$id)
                                    ->get();
        //dd($detalle);
        //PARA LITERAL EN MONEDA BOLIVIANOS
        $formatterb = new NumeroALetras();
        $reb= (int) ($ingreso->monto_total / 1000);
        $literalb= $formatterb->toInvoice($ingreso->monto_total, 2,'');
        if($reb==1){
            $literalb = 'UN '.$literalb;
        }

        $data =['ingreso'=>$ingreso, 'detalle'=>$detalle,'literalb'=>$literalb,'datos_empresa'=>$datos_empresa];

        $pdf = PDF::loadView('ingresos.pdf_cliente',$data); 
        $pdf->setPaper("letter", "portrait");
        return $pdf->stream('Ingreso.pdf');
    }

    //FUNCION AJAX PARA EL INDEX
    public function ajaxingresos(){
        
        $data = Ingresos::where('id_sucursal','=',auth()->user()->id_sucursal)
                        ->select('ingresos.*')
                        ->orderBy('id', 'DESC')->get();
        return Datatables::of($data)
            ->addColumn('btn','ingresos.actions')
            ->addColumn('pdf','ingresos.pdf')
            ->rawColumns(['btn','pdf'])
            ->make(true);
        
    }
    public function ingreso_anular(Request $request){
        $id = $request->get('id_factura');
        $ingresos = Ingresos::findOrFail($id);
        $ingresos->estado = 1;
        $ingresos->save();
        return redirect('ingresos')->with('status', 'REGISTRO SE ANULO CON EXITO');

    }
    
    public function index()
    {
        //
        return view('ingresos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $proveedor = Proveedor::all();
        $grupos = Grupo2::all();
       
        $fecha=Carbon::now(-4)->format('Y-m-d');
       
        $id_ing = Ingresos::where('id_sucursal','=',auth()->user()->id_sucursal)
        ->orderby('id','DESC')
        ->get();
       
       
        if($id_ing->isEmpty()){
            $id_ing->nro_por_sucursal = 1;
        }else{
            $id_ing = Ingresos::where('id_sucursal','=',auth()->user()->id_sucursal)
            ->orderby('id','DESC')
            ->first();
        }
        //dd($id_ing);
        $sucu = Sucursal::where('nro_sucursal','=',auth()->user()->id_sucursal)
        ->first();
        //dd($sucu);
         return view('ingresos.ingresos',['id_ing'=>$id_ing, 'sucu'=>$sucu, 'fecha'=>$fecha, 'grupos'=>$grupos, 'proveedor'=>$proveedor]);     
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
        
        $fecha =  $request->get('fecha');
        $id_ingreso =  $request->get('id_ingreso');
       
        //detalle
        $id =  $request->get('id');
        $unidad_por_paquete =  $request->get('unidad_por_paquete');
        $cantidad =  $request->get('cantidad');
        $cantidad_paquete =  $request->get('cantidad_paquete');
        $cantidad_unidad =  $request->get('cantidad_unidad');
        $precio_unitario =  $request->get('precio_unitario');
        $subtotal =  $request->get('subtotal');
        //total
        $total_detalle =  $request->get('total_detalle');

        $ingreso =  new Ingresos;
        $ingreso->id_sucursal = auth()->user()->id_sucursal;
        //dd($ingreso, auth()->user()->id_sucursal);
        $ingreso->nro_por_sucursal = $id_ingreso;
        $ingreso->fecha = $fecha;
        $ingreso->fecha_hora = Carbon::now(-4)->format('Y-m-d H:i:s');
        $ingreso->monto_total =$total_detalle;
        $ingreso->usuario = auth()->user()->id;
        $ingreso->save();
        $cont = 0; 
                
        while($cont < count($id)){
            $unidad_por_paquete = (double) str_replace(',', '',  $unidad_por_paquete[$cont]);
            $cantidad = (double) str_replace(',', '', $cantidad[$cont]);
            $cantidad_paquete = (double) str_replace(',', '', $cantidad_paquete[$cont]);
            $cantidad_unidad = (double) str_replace(',', '', $cantidad_unidad[$cont]);
            $precio_unitario = (double) str_replace(',', '', $precio_unitario[$cont]);
            $subtotal = (double) str_replace(',', '', $subtotal[$cont]);

            $ingreso_detalle = new IngresosDetalle;

            $ingreso_detalle->id_ingreso = $ingreso->id;
            $ingreso_detalle->id_sucursal = auth()->user()->id_sucursal;
            $ingreso_detalle->id_producto= $id[$cont];
            $ingreso_detalle->cantidad_paquete = $cantidad_paquete;
            $ingreso_detalle->cantidad_unidad = $cantidad_unidad;
            $ingreso_detalle->precio = $precio_unitario;
            $ingreso_detalle->subtotal= $subtotal;
            $ingreso_detalle->save();
            $cont ++;
        }
        return redirect('ingresos')->with('status', 'SE REGISTRO EL INGRESO CON EXITO');
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
