<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check() ) {
        return view('hola');
    }
    return view('hola');//hola//auth.login
})->middleware('activo');


//Route::Resource('login','LoginController');
//Route::get('ingresosaldo','SaldoIngresosController@index')->name('cis');

/*cambios momentanios*/
Route::get('inicio','IndexController@index')->middleware('activo');

/** */

/** menu vista gasto */
Route::get('menugasto','IndexController@menugasto')->middleware('admin');

//Auth::routes();
Auth::routes(['register' => false]);

Route::get('index','IndexController@index')->middleware('admin');

Route::Resource('usuarios','UsuarioController')->middleware('admin');
Route::get('usuariosajax','UsuarioController@usuariosajax');



 /*CRUD Sucursal*/
 Route::Resource('sucursal','SucursalController')->middleware('admin');
 Route::get('sucursalajax','SucursalController@sucursalajax')->middleware('admin');

//  /*CRUD Punto Venta*/
//  Route::Resource('punto/venta','PuntoVentaController')->middleware('adminjr')->middleware('admin');
//  Route::get('punto_ventaajax','PuntoVentaController@punto_ventaajax')->middleware('admin');

/**CRUD Clientes */
Route::Resource('clientes','ClientesController')->middleware('operador');
Route::get('clientesajax','ClientesController@clientesajax')->middleware('operador');
Route::get('validacion_nit','ClientesController@VerificarNIT')->middleware('operador');

/**CRUD Productos */
Route::Resource('productos','ProductosController')->middleware('admin');
Route::get('productosajax','ProductosController@productosajax')->middleware('admin');
Route::Resource('productos2','ProductosTasaCeroController')->middleware('admin');
Route::get('productos2ajax','ProductosTasaCeroController@productos2ajax')->middleware('admin');

/**CRUD UNIDAD MEDIDA */
Route::Resource('unidad/medida/selecion','UnidadMedidaController')->middleware('admin');
Route::get('unidadmedidaajax','UnidadMedidaController@unidadmedidaajax')->middleware('admin');

// CRUD Grupos
Route::Resource('grupos', 'GruposController')->middleware('admin');
Route::get('gruposajax','GruposController@gruposajax')->middleware('admin');

// CRUD Grupo2
Route::Resource('grupo2', 'Grupo2Controller')->middleware('admin');
Route::get('grupo2ajax','Grupo2Controller@grupo2ajax')->middleware('admin');

//CRUD PROVEEDORES
Route::resource('proveedor','ProveedorController')->middleware('admin');
Route::get('proveedorajax','ProveedorController@proveedorajax')->middleware('admin');

//CRUD Ingresos
Route::Resource('ingresos','IngresosController')->middleware('admin');
Route::post('ingreso_anular','IngresosController@ingreso_anular')->middleware('admin');
Route::get('ajaxingresos','IngresosController@ajaxingresos')->middleware('admin');
Route::get('pdf_ingresos/{id}','IngresosController@pdf_ingresos')->middleware('admin');
//CRUD cafc
Route::Resource('cafc','CafcController')->middleware('admin');
Route::get('ajaxcafc','CafcController@ajaxcafc')->middleware('admin');
/**PREFACTURACION */
// Route::Resource('prefactura','PreFacturaController')->middleware('admin');
// Route::get('cliente_fac','PreFacturaController@cliente_fac')->middleware('admin');

// Route::get('facturaajax','PreFacturaController@facturaajax')->middleware('admin');
// Route::get('pre_pdf_factura/{id}','PreFacturaController@pre_pdf_factura')->middleware('admin');
//Route::post('prefactura','FacturacionController@prefactura')->middleware('admin');
Route::get('verificanit','FacturacionController@verificar_nit')->middleware('operador');
Route::get('es_cliente','FacturacionController@es_cliente')->middleware('operador');
Route::get('id_cliente','FacturacionController@id_cliente')->middleware('operador');

