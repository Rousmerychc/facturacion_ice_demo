<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\User;
use App\Sucursal;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use \Auth;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     //
    public function __construct()
    {
        $this->middleware('auth');
    }
      /**  *********************************************   */
  
      public function index()
      {
          //$usuarios= User::all();
          return view('usuarios.usuario');
      }
      public function usuariosajax(Request $request)
     {
        //if ($request->ajax()) {
            //$data = User::all();
            $data = DB::table('users')
            ->where('users.id','!=',1)
            ->get();
            //dd($data);
            return Datatables::of($data)
                ->addColumn('btn','usuarios.actions')
                ->rawColumns(['btn'])
                ->make(true);
        //}
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
       
        return view('usuarios.register',['sucursal'=>$sucursal]);
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
        $usuario = new User;
       
        $usuario->name = $request->get('name');
        $usuario->apellido = $request->get('apellido');
        $usuario->password = Hash::make($request->get('password'));
       
        $usuario->email = $request->get('email');
        $usuario->rol = (int)$request->get('rol');
        
        $sucu = Sucursal::where('id','=',(int)$request->get('id_sucursal'))->first();
        $usuario->id_sucursal = $sucu->nro_sucursal;
        $usuario->descripcion_sucursal = $sucu->descripcion;
        $usuario->punto_venta = (int)$request->get('punto_venta'); 
        $usuario->estado =  (int)$request->get('estado');
      
        $usuario->save();
    
        return redirect('usuarios')->with('status', 'registro guardado con exito');
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
        $sucursal = Sucursal::all();
        $usuarios =User::findOrFail($id);
        return view('usuarios.edit',['usuarios'=>$usuarios,'sucursal'=>$sucursal]);
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
        
        //$datousuario =request()->except(['_token','_method','password_confirmation']);
       //User::where('id','=',$id)->update($datousuario);
       $usuario =  User::where('id','=',$id)->first();
      
        $usuario->name = $request->get('name');
        $usuario->apellido = $request->get('apellido');
        //$usuario->password = Hash::make($request->get('password'));
       
        $usuario->email = $request->get('email');
        $usuario->rol = (int)$request->get('rol');
        
        $sucu = Sucursal::where('id','=',(int)$request->get('id_sucursal'))->first();
        $usuario->id_sucursal = $sucu->nro_sucursal;
        $usuario->descripcion_sucursal = $sucu->descripcion;
        $usuario->punto_venta = (int)$request->get('punto_venta'); 
        $usuario->estado =  (int)$request->get('estado');
        $usuario->estado_descuento =  (int)$request->get('estado_descuento');
        //dd($usuario);
        $usuario->save();
        
           
        
        
        return redirect('usuarios')->with('status', 'registro guardado con exito');

        /*
        // Validar los datos
        $this->validate($request, [
            'password' => 'required|confirmed|min:6|max:32',
        ]);
        // Note la regla de validación "confirmed", que solicitará que usted agregue un campo extra llamado password_confirm

        $user = Auth::user(); // Obtenga la instancia del usuario en sesión

        $password = bcrypt($request->password); // Encripte el password


        $user->password = $password; // Rellene el usuario con el nuevo password ya encriptado
        $user->save(); // Guarde el usuario

        // Por ultimo, redirigir al usuario, por ejemplo al formulario anterior, con un mensaje de que el password fue actualizado
        return redirect()->back()->withSuccess('Password actualizado');*/
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
