<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;


use DataTables;
use App\User;

use Illuminate\Support\Facades\DB;

use App\Transferencia;
use App\CuentasDisponibles;
use App\CuentasDisponiblesUsuario;


class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     /* VERIFICA SI ESTA AUNTENTIFICADO EL USUARIO*/  
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    /**  *********************************************   */

    public function transferenciajax(Request $request)
    {
       
            // $data = Almacenes::all();
            // return Datatables::of($data)
            //     ->addColumn('btn','almacen.actions')
            //     ->rawColumns(['btn'])
            //     ->make(true);
        
    }
    public function inincio(){
        return view('hola');
    }
    
    public function menugasto(){
        return view('vista_gastos');
    }

    public function index()
    {

        return view('hola');
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