/**facturacion */
Route::Resource('facturacion','FacturacionController')->middleware('operador');
Route::post('anular_fac','FacturacionController@anular_fac')->middleware('admin');
Route::get('enviarpaquete','FacturacionController@emisionFueraLinea')->middleware('operador');
Route::get('codigoQR/{id}','FacturacionController@codigoQR')->middleware('operador');
Route::get('codigoQR_modal','FacturacionController@codigoQR_modal')->middleware('operador');
Route::get('pdf_factura/{id}','FacturacionController@pdf')->middleware('operador');
Route::get('borrar','FacturacionController@pruebaborrado')->middleware('operador');
Route::get('correo','FacturacionController@correo')->middleware('operador');
Route::get('ajaxfactura','FacturacionController@ajaxfactura')->middleware('operador');
Route::get('seleciona_prefactura','FacturacionController@seleciona_prefactura')->middleware('operador');
Route::get('pdf_clientes/{id}/{tipo}','FacturacionController@pdf_clientes')->middleware('operador');
Route::get('producto_fac','FacturacionController@producto_fac')->middleware('operador');
Route::get('cuis','FacturacionController@cuis')->middleware('operador');
Route::get('cufd','FacturacionController@cufd')->middleware('operador');
Route::get('pendiente','FacturacionController@pendiente')->middleware('operador');
Route::get('verificarComunucacion','FacturacionController@verificarComunucacion')->middleware('operador');
Route::get('producto_fac','FacturacionController@producto_fac')->middleware('operador');
Route::get('porducto_grupo','FacturacionController@porducto_grupo')->middleware('operador');

// /**facturacion */
// Route::Resource('facturacionT','FacturaTasaCeroController')->middleware('admin');
// Route::post('anular_facT','FacturaTasaCeroController@anular_fac')->middleware('admin');
// Route::get('enviarpaqueteT','FacturaTasaCeroController@emisionFueraLinea')->middleware('admin');
// Route::get('codigoQRT/{id}','FacturaTasaCeroController@codigoQR')->middleware('admin');
// Route::get('codigoQR_modalT','FacturaTasaCeroController@codigoQR_modal')->middleware('admin');
// Route::get('pdf_facturaT/{id}','FacturaTasaCeroController@pdf')->middleware('admin');
// Route::get('borrarT','FacturaTasaCeroController@pruebaborrado')->middleware('admin');
// Route::get('correoT','FacturaTasaCeroController@correo')->middleware('admin');
// Route::get('ajaxfacturaT','FacturaTasaCeroController@ajaxfactura')->middleware('admin');
// Route::get('seleciona_prefacturaT','FacturaTasaCeroController@seleciona_prefactura')->middleware('admin');
// Route::get('pdf_clientesT/{id}','FacturaTasaCeroController@pdf_clientes')->middleware('admin');
// Route::get('producto_facT','FacturaTasaCeroController@producto_fac')->middleware('admin');
// Route::get('cuisT','FacturaTasaCeroController@cuis')->middleware('admin');
// Route::get('cufdT','FacturaTasaCeroController@cufd')->middleware('admin');
// Route::get('pendienteT','FacturaTasaCeroController@pendiente')->middleware('admin');
// Route::get('verificarComunucacionT','FacturaTasaCeroController@verificarComunucacion')->middleware('admin');

// //facturacion mannual
// Route::get('create2','FacturacionController@create2')->middleware('admin');
// Route::get('emisionFueraLinea','FacturacionController@emisionFueraLinea')->middleware('admin');
// Route::post('emisionManuales','FacturacionController@emisionManuales')->middleware('admin');
// Route::post('facuramanual','FacturacionController@facturasManuales')->middleware('admin');
// Route::get('boradoarchivos','FacturacionController@borradoarchivos')->middleware('admin');

