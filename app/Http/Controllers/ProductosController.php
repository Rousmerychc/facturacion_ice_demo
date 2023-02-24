<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Productos;
use App\Grupos;
use App\Grupo2;

use App\UnidadMedida;
use App\ParametricaProductosServicios;

use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Sucursal;
use App\ProductoPrecio;

class ProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function productosajax(Request $request)
    {
        $dato_sucu = auth()->user()->id_sucursal;

        if ($request->ajax()) {
            $data =  DB::select(DB::raw('SELECT productos.id, productos.descripcion_producto, productos.id_grupo, productos.unidad_medida, productos.cantidad_litros_x_unidad, t1.*,grupo2.descripcion_grupo FROM productos
            JOIN (SELECT * FROM productos_precio where productos_precio.id_sucursal = ?)as t1
            ON productos.id = t1.id_producto JOIN grupo2 ON grupo2.id = productos.id_grupo'), [$dato_sucu]);
                return Datatables::of($data)
                ->addColumn('btn','productos.actions')
                ->rawColumns(['btn'])
                ->make(true);
        }
    }


    public function index()
    {
        //
        return view('productos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $grupos = Grupos::all();
        $grupo2 = Grupo2::all();
        $parametrica_producto = ParametricaProductosServicios::all();
        $unidad_medida = UnidadMedida::where('estado','=',1)->get();
        return view('productos.create',['grupos' => $grupos, 'grupo2' => $grupo2, 'parametrica_producto'=>$parametrica_producto, 'unidad_medida'=>$unidad_medida]);
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
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d');
        $producto = new Productos;
        $producto->id_grupo = $request->get('grupo2');
        $producto->id_grupo_porcentual = $request->get('grupo');
        
        $producto->descripcion_producto = $request->get('descripcion');
        $producto->id_medida = $request->get('unidad_medida');
            $unidad_medida_des = UnidadMedida::findOrFail($request->get('unidad_medida'));
        $producto->unidad_medida = $unidad_medida_des->descripcion;
        $producto->cantidad_litros_x_unidad = $request->get('cantidad_litros_x_unidad');
        // $producto->precio1 = $request->get('precio1');
        // $producto->precio2 = $request->get('precio2');
        // $producto->precio3 = $request->get('precio3');
        $producto->unidad_por_paquete = $request->get('unidad_por_paquete');
        $producto->id_parametrica_producto = $request->get('codigo_impuestos');
            $producto_impuestos = ParametricaProductosServicios::findOrFail($request->get('codigo_impuestos'));
        $producto->codigo_actividad =  $producto_impuestos->codigo_actividad;
        $producto->codigo_producto = $producto_impuestos->codigo_producto;
        $producto->estado =(int) $request->get('estado');
        $producto->usuario = $dato;
        $producto->fecha = $fechahoyhora;
        $producto->save();

        $sucuarsal = Sucursal::all();
        foreach($sucuarsal as $sucu ){
            $producto_precio = new ProductoPrecio;
            $producto_precio->id_sucursal = $sucu->nro_sucursal;
            $producto_precio->id_producto = $producto->id;
            $producto_precio->precio1 = $request->get('precio1');
            $producto_precio->precio2 = $request->get('precio2');
            $producto_precio->precio3 = $request->get('precio3');

            $pu1 =round((floatval($request->get('precio1')) / floatval($request->get('unidad_por_paquete'))),5); 
            $pu2 =round((floatval($request->get('precio2')) / floatval($request->get('unidad_por_paquete'))),5); 
            $pu3 =round((floatval($request->get('precio3')) / floatval($request->get('unidad_por_paquete'))),5);
            
            $producto_precio->precio_unitario1 = $pu1;
            $producto_precio->precio_unitario2 = $pu2;
            $producto_precio->precio_unitario3 = $pu3;
            

            $producto_precio->save();
        }

       
        return redirect('productos')->with('status', 'REGISTRO SE GUARDO CON EXITO');

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
        $dato_sucu = auth()->user()->id_sucursal;
        $grupo2 = Grupo2::all();
        $grupos = Grupos::all();
        $parametrica_producto = ParametricaProductosServicios::all();
        $unidad_medida = UnidadMedida::all();
        
        $producto1 = DB::select(DB::raw('SELECT productos.*, t1.*,grupo2.descripcion_grupo FROM productos
        JOIN (SELECT * FROM productos_precio where productos_precio.id_sucursal = ?)as t1
        ON productos.id = t1.id_producto JOIN grupo2 ON grupo2.id = productos.id_grupo
        WHERE t1.id = ?'), [$dato_sucu,$id]);
        $producto = $producto1[0];
        //dd($producto);

        return view('productos.edit',['producto'=>$producto,'grupos' => $grupos,'grupo2' => $grupo2, 'parametrica_producto'=>$parametrica_producto, 'unidad_medida'=>$unidad_medida]);
        
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
        
        $dato = auth()->user()->id;
        $dato_sucu = auth()->user()->id_sucursal;

        $fechahoyhora=Carbon::now(-4)->format('Y-m-d');

        $productop = ProductoPrecio::findOrFail($id);
        $producto = Productos::findOrFail($productop->id_producto);

        $producto->id_grupo = $request->get('grupo2');
        $producto->id_grupo_porcentual = $request->get('grupo');
        
        $producto->descripcion_producto = $request->get('descripcion');
        $producto->id_medida = $request->get('unidad_medida');
            $unidad_medida_des = UnidadMedida::findOrFail($request->get('unidad_medida'));
        $producto->unidad_medida = $unidad_medida_des->descripcion;
        $producto->cantidad_litros_x_unidad = $request->get('cantidad_litros_x_unidad');
        
        $producto->unidad_por_paquete = $request->get('unidad_por_paquete');
        $producto->id_parametrica_producto = $request->get('codigo_impuestos');
            $producto_impuestos = ParametricaProductosServicios::findOrFail($request->get('codigo_impuestos'));
        $producto->codigo_actividad =  $producto_impuestos->codigo_actividad;
        $producto->codigo_producto = $producto_impuestos->codigo_producto;
        $producto->estado =$request->get('estado');
        $producto->usuario = $dato;
        $producto->fecha = $fechahoyhora;
        $producto->save();
       // dd('holi');
       
        $producto_precio = ProductoPrecio::where('id_producto','=',$producto->id)
                            ->where('id_sucursal','=',$dato_sucu)
                            ->first();
                    
        $producto_precio->precio1 = $request->get('precio1');
        $producto_precio->precio2 = $request->get('precio2');
        $producto_precio->precio3 = $request->get('precio3');

        $pu1 =round((floatval($request->get('precio1')) / floatval($request->get('unidad_por_paquete'))),5); 
        $pu2 =round((floatval($request->get('precio2')) / floatval($request->get('unidad_por_paquete'))),5); 
        $pu3 =round((floatval($request->get('precio3')) / floatval($request->get('unidad_por_paquete'))),5);
        
        $producto_precio->precio_unitario1 = $pu1;
        $producto_precio->precio_unitario2 = $pu2;
        $producto_precio->precio_unitario3 = $pu3;
        
        $producto_precio->save();

        return redirect('productos')->with('status', 'REGISTRO SE GUARDO CON EXITO');
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
