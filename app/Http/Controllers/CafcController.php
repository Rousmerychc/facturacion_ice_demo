<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cafc;

use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Sucursal;

class CafcController extends Controller
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
    public function index()
    {
        //
        return view('cafc.index');
    }
    public function ajaxcafc(Request $request)
    {
        if ($request->ajax()) {
            //$data = Productos::orderBy('id', 'DESC')->get();
            $data = Cafc::all();
                return Datatables::of($data)
                ->addColumn('btn','cafc.actions')
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
        $sucursales = Sucursal::all();
        return view('cafc.create',['sucursales'=>$sucursales]);
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
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d H:i:s');
        $fecha = Carbon::now(-4)->format('Y-m-d');
        $sucu = Sucursal::where('id','=',(int)$request->get('id_sucursal'))->first();
        $comprobando =Cafc::where('fecha_vigencia','>',$fecha)
        ->where('id_sucursal' ,'=' ,$sucu->nro_sucursal)
        ->where('id_punto_venta','=',$request->{'punto_venta'})
        ->get();
        $comprobando1 =Cafc::where('fecha_vigencia','>',$fecha)
        ->where('id_sucursal' ,'=',$sucu->nro_sucursal)
        ->where('id_punto_venta','=',$request->{'punto_venta'})
        ->first();
           
        if($comprobando->isEmpty() || $comprobando1->nro_cafc_emitidas == $comprobando1->nro_final){
            $cafc = new Cafc;
       
        $cafc->codigo_cafc = $request->get('codigo_cafc');
        $cafc->nro_inicial = $request->get('nro_inicial');
        $cafc->nro_final = $request->get('nro_final');
        $cafc->fecha_vigencia = $request->get('fecha_vigencia');
        $cafc->usuario = $dato;
        $cafc->fecha = $fechahoyhora;
        $cafc->id_sucursal = $sucu->nro_sucursal;
        $cafc->id_punto_venta = $request->{'punto_venta'};
        $cafc->save();

        return redirect('cafc')->with('status', 'registro guardado con exito');

        }else{
            return redirect('cafc')->with('status', 'EXISTE UN CAFC VIGENTE');
        }
        
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
        $sucursales = Sucursal::all();
        $cafc =Cafc::findOrFail($id);
        return view('cafc.edit',['cafc' =>$cafc,'sucursales'=>$sucursales]);
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
        $dato = auth()->user()->id;
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d H:i:s');
        $cafc = Cafc::findOrFail($id);;
       
        $cafc->codigo_cafc = $request->get('codigo_cafc');
        $cafc->nro_inicial = $request->get('nro_inicial');
        $cafc->nro_final = $request->get('nro_final');
        $cafc->fecha_vigencia = $request->get('fecha_vigencia');
        $cafc->usuario = $dato;
        $cafc->fecha = $fechahoyhora;

        $sucu = Sucursal::where('id','=',(int)$request->get('id_sucursal'))->first();
        
        $cafc->id_sucursal =$sucu->nro_sucursal;
        $cafc->id_punto_venta = $request->{'punto_venta'};
        $cafc->save();

        return redirect('cafc')->with('status', 'registro guardado con exito');

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