//NOTA DE VENTA
Route::Resource('nota_venta','NotaVentaController')->middleware('operador');
Route::get('nota_ventaajax','NotaVentaController@nota_ventaajax')->middleware('operador');
Route::get('pdf_clientes_nota_venta/{id}','NotaVentaController@pdf_clientes')->middleware('operador');
/**REPORTES */
Route::Resource('reportes','ReportesController')->middleware('admin');
Route::post('reporte_libro_ventas','ReportesController@libro_ventas')->middleware('admin');
Route::post('reporte_ventas_por_litros','ReportesController@ventas_litros')->middleware('admin');
Route::post('reporte_movimiento_diario_item','ReportesController@movimiento_diario_item')->middleware('admin');
Route::post('reporte_resumen_item','ReportesController@resumen_item')->middleware('admin');
Route::post('poliza/exportacion','ReportesController@poliza_exportacion')->middleware('admin');
Route::post('poliza/exportacion/resumen','ReportesController@poliza_exportacio_resumen')->middleware('admin');

//CRUD TRASPASO
Route::Resource('traspaso','TraspasoController')->middleware('operador');
Route::get('traspasoajax','TraspasoController@traspasoajax')->middleware('operador');
Route::get('pdf_clientes_traspaso/{id}','TraspasoController@pdf_clientes')->middleware('operador');


// /**FECHA DE SALIDA */
// Route::Resource('fecha/salida','FechaSalidaController')->middleware('admin');
// Route::get('fecha_salida_ajax','FechaSalidaController@fecha_salida_ajax')->middleware('admin');    
/**Sincronizacion */
Route::Resource('sincronizacion','SincronizacionController')->middleware('admin');
Route::get('actividades','SincronizacionController@actividades')->middleware('admin');
Route::get('documetosector','SincronizacionController@ListaActividadesDocumentoSector')->middleware('admin');
Route::get('leyenda','SincronizacionController@sincronizarListaLeyendasFactura')->middleware('admin');
Route::get('mensajes/servicios','SincronizacionController@ListaMensajesServicios')->middleware('admin');
Route::get('productos1/servicios','SincronizacionController@ListaProductosServicios')->middleware('admin');
Route::get('eventos/significativos','SincronizacionController@ParametricaEventosSignificativos')->middleware('admin');
Route::get('motivo/anulacion','SincronizacionController@ParametricaMotivoAnulacion')->middleware('admin');
Route::get('pais/origen','SincronizacionController@ParametricaPaisOrigen')->middleware('admin');
Route::get('ducumento/digital','SincronizacionController@ParametricaDocumentoTipoIdentidad')->middleware('admin');
Route::get('documento/sector','SincronizacionController@ParametricaTipoDocumentoSector')->middleware('admin');
Route::get('tipo/emision','SincronizacionController@ParametricaTipoEmision')->middleware('admin');
Route::get('tipo/habitacion','SincronizacionController@ParametricaTipoHabitacion')->middleware('admin');
Route::get('tipo/pago','SincronizacionController@ParametricaTipoMetodoPago')->middleware('admin');
Route::get('tipo/moneda','SincronizacionController@ParametricaTipoMoneda')->middleware('admin');
Route::get('tipo/punto/venta','SincronizacionController@ParametricaTipoPuntoVenta')->middleware('admin');
Route::get('tipo/factura','SincronizacionController@ParametricaTiposFactura')->middleware('admin');
Route::get('unidad/medida','SincronizacionController@ParametricaUnidadMedida')->middleware('admin');

//rutas internas
route::get('actualizar','SincronizacionController@actualizar')->middleware('admin');
route::get('mostrartabla','SincronizacionController@mostrartabla')->middleware('admin');

Route::get('registrar_punto_venta','FacturacionController@ResgistrarPuntoVenta')->middleware('admin');
Route::get('prueba_veri_conec','FacturacionController@prueba_veri_conec')->middleware('admin');
Route::get('copia_a_excel','SincronizacionController@copia_a_excel')->middleware('admin');