<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Datos;
use Carbon\Carbon;
use App\UnidadMedida;
use App\Productos;
use App\Grupo2;
use App\Grupo;
use App\Sucursal;

use DataTables;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use PDF;
use App\Traspaso;
use App\TraspasoDetalle;

class TraspasoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
    }
    public function traspasoajax(){
        
        $data = DB::select(DB::raw('SELECT t1.*,sucursal.descripcion as destino_descripcion FROM( SELECT traspaso.*, sucursal.descripcion as origen_descripcion FROM traspaso JOIN sucursal ON sucursal.nro_sucursal = traspaso.id_sucursal_origen )as t1 JOIN sucursal ON sucursal.nro_sucursal = t1.id_sucursal_destino'));
        
        // Traspaso::where('id_sucursal_origen','=',auth()->user()->id_sucursal)
        // ->where('punto_venta','=',auth()->user()->punto_venta)
        // ->orderBy('id', 'DESC')->get();
        
        return Datatables::of($data)
            ->addColumn('btn','traspaso.actions')
            ->addColumn('pdf','traspaso.pdf')
            ->rawColumns(['btn','pdf'])
            ->make(true);
        
    }
    public function index()
    {
        //
        return view('traspaso.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $sucu = Sucursal::all();
        $fecha=Carbon::now(-4)->format('Y-m-d');
        $grupos = Grupo2::all();

        return view('traspaso.traspaso',[ 'sucu'=>$sucu, 'fecha'=>$fecha,  'grupos'=>$grupos]);
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
