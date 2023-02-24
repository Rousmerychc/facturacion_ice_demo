<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Sucursal;
use DataTables;
use Carbon\Carbon;
use App\Productos;
use App\ProductoPrecio;
use Illuminate\Support\Facades\DB;
class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function index()
    {
        //
        return view('sucursal.index');
    }
    public function sucursalajax(Request $request)
    {
        if ($request->ajax()) {
            $data = Sucursal::all();
            return Datatables::of($data)
                ->addColumn('btn','sucursal.actions')
                ->rawColumns(['btn'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $sucursal = Sucursal::all();
        return view('sucursal.create',['sucursal'=>$sucursal]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

   
    public function store(Request $request)
    {   
        $dato = auth()->user()->id;
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d H:i:s');
        $sucursal = new Sucursal;
        $sucursal->descripcion = $request->get('descripcion');
        $sucursal->direccion = $request->get('direccion');
        $sucursal->telefono = $request->get('telefono');
        $sucursal->municipio = $request->get('municipio');
        $sucursal->estado =  (int)$request->get('estado');      
        $sucursalultimo = Sucursal::orderby('id','DESC')->first();
        $numero_sucursalultimo = $sucursalultimo->nro_sucursal;
        $sucursal->nro_sucursal =  $numero_sucursalultimo+1;        
        //dd((int)$request->get('estado'));
        $sucursal->usuario = $dato;
        $sucursal->fecha = $fechahoyhora;
        $sucursal->save();
        
        $productos =  DB::select(DB::raw('SELECT productos.id, t1.* FROM productos
        JOIN (SELECT * FROM productos_precio where productos_precio.id_sucursal = 0)as t1
        ON productos.id = t1.id_producto'));
        
            // dd($productos);

        foreach ($productos as $prod) {

            $producto_precio = new ProductoPrecio;
            $producto_precio->id_sucursal = $numero_sucursalultimo+1;

            $producto_precio->id_producto = $prod->id_producto;
            $producto_precio->precio1 = $prod->precio1;
            $producto_precio->precio2 = $prod->precio2;
            $producto_precio->precio3 = $prod->precio3;
                      
            $producto_precio->precio_unitario1 = $prod->precio_unitario1;
            $producto_precio->precio_unitario2 = $prod->precio_unitario2;
            $producto_precio->precio_unitario3 = $prod->precio_unitario3;
            $producto_precio->save();

            
        }
        
        return redirect('sucursal')->with('status', 'registro guardado con exito');
    
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
        $sucursales =Sucursal::findOrFail($id);
        $sucu = Sucursal::all();
        //dd($sucursales);
        return view('sucursal.edit',['sucursales'=>$sucursales , 'sucu'=>$sucu]);
       
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
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d H:i:s');
        $dato = auth()->user()->id;
        $sucursal =  Sucursal::where('id','=',$id)->first();
        $sucursal->descripcion = $request->get('descripcion');
        $sucursal->direccion = $request->get('direccion');
        $sucursal->telefono = $request->get('telefono');
        $sucursal->municipio = $request->get('municipio');
        $sucursal->estado =  (int)$request->get('estado');        
        // $sucursal->nro_sucursal =  $request->get('nro_sucursal');        
        $sucursalultimo = Sucursal::where('id','=',$id)->first();
        $numero_sucursalultimo = $sucursalultimo->nro_sucursal;
        $sucursal->nro_sucursal =  $numero_sucursalultimo;
        // dd($sucursal->nro_sucursal);
        $sucursal->usuario = $dato;
        $sucursal->fecha = $fechahoyhora;
        $sucursal->save();

        

       return redirect('sucursal');
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
        Sucursal::destroy($id);
        return redirect('sucursal');
    }
}
