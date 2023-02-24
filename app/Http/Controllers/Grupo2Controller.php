<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Grupo2;

use DataTables;
use Illuminate\Support\Facades\DB;

class Grupo2Controller extends Controller
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
        return view('grupo2.index');
    }

    public function grupo2ajax(Request $request)
    {
        if ($request->ajax()) {
            //$data = Productos::orderBy('id', 'DESC')->get();
            $data = Grupo2::all();
                return Datatables::of($data)
                ->addColumn('btn','grupo2.actions')
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
        return view('grupo2.create');
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
        $grupo = new Grupo2;
       
        $grupo->descripcion_grupo = $request->get('descripcion');
        $grupo->save();

        return redirect('grupo2')->with('status', 'registro guardado con exito');
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
        $grupo =Grupo2::findOrFail($id);
        return view('grupo2.edit',['grupo' =>$grupo]);
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
        $grupo =Grupo2::findOrFail($id);
        $grupo->descripcion_grupo = $request->get('descripcion');
        $grupo->save();

        return redirect('grupo2')->with('status', 'registro guardado con exito');
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
