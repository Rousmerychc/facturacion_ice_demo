<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Clientes;

use DataTables;
use Carbon\Carbon;
use App\ParametricaPaisOrigen;
use App\ParametricaDocumentoTipoIdentidad;
use App\ParametricaTipoDocumentoSector;
use App\ParametricaTipoMetodoPago;
use App\ ParametricaTipoMoneda;
use Illuminate\Support\Facades\DB;


class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct()  {
        $this->middleware('auth');
    }
    public function VerificarNIT(Request $request)
    {
        $nit =  $request->get('dato');
        //dd($nit);
        $wsdl = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl";
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJvcmJvbHNhMSIsImNvZGlnb1Npc3RlbWEiOiI3MUNDQzlBQjhCMTREMThFQTgxRThBRSIsIm5pdCI6Ikg0c0lBQUFBQUFBQUFETTBNREExTmpjM01MSUFBTmhBRmlnS0FBQUEiLCJpZCI6MTI4MjczLCJleHAiOjE2NzQyNTkyMDAsImlhdCI6MTY0Mjc4Mjc4OSwibml0RGVsZWdhZG8iOjEwMDUzNzcwMjgsInN1YnNpc3RlbWEiOiJTRkUifQ.g8G1KZjRpf0Z4BnLqjIRPBNQ6OQMgvBHfMPEdR4r6xuR0Hzgzt_DF4nVRH79_42dcwbCOPX_xw_ey-fBDWF_5g';

        $client = new \SoapClient($wsdl, [ 
            'stream_context' => stream_context_create([ 
                'http'=> [ 
                    'header' => "apikey: TokenApi $token",  
                ] 
            ]),

            'cache_wsdl' => WSDL_CACHE_NONE,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
        ]);

        $SolicitudVerificarNit = array(
            'codigoAmbiente'        => 2,
            'codigoModalidad'       => 1,
            'codigoSistema'         => '71CCC9AB8B14D18EA81E8AE',
            'codigoSucursal'        => 0,
            'cuis'                  => 'B4450D89',
            // 'cuis'                  => 'EED58C49',
            'nit'                   => 1005377028,
            'nitParaVerificacion'   => $nit,
        );
        $verificarNit = $client->verificarNit(
            array(
                "SolicitudVerificarNit" => $SolicitudVerificarNit,
            )
        );

        //$prueba = $verificarNit->
        //dd($verificarNit);
        $p = $verificarNit->{'RespuestaVerificarNit'};
        $p1 = $p->{'mensajesList'};
        $prueba = $p1->{'descripcion'};
        //dd($prueba);
        return response(json_encode(array('prueba'=>$prueba)),200)->header('Content-type','text/plain');
    }

    public function index()
    {
        //
        return view('clientes.index');
    }
    public function clientesajax(Request $request)
    {
        if ($request->ajax()) {
           $data = Clientes::all();
            return Datatables::of($data)
                ->addColumn('btn','clientes.actions')
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

        $tipo_documento = ParametricaDocumentoTipoIdentidad::all();

        return view('clientes.create',['tipo_documento'=>$tipo_documento]);

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

        $cliente =new Clientes;
        $cliente->descripcion = $request->get('descripcion');
        $cliente->razon_social = $request->get('razon_social');
        $cliente->responsable = $request->get('responsable');
        $cliente->direccion = $request->get('direccion');
        $cliente->email = $request->get('email');
        $cliente->id_tipo_documento = $request->get('id_tipo_documento'); 
        $cliente->nro_documento = $request->get('nro_documento');
        $cliente->excepcion = $request->get('validanit');
        $cliente->complemento = $request->get('complemento');
        $cliente->razon_social = $request->get('razon_social');
        $cliente->id_categoria_precio = (int)$request->get('id_categoria_precio');
        $cliente->estado =  (int)$request->get('estado');
        
        $cliente->usuario = $dato;
        $cliente->usuario = $dato;
        $cliente->fecha = $fechahoyhora;

        $cliente->save();

        return redirect('clientes')->with('status', 'REGISTRO GUARDADO CON EXITO');
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
        $cliente = Clientes::findOrFail($id);
       
        $tipo_documento = ParametricaDocumentoTipoIdentidad::all();

        return view('clientes.edit',['tipo_documento'=>$tipo_documento, 'cliente'=>$cliente]);
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
        $fechahoyhora=Carbon::now(-4)->format('Y-m-d');

        $cliente =Clientes::findOrFail($id);
        $cliente->descripcion = $request->get('descripcion');
        $cliente->razon_social= $request->get('razon_social');
        $cliente->responsable = $request->get('responsable');
        $cliente->direccion = $request->get('direccion');
        $cliente->email = $request->get('email');
        $cliente->id_tipo_documento = $request->get('id_tipo_documento'); 
        $cliente->nro_documento = $request->get('nro_documento');
        $cliente->excepcion = $request->get('validanit');
        $cliente->complemento = $request->get('complemento');
        $cliente->id_categoria_precio = (int)$request->get('id_categoria_precio');
        $cliente->estado =  (int)$request->get('estado');
        
        $cliente->usuario = $dato;
        $cliente->usuario = $dato;
        $cliente->fecha = $fechahoyhora;

        $cliente->save();
        
        return redirect('clientes')->with('status', 'REGISTRO GUARDADO CON EXITO');
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
