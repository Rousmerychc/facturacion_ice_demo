<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UnidadMedida;
use DataTables;
use Illuminate\Support\Facades\DB;

class UnidadMedidaController extends Controller
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

    public function unidadmedidaajax(Request $request)
    {
        if ($request->ajax()) {
            $data =UnidadMedida::where('estado','=',1)->get();
                return Datatables::of($data)
                ->make(true);
        }
    }


    public function index()
    {
        //
        return view('unidad_medida.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $unidad_medida = UnidadMedida::all();
      

        return view('unidad_medida.create',['unidad_medida' => $unidad_medida]);
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
        
        $id = $request->get('id');
        $estado = $request->get('estado');
        $cont = 0; 
      
        $poner_estdo_a_cero = DB::select('UPDATE parametrica_unidad_medida SET estado = 0');
        while($cont < count($estado)){
            $producto = UnidadMedida::findOrFail((int)$estado[$cont]);
            $producto->estado = 1 ;
            $cont++;
            $producto->save();
        }

        return redirect('unidad/medida/selecion')->with('status', 'REGISTRO SE GUARDO CON EXITO');
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
