<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Grupos;

use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GruposController extends Controller
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
        return view('grupos.index');
    }

    public function gruposajax(Request $request)
    {
        if ($request->ajax()) {
            //$data = Productos::orderBy('id', 'DESC')->get();
            $data = Grupos::all();
                return Datatables::of($data)
                ->addColumn('btn','grupos.actions')
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
        return view('grupos.create');
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
        $grupo = new Grupos;
       
        $grupo->descripcion = $request->get('descripcion');
        $grupo->ice_porcentual = $request->get('ice_porcentual');
        $grupo->ice_especifico = $request->get('ice_especifico');
        $grupo->usuario = $dato;
        $grupo->fecha = $fechahoyhora;

        $grupo->save();

        return redirect('grupos')->with('status', 'registro guardado con exito');
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
       $grupo =Grupos::findOrFail($id);
        return view('grupos.edit',['grupo' =>$grupo]);
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
        $grupo =Grupos::findOrFail($id);
       
        $grupo->descripcion = $request->get('descripcion');
        $grupo->ice_porcentual = $request->get('ice_porcentual');
        $grupo->ice_especifico = $request->get('ice_especifico');
        $grupo->usuario = $dato;
        $grupo->fecha = $fechahoyhora;

        $grupo->save();

        return redirect('grupos')->with('status', 'registro guardado con exito');
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
