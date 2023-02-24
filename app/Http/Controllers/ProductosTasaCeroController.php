<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Productos;

use App\UnidadMedida;
use App\ParametricaProductosServicios;
use App\Grupos;

use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ProductosTasaCeroController extends Controller
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

    public function productos2ajax(Request $request)
    {
        if ($request->ajax()) {
            $data = Productos::where('estado','=',1)->where('codigo_actividad','=',773901)->get();
                return Datatables::of($data)
                ->addColumn('btn','productos.actions')
                ->rawColumns(['btn'])
                ->make(true);
        }
    }


    public function index()
    {
        //
        return view('productos.index2');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $producto = Productos::where('codigo_actividad','=',773901)->get();
      

        return view('productos.create2_tasa_cero',['producto' => $producto]);
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
        $id = $request->get('id');
        $estado = $request->get('estado');
        //dd($id,$estado);
        $cont = 0; 
        //dd($factura_bd->id);
        $poner_estdo_a_cero = DB::select('UPDATE productos SET estado = 0 WHERE productos.codigo_actividad = 773901 ');
        while($cont < count($estado)){
            $producto = Productos::findOrFail((int)$estado[$cont]);
            $producto->estado = 1 ;
            $cont++;
            $producto->save();
        }

        return redirect('productos2')->with('status', 'REGISTRO SE GUARDO CON EXITO');

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
