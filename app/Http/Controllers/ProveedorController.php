<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proveedor;

use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProveedorController extends Controller
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
        return view('proveedor.index');
    }

    public function proveedorajax(Request $request)
    {
        if ($request->ajax()) {
            //$data = Productos::orderBy('id', 'DESC')->get();
            $data = Proveedor::all();
                return Datatables::of($data)
                ->addColumn('btn','proveedor.actions')
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
        return view('proveedor.create');
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
        $proveedor = new Proveedor;
       
        $proveedor->descripcion = $request->get('descripcion');
        $proveedor->usuario = $dato;
        $proveedor->fecha = $fechahoyhora;

        $proveedor->save();

        return redirect('proveedor')->with('status', 'registro guardado con exito');
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
       $proveedor =Proveedor::findOrFail($id);
        return view('proveedor.edit',['proveedor' =>$proveedor]);
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
        $proveedor =Proveedor::findOrFail($id);
       
        $proveedor->descripcion = $request->get('descripcion');
        $proveedor->usuario = $dato;
        $proveedor->fecha = $fechahoyhora;

        $proveedor->save();

        return redirect('proveedor')->with('status', 'registro guardado con exito');
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
