@extends('layouts.index')

@section('content')

<div class ="divpagina">
    <center>
        @if (session('status'))
                <!-- Button trigger modal -->
                <button style= "display:none;" type="button" class="btn btn-primary" id ="modal_respuesta_servidor1" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                    Launch demo modal
                </button>
        @endif
    </center>
<form method="POST" action="{{ url('facturacion') }}" autocomplete="off" id ="formulario">
@csrf
<div class ="divpagina">
    <div class ="titulobotonagregar">
        <div><h5 class="titulos"><i class="fas fa-angle-double-right"></i>  FACTURACION EN LINEA</h5></div>
    </div>

    <div class = "tamano_letra_fatura">
        <div  class="divformulario">
        <div class = "titulo_form_fac">
            <h6>Datos Cliente:</h6>
        </div>
        <div class ="row ">
            <div class = "datos_fac">

            <input type="hidden" id = "estado_descuento" value = "{{$dato_estado_descuento}}">
                    <div class = "form-group row ">
                        <div class=" form-group col-sm-6 labelliquidaciones">
                            <label for="name" class="titulonrofac">{{ __('Fecha:') }}</label>
                            <input name="fecha" type="date" class="form-control input_facturacion" value="{{$fecha}}" readonly required>
                        </div>
                        <div class="form-group col-sm-5 labelliquidaciones">
                        <label for="name" class=" titulonrofac">Nro:</label>
                        <input name="nro_factura" type="number" class="form-control texto_titulo_fecha_nrofac input_facturacion" value ="{{$id_fac->id_factura +1}}"  readonly required>
                        </div>

                            <div class=" row form-group col-sm-11 labelliquidaciones">
                                <label for="name" class=" titulonrofac">Sucursal:</label>
                                <input name="sucursal" type="text" class="form-control input_facturacion"  value ="{{$sucu->descripcion}}" readonly required>
                            </div>

                    </div>

            </div>
            <div class = "cliente_fac">

                <div class = "cliente_fac_datos">
                    <div class="form-group col-md-5 labelliquidaciones">
                        <label for="name"  class="titulonrofac">{{ __('Clientes:') }}</label>
                        <select name="id_cliente1" id = "id_cliente1"  class="form-control input_facturacion " required>
                            <option value=""></option>
                            @foreach($clientes as $cli)
                            <option  value="{{$cli->id}}"> {{$cli->id}} -- {{$cli->descripcion}}</option>                            
                            @endforeach
                            
                        </select>
                        <input type="hidden" id = "tipo_precio">
                        <input type="hidden" name ="id_cliente2" id ="id_cliente2">
                    </div>

                    <div class="form-group col-md-4 labelliquidaciones">
                        <label for="name"  class="titulonrofac">{{ __('Tipo de Documento:') }}</label>
                        <select name="id_tipo_documento" id = "id_tipo_documento" type = "number" class="form-control input_facturacion " required>
                            <option value=""></option>
                            @foreach($tipo_doc as $td)
                            <option  value="{{$td->codigo_clasificador}}" {{ old( "id_tipo_documento") == $td->codigo_clasificador ? 'selected' : '' }}>{{$td->descripcion}}  </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-2 labelliquidaciones">
                            <label for="name" class="titulonrofac">Nro Documento: </label>
                            <input name="nro_documento" id="nro_documento" type="text" class="form-control input_facturacion" maxlength="15" onblur="validarnit()" value="{{ old('nro_documento') }}"   required>
                            <input type="hidden" id = "validanit" name = "validanit" value = "{{ old('validanit')}}">
                    </div>
                    <div class="form-group  col-md-1 labelliquidaciones">
                        <label for="name" class="titulonrofac">&nbsp;</label>
                        <input name="complemento" type="text" class="form-control input_facturacion" id = "complemento"readonly placeholder ="Complemento" value="{{ old('complemento') }}" required>
                    </div>

                </div>

                <div class = "cliente_fac_datos">

                    <div class="form-group col-md-5 labelliquidaciones">
                        <label for="name" class="titulonrofac">Razon Social:</label>
                        <input name="razon_social" id = "razon_social" type="text" class="form-control input_facturacion" value="{{ old('razon_social') }}"  required>
                    </div>

                    <div class="form-group col-md-5 labelliquidaciones">
                        <label for="name" class="titulonrofac">Email:</label>
                        <input id="email" id = "email" type="email" class="form-control @error('email') is-invalid @enderror input_facturacion" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="form-group col-md-2 labelliquidaciones">
                        <label for="name"  class="titulonrofac">{{ __('Tipo de Pago:') }}</label>
                        <select name="id_tipo_pago" id="id_tipo_pago"type = "number" class="form-control input_facturacion" required>
                            @foreach($tipo_pago as $tp)
                            <option value=" {{$tp->codigo_clasificador}} ">{{$tp->descripcion}}  </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-2 labelliquidaciones" id="div_tarjeta" style="display:none">
                    <label for="name" >Nro tarjeta:</label>
                    <input name="nro_tarjeta" id = "nro_tarjeta"  type="text" class="form-control input_facturacion" maxlength="4"  value = "" readonly required>
                </div>
                <div class="form-group col-md-2 labelliquidaciones" id="div_tarjeta2" style="display:none">
                    <label for="name">&nbsp;</label>
                    <input name="nro_tarjeta2" id = "nro_tarjeta2" type="text" class="form-control input_facturacion " maxlength="4" value = "" readonly required  >
                </div>
            </div>
        </div>
        </div>

        <div class="divformulario">
            <div class = "titulo_form_fac">
                <h6>Detalle Productos:</h6>
            </div>
            <div class = "row">
                <div class="form-group col-md-6 ">
                    <label for="name">{{ __('Seleccionar Grupo:') }}</label>
                    <select name="id_grupo" id="id_grupo"type = "number" class="form-control form-control-sm" required>
                        <option value=""></option>
                        @foreach($grupos as $gr)
                        <option value="{{$gr->id}}">{{$gr->id}} - {{$gr->descripcion_grupo}}  </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4 ">
                    <label for="name">{{ __('Produtos Por Grupo:') }}</label>
                    <select name="id_producto" id="id_producto"type = "number" class="select2 form-control input_facturacion" required>
                    <option value=""></option>
                    </select>
                </div>

                <div class = "boton_agregar_producto">
                    <button type="button" onclick="agregar();" class="btn btn-outline-success btn-sm" id = "boton_agregar"> + Agregar Fila</button>
                </div>



            </div>

            <div  class="table-responsive">
                <table id ="detalle" class="table1 table-bordered" >
                    <thead  class="color_table ">
                        <tr>
                            <th class = "tamano_letra_factura" style="width : 50px;">Accion</th>
                            <th class = "tamano_letra_factura" style="width : 70px;">Cantidad Total</th>
                            <th class = "tamano_letra_factura" style="width : 200px;">Descripcion</th>
                            <th class = "tamano_letra_factura" style="width : 50px;">Cantidad Paquete</th>
                            <th class = "tamano_letra_factura" style="width : 50px;">Cantidad Unidad</th>
                            <th class = "tamano_letra_factura" style="width : 60px;">Precio Unitario</th>
                            <th class = "tamano_letra_factura" style="width : 80px;">Descuento</th>
                            <th class = "tamano_letra_factura" style="width : 50px;">ICE %</th>
                            <th class = "tamano_letra_factura" style="width : 50px;">ICE ESP</th>
                            <th class = "tamano_letra_factura" style="width : 120px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>



                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan ="6"></td>
                            <td colspan ="3"> <label for="" class = "input_facturacion1">SUBTOTAL Bs </label></td>
                            <td class = "padding_tabla"><input type="double" style="width : 100%" class="sinborde alinacionderecha1  input_facturacion1" id ="total_detalle1" name="total_detalle1" value ="{{old('total_detalle1')}}" readonly ></td>
                        </tr>
                        <tr>
                            <td colspan ="6"></td>
                            <td colspan ="3"> <label for="" class = "input_facturacion1">(-) DESCUENTO Bs </label></td>
                            <td class = "padding_tabla"><input type="double" style="width : 100%" class="sinborde alinacionderecha1  input_facturacion1"  value ="0.00" readonly ></td>
                        </tr>
                        <tr>
                            <td colspan ="6"></td>
                            <td colspan ="3"> <label for="" class = "input_facturacion1">TOTALBs </label></td>
                            <td class = "padding_tabla"><input type="double" style="width : 100%" class="sinborde alinacionderecha1  input_facturacion1" id ="total_detalle" name="total_detalle" value ="{{old('total_detalle')}}" readonly ></td>
                        </tr>
                        <tr>
                            <td colspan ="6"></td>
                            <td colspan ="3"> <label for="" class = "input_facturacion1"> TOTAL ICE ESPECÍFICO Bs </label></td>
                            <td class = "padding_tabla"><input type="double" style="width : 100%;" class="sinborde alinacionderecha1  input_facturacion1" name="ice_especifico_total" id="ice_especifico_total" value ="{{old('ice_especifico_total')}}" readonly ></td>
                        </tr>

                        <tr>
                            <td colspan ="6"></td>
                            <td colspan ="3"> <label for="" class = "input_facturacion1">TOTAL ICE PORCENTUAL Bs </label></td>
                            <td class = "padding_tabla"><input type="double" style="width : 100%" class="sinborde alinacionderecha1  input_facturacion1" name="ice_porcentual_total" id="ice_porcentual_total" value ="{{old('ice_porcentual_total')}}" readonly ></td>
                        </tr>


                        <tr>
                            <td colspan ="6"></td>
                            <td colspan ="3"> <label for="" class = "input_facturacion1">TOTAL BASE CREDITO FISCAL Bs </label></td>
                            <td class = "padding_tabla"><input type="double" style="width : 100%" class="sinborde alinacionderecha1  input_facturacion1" name="subtotal_para_iva" id="subtotal_para_iva" value ="{{old('subtotal_para_iva')}}" readonly ></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

     <div class="botones_atras_guardar">
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('FacturacionController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="button" class="btn btn-primary" onclick="validar();"> Guardar </button>
        </div>
        <div style = "display:none">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop" id ="modal_seguro_enviar"> Guardar modal </button>
        </div>
    </div>
    </div>
</form>
</div>
<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-body">
        Seguro de Guardar
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal" id = "boton_cancelar">CANCELAR</button>
        <button type="button" class="btn btn-primary"  data-bs-dismiss="modal" onclick="enviar();" id = "boton_enviar">ACEPTAR</button>
      </div>
    </div>
  </div>
</div>


<!-- Button trigger modal -->
<div style = "display:none">
<button type="button" class="btn btn-primary" id ="modal_respuesta_servidor" data-toggle="modal" data-target="#staticBackdrop1">
  Launch static backdrop modal
</button>
</div>


<!-- Modal -->
<div class="modal fade" id="staticBackdrop1" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Mensaje del Sistema</h5>
      </div>
      <div class="modal-body">
      <label for="" id = "res_nit"></label>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick ="no_continua()"><label for=""  id = "cerrar" ></label></button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick ="si_continua()" id = "boton_si">Si</button>

      </div>
    </div>
  </div>
</div>


<!-- Modal MENSAJE SISTEMA-->
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Mensaje del Sistema</h5>

      </div>
      <div class="modal-body">
        {{ session('status') }}
      </div>
      <div class="modal-footer">
        <button type="button " class="btn btn-secondary btn-sm" data-bs-dismiss="modal">cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection


@section('js')
<script>

    var cont = 0;
    var total_detalle = 0;

function  si_continua() {
    $("#validanit").val(1);
}
function no_continua(){
    $("#validanit").val(0);
}
function validar() {
    sw = 0;
    subtotal = document.getElementsByName("subtotal[]");
    descripcion = document.getElementsByName("descripcion[]");
   // console.log(descripcion);
    tipo_doc = document.getElementById("id_tipo_documento").value;
    nro_documento = document.getElementById("nro_documento").value;
    complemento = document.getElementById("complemento").value;
    razon_social = document.getElementById("razon_social").value;
    id_tipo_pago = document.getElementById("id_tipo_pago").value;
    id_cliente1 = document.getElementById("id_cliente1").value;
    nro_tarjeta = document.getElementById("nro_tarjeta").value;
    nro_tarjeta2 = document.getElementById("nro_tarjeta2").value;

    email = document.getElementById("email").value;
    //console.log(email+"  email")

    if(id_cliente1 === ""){
        alert("NO SELECCIONO CLIENTE");
         sw = 1;
         document.getElementById("id_cliente1").focus();
        return
    }

    if(tipo_doc === ""){
        alert("NO SELECCIONO TIPO DE DOCUMENTO");
         sw = 1;
         document.getElementById("id_tipo_documento").focus();
        return
    }

    if(nro_documento === ""){
        alert("NO INTRODUJO NRO DE DOCUMENTO");
         sw = 1;
         document.getElementById("nro_documento").focus();
        return
    }

    if(razon_social === ""){
        alert("NO INTRODUJO RAZON SOCIAL");
         sw = 1;
         document.getElementById("razon_social").focus();
        return
    }

    if(email != ""){
        emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
    //Se muestra un texto a modo de ejemplo, luego va a ser un icono
        if (emailRegex.test(email)) {
        //console.log("");
        } else {
            sw = 1;
        alert( "FORMATO DE EMAIL INCORRECTO");
        document.getElementById("email").focus();
        }
    }

    if(id_tipo_pago === ""){
        alert("NO SELECIONO TIPO DE PAGO");
         sw = 1;
         document.getElementById("id_tipo_pago").focus();
        return
    }

    if(id_tipo_pago == 2){
       
       if(nro_tarjeta.length != 4){
           alert("NO INTRODIJO NRO DE TARJETA O CANTIDAD DE CARACTERES EQUIVOCADOS");
           sw = 1;
           document.getElementById("nro_tarjeta").focus();
           return
       }

       if(nro_tarjeta2.length != 4){
           alert("NO INTRODIJO NRO DE TARJETA O CANTIDAD DE CARACTERES EQUIVOCADOS");
           sw = 1;
           document.getElementById("nro_tarjeta2").focus();
           return
       }   
   }

    if(subtotal.length>0){

        for(var j=0; j<subtotal.length;j++){
            cadena =String(subtotal[j].value)
            if( cadena === "NaN.NaN" || cadena === "0.00000" || cadena === "0"){
                alert('VALORES DE PRECIO O CANTIDAD INVALIDO')
                sw=1;
                document.getElementById("cantidad0").focus();
                return
            }
        }
        for(var i=0; i<descripcion.length;i++){
            cadena1 =String(descripcion[i].value)
            //console.log(cadena1+ "textarea");
            if( cadena1.length==0){
                alert('NO INTRODUJO DESCRIPCION DE FACTURA')
                sw=1;
                document.getElementById("descripcion0").focus();
                return
            }
        }
    }else{
        sw = 1;
        alert('NO AGREGO FILAS EN DETALLE')
        document.getElementById("boton_agregar").focus();
        return
    }

    if(sw == 0){
        document.getElementById("modal_seguro_enviar").click();
    }
}

function enviar(){
    boton_enviar = document.getElementById("boton_enviar");
    boton_enviar.disabled = true;

    boton_cancelar = document.getElementById("boton_cancelar");
    boton_cancelar.disabled = true;

    form1= document.getElementById("formulario");
    form1.submit();


}

function eliminarfila(){
    $(document).on('click', '.borrar', function (event) {
        event.preventDefault();
        $(this).closest('tr').remove();
    });

    setTimeout(function(){
       calcula();
    }, 1000);

}

function agregar(){

        var id_producto = document.getElementById("id_producto").value;
        var tipo_precio = document.getElementById("tipo_precio").value;
        var estado_descuento = document.getElementById("estado_descuento").value;

        if(id_producto === ""){
        alert("NO SELECCIONO PRODUCTO");
            document.getElementById("id_producto").focus();
        return
        }
        if(tipo_precio === ""){
        alert("NO SELECCIONO ClIENTE");
            document.getElementById("id_cliente1").focus();
        return
        }
        var parametros={
        "dato": id_producto,
            };
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method:'GET',
                url:"{{ url('producto_fac') }}",
                data:parametros

            }).done(function(res){

                var arreglo = JSON.parse(res);
                   //console.log(arreglo);
                //console.log(arreglo.prueba.ice_porcentual);

                var linea0 = '<tr id ="fila'+cont+'">'
                var linea1 ='<td style="display:none;"><input type="text"  name="id[]" readonly value="'+arreglo.prueba.id+'" ></td>';
                var linea2 ='<td style="display:none;"><input type="text"  name="codigo_impuestos[]" readonly value="'+arreglo.prueba.codigo_producto+'" ></td>';

                var linea3 ='<td style="display:none;"><input type="text"  name="codigo_actividad[]" readonly value="'+arreglo.prueba.codigo_actividad+'" ></td>';
                var linea4 ='<td style="display:none;"><input type="text"  name="id_medida[]" readonly value="'+arreglo.prueba.id_medida+'" ></td>';
                var linea5 ='<td style="display:none;"><input type="text"  name="unidad_medida[]" readonly value="'+arreglo.prueba.unidad_medida+'" ></td>';
                var linea6 ='<td style="display:none;"><input type="text"  name="ice_porcentual[]" id = "ice_porcentual'+cont+'"readonly value="'+arreglo.prueba.ice_porcentual+'" ></td>';
                var linea7 ='<td style="display:none;"><input type="text"  name="ice_especifico[]" id = "ice_especifico'+cont+'" readonly value="'+arreglo.prueba.ice_especifico+'" ></td>';
                var linea8 ='<td style="display:none;"><input type="text"  name="cantidad_litros_x_unidad[]" id ="cantidad_litros_x_unidad'+cont+'" readonly value="'+arreglo.prueba.cantidad_litros_x_unidad+'" ></td>';
                var linea9 ='<td style="display:none;"><input type="text"  name="unidad_por_paquete[]" id ="unidad_por_paquete'+cont+'" readonly value="'+arreglo.prueba.unidad_por_paquete+'" ></td>';

                var linea00 = '<td style="display:none;"><input "type="double"  id = "subtot_linea_v'+cont+'"  name="subtot_linea_v[]"   value="0" ></td>';
                var linea01 = '<td style="display:none;"><input "type="double"  id = "alicuota_linea_v'+cont+'" name="alicuota_linea_v[]"   value="0" ></td>';
                var linea02 = '<td style="display:none;"><input "type="double"  id = "neto_ice_linea_v'+cont+'" name="neto_ice_linea_v[]"  value="0" ></td>';
                var linea03 =' <td style="display:none;"><input "type="double"  id = "cantidad_ice_litros_v'+cont+'" name ="cantidad_ice_litros_v[]" value = 0> </td>';

                var linea04 =' <td style="display:none;"><input "type="double"  id = "ice_porcentual_calculado_cinco_v'+cont+'" name ="ice_porcentual_calculado_cinco_v[]" value = 0> </td>';
                var linea05 =' <td style="display:none;"><input "type="double"  id = "ice_especifico_calculado_cinco_v'+cont+'" name ="ice_especifico_calculado_cinco_v[]" value = 0> </td>';
                var linea06 =' <td style="display:none;"><input "type="double"  id = "subtotal_cinco_v'+cont+'" name ="subtotal_cinco_v[]" value = 0> </td>';

                var linea10 = '<td class = "padding_tabla" style="width : 60px;"><center><button type="button" class="borrar btn btn-outline-danger btn-sm texto_tabla" onClick="eliminarfila()" ><i class="fas fa-times"></i></button></center></td>'
                var linea11 ='<td class = "padding_tabla"><input type="double" class ="sinborde alinacionderecha1"  style="width :100%" name="cantidad[]" id ="cantidad'+cont+'" onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" readonly></td>';
                var linea12 ='<td class = "padding_tabla"><input type="text" class="sinborde input_facturacion1" style="width : 100%;" name="descripcion_producto[]" value="'+arreglo.prueba.descripcion_producto+'"  readonly></td>';

                var linea13 = '<td class = "padding_tabla"><input style="width : 100%; "type="double" class = "alinacionderecha1 input_facturacion1 " id = "cantidad_paquete'+cont+'" name="cantidad_paquete[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" ></td>';
                var linea14 = '<td class = "padding_tabla"><input style="width :100%; "type="double" class = "alinacionderecha1 input_facturacion1 " id = "cantidad_unidad'+cont+'" name="cantidad_unidad[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"></td>';


                if(tipo_precio == 1){
                    var linea15 = '<td class = "padding_tabla"><input style="width : 100%; "type="double" class = "alinacionderecha1 sinborde input_facturacion1 " id = "precio_unitario'+cont+'" name="precio_unitario[]"  value = "'+arreglo.prueba.precio_unitario1+'" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" readonly></td>';
                }
                if(tipo_precio == 2){
                    var linea15 = '<td class = "padding_tabla"><input style="width : 100%; "type="double" class = "alinacionderecha1 sinborde input_facturacion1 " id = "precio_unitario'+cont+'" name="precio_unitario[]"  value = "'+arreglo.prueba.precio_unitario2+'" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" readonly></td>';
                }
                if(tipo_precio == 3){
                    var linea15 = '<td class = "padding_tabla"><input style="width : 100%; "type="double" class = "alinacionderecha1 sinborde input_facturacion1 " id = "precio_unitario'+cont+'" name="precio_unitario[]"  value = "'+arreglo.prueba.precio_unitario3+'" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" readonly></td>';
                }

                if(estado_descuento == 1){
                    var linea16 = '<td class = "padding_tabla"><input style="width : 100%; "type="double" class = "alinacionderecha1 input_facturacion1 " id = "descuento'+cont+'" name="descuento[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" ></td>';
                }else{
                    var linea16 = '<td class = "padding_tabla"><input style="width : 100%; "type="double" class = "alinacionderecha1 input_facturacion1 sinborde" id = "descuento'+cont+'" name="descuento[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" readOnly></td>';
                }

                
                var linea17 = '<td class = "padding_tabla"><input style="width :  100%; "type="double" class = "sinborde alinacionderecha1 input_facturacion1 " id = "ice_porcentual_calculado'+cont+'" name="ice_porcentual_calculado[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" readonly></td>';
                var linea18 = '<td class = "padding_tabla"><input style="width :  100%; "type="double" class = "sinborde alinacionderecha1 input_facturacion1 " id = "ice_especifico_calculado'+cont+'"  name="ice_especifico_calculado[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" readonly></td>';

                var linea19 = '<td class = "padding_tabla"><input style="width : 100%; type="double" class=" alinacionderecha1  input_facturacion1 sinborde subtotal" id = "subtotal'+cont+'" name="subtotal[]" onblur="calcula(this.form)" value="'+0+'" readonly></td></tr>';

                var linea = linea0+linea1+linea2+linea3+linea4+linea5+linea6+linea7+linea8+linea9+linea00+linea01+linea02+linea03+linea04+linea05+linea06+linea10+linea11+linea12+linea13+linea14+linea15+linea16+linea17+linea18+linea19
                $('#detalle > tbody:first').append(linea);

                cont++;
                });




}


function decimales(cadena){
    posicion = cadena.indexOf('.');
    decimal = cadena.slice(posicion+1);
    entero= cadena.substring(0,posicion);
    entero1 =  parseFloat(entero).toLocaleString('en')
    final= entero1+'.'+decimal;

    return final;
}
function redondea(valor){

	r=(parseInt(valor*100 +0.501))/100

	return r;
}

function redondea5(valor){

	r=(parseInt(valor*100000 +0.501))/100000

	return r;
}


function calcula(){

    //variables para calculos-------
    var ice_porcentual = document.getElementsByName("ice_porcentual[]");
    var ice_especifico = document.getElementsByName("ice_especifico[]");
    var cantidad_litros_x_unidad = document.getElementsByName("cantidad_litros_x_unidad[]");
    //-------------
    //cantidades--------
    var unidad_por_paquete = document.getElementsByName("unidad_por_paquete[]");
    var cantidad = document.getElementsByName("cantidad[]");
    var cantidad_paquete = document.getElementsByName("cantidad_paquete[]");
    var cantidad_unidad = document.getElementsByName("cantidad_unidad[]");
    //---------------

    var precio_unitario = document.getElementsByName("precio_unitario[]");
    var descuento = document.getElementsByName("descuento[]");
    var cantidad_ice_litros_c = document.getElementsByName("cantidad_ice_litros_v[]");
    var subtotal_a = document.getElementsByName("subtotal[]");


    var ice_especifico_calculado = document.getElementsByName("ice_especifico_calculado[]");
    var ice_porcentual_calculado = document.getElementsByName("ice_porcentual_calculado[]");


    var ice_especifico_calculado_cinco_v = document.getElementsByName("ice_especifico_calculado_cinco_v[]");
    var ice_porcentual_calculado_cinco_v = document.getElementsByName("ice_porcentual_calculado_cinco_v[]");
    var subtotal_cinco_v = document.getElementsByName("subtotal_cinco_v[]");


    var subtot_linea_v = document.getElementsByName("subtot_linea_v[]");
    var alicuota_linea_v = document.getElementsByName("alicuota_linea_v[]");
    var neto_ice_linea_v= document.getElementsByName("neto_ice_linea_v[]");

    var sumtot = 0;
    var subtot_linea = 0;
    var ice_porcentual_sum = 0;
    var ice_especifico_sum = 0;
    var subtotal_para_iva = 0;

    for(var j=0; j<cantidad.length;j++){

        //calculos para cantidad total
        //--------------------------------------------------------------------
        unidad_por_paquete0 =  unidad_por_paquete[j].value.replace(/,/g, '');
        unidad_por_paquete1 = redondea5( unidad_por_paquete0);
        unidad_por_paquete3= decimales( unidad_por_paquete1.toFixed(5));
        $('#'+ unidad_por_paquete[j].id).val( unidad_por_paquete3);

        cantidad_paquete0 = cantidad_paquete[j].value.replace(/,/g, '');
        cantidad_paquete1 = redondea5(cantidad_paquete0);
        cantidad_paquete3= decimales(cantidad_paquete1.toFixed(5));
        $('#'+cantidad_paquete[j].id).val(cantidad_paquete3);

        cantidad_unidad0 = cantidad_unidad[j].value.replace(/,/g, '');
        cantidad_unidad1 = redondea5(cantidad_unidad0);
        cantidad_unidad3= decimales(cantidad_unidad1.toFixed(5));
        $('#'+cantidad_unidad[j].id).val(cantidad_unidad3);

        cantidad_total = (unidad_por_paquete1*cantidad_paquete1)+cantidad_unidad1; //con este dato las operaciones
        cantidad_total1 = redondea5(cantidad_total);
        cantidad_total3= decimales(cantidad_total1.toFixed(5));
        $('#'+ cantidad[j].id).val(cantidad_total3);
        //-----------------------------------------------------------------------

        //calculo ICE ESPECIFICO
        //-----------------------------------------------------------------------

        cantidad_litros_x_unidad0 = cantidad_litros_x_unidad[j].value.replace(/,/g, '');
        cantidad_litros_x_unidad1 = redondea5(cantidad_litros_x_unidad0);

        ice_especifico0 = ice_especifico[j].value.replace(/,/g, '');
        ice_especifico1 = redondea5(ice_especifico0);

        ice_especifico_calculado_c =  ice_especifico1 * cantidad_litros_x_unidad1 *  cantidad_total;

        //para vista con 2 decimales
        ice_especifico_calculado1 = redondea5(ice_especifico_calculado_c);
        ice_especifico_calculado3= decimales(ice_especifico_calculado1.toFixed(5));
        $('#'+ ice_especifico_calculado[j].id).val(ice_especifico_calculado3);

        //------ para mandar con 5 decimales -------//
        ice_especifico_calculado5 = redondea5(ice_especifico_calculado_c);
        ice_especifico_calculado55= decimales(ice_especifico_calculado5.toFixed(5));
        $('#'+ ice_especifico_calculado_cinco_v[j].id).val(ice_especifico_calculado55);

        console.log(ice_especifico1,cantidad_litros_x_unidad1 ,cantidad_total ,ice_especifico_calculado_c,ice_especifico_calculado3);
        //----------------------------------------------------------------------------------

        //-----------------------------------------------------------------------------------
        //cantidad ice po litros
        //------------------------------------------------------------------------------------

        cantidad_ice_litros0 = cantidad_litros_x_unidad1 *  cantidad_total;
        cantidad_ice_litros1 = redondea5(cantidad_ice_litros0);
        cantidad_ice_litros3= decimales(cantidad_ice_litros1.toFixed(5));
        $('#'+cantidad_ice_litros_c[j].id).val(cantidad_ice_litros3);
        //------------------------------------------------------------------------------------
        //CALCULANDO EL ICE %

        //calculando subtotal - descuento
        //----------------------------------------------------------------------
        precio_unitario0 = precio_unitario[j].value.replace(/,/g, '');
        precio_unitario1 = redondea5(precio_unitario0);
        precio_unitario3= decimales( precio_unitario1.toFixed(5));
        $('#'+precio_unitario[j].id).val( precio_unitario3);

        descuento0 = descuento[j].value.replace(/,/g, '');
        descuento1 = redondea5(descuento0);
        descuento3= decimales( descuento1.toFixed(5));
        $('#'+descuento[j].id).val( descuento3);

        subtot_linea = (precio_unitario1 * cantidad_total) -  descuento1; //con este dato se hace los calculos
        subtot_linea1 = redondea5(subtot_linea);
        subtot_linea3= decimales(subtot_linea1.toFixed(5));

        if(subtot_linea1 < descuento1){
            alert('DESCUENTO MAYOR A SUBTOTAL CALCULADO')
            $('#'+descuento[j].id).val( 0);
            subtot_linea = (precio_unitario1 * cantidad_total); //con este dato se hace los calculos
            subtot_linea1 = redondea5(subtot_linea);
            subtot_linea3= decimales(subtot_linea1.toFixed(5));
            return;

        }
        $('#'+subtot_linea_v[j].id).val( subtot_linea3);

        //calculando alicuota iva
        alicuota_linea = subtot_linea1 * 0.13; //tratar de ponerlo como variable el iva
        //console.log(alicuota_linea);
        alicuota_linea1 = redondea5(alicuota_linea);
        alicuota_linea3= decimales(alicuota_linea1.toFixed(5));
        $('#'+alicuota_linea_v[j].id).val(alicuota_linea3);

        //calculando neto Ice
        neto_ice_linea = subtot_linea1 - alicuota_linea1

        neto_ice_linea1 = redondea5(neto_ice_linea);
        neto_ice_linea3= decimales(neto_ice_linea1.toFixed(5));
        $('#'+neto_ice_linea_v[j].id).val(neto_ice_linea3);

        //ice %
        ice_porcentual0 = ice_porcentual[j].value.replace(/,/g, '');
        ice_porcentual1 = parseFloat(ice_porcentual0);

        ice_linea = neto_ice_linea1  * ice_porcentual1;

        //para la vista
        ice_linea1 = redondea5(ice_linea);
        ice_linea3= decimales(ice_linea1.toFixed(5));
        $('#'+ice_porcentual_calculado[j].id).val(ice_linea3);

        //para enviar
        ice_linea5 = redondea5(ice_linea);
        ice_linea55= decimales(ice_linea5.toFixed(5));
        $('#'+ice_porcentual_calculado_cinco_v[j].id).val(ice_linea55);

        //calculando subtotal total

        //subtot = subtot_linea1 + ice_especifico_calculado1 + ice_linea1;
        subtot = subtot_linea1 + ice_especifico_calculado5 + ice_linea5;
       // vista
        subtot2 = redondea5(subtot);
        subtotal= decimales(subtot2.toFixed(5));
        $('#'+subtotal_a[j].id).val(subtotal);
        //para enviar
        subtot5 = redondea5(subtot);
        subtotal55= decimales(subtot5.toFixed(5));
        $('#'+subtotal_cinco_v[j].id).val(subtotal55);

        //
         sumtot =  sumtot + parseFloat(subtot5);
        //  console.log(sumtot+"--------------**********");
         //para la vista
        //  console.log(sumtot);
        sumtot00 = redondea(sumtot);
        sumtot1 = decimales(sumtot00.toFixed(2));

        ice_porcentual_sum = ice_porcentual_sum + ice_linea5
        ice_porcentual_sum2 = redondea(ice_porcentual_sum);
        ice_porcentual_sum3= decimales(ice_porcentual_sum2.toFixed(2));

        ice_especifico_sum = ice_especifico_sum +ice_especifico_calculado1;
        ice_especifico_sum2 = redondea(ice_especifico_sum);
        ice_especifico_sum3= decimales(ice_especifico_sum2.toFixed(2));
        // console.log(ice_porcentual_sum2, ice_especifico_sum2)
        //console.log(precio_unitario1,descuento1,subtot_linea1,alicuota_linea1,neto_ice_linea1,ice_porcentual1);

    }
    // console.log(sumtot00+"***********");
    subtotal_para_iva = sumtot00 - ice_porcentual_sum2 - ice_especifico_sum2;
    subtotal_para_iva2 = redondea(subtotal_para_iva);
    subtotal_para_iva3= decimales(subtotal_para_iva2.toFixed(2));
    $("#total_detalle").val(sumtot1);
    $("#total_detalle1").val(sumtot1);
    $("#ice_porcentual_total").val(ice_porcentual_sum3);
    $("#ice_especifico_total").val(ice_especifico_sum3);
    $("#subtotal_para_iva").val(subtotal_para_iva3);

}

function validarnit(){
    var cod = document.getElementById("id_tipo_documento").value;
    var validanit = document.getElementById("validanit").value
    var nro_documento = document.getElementById("nro_documento").value
    $("#res_nit").empty();
    $("#cerrar").empty();
    $("#razon_social").val("");
    $("#email").val("");
    var parametros={
       "dato": nro_documento,
        };
    if(cod == 5){ 

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method:'GET',
            url:"{{ url('verificanit') }}",
            data:parametros
        
        }).done(function(res){
            
            var arreglo = JSON.parse(res);
                console.log(arreglo)

                if(arreglo.conexion == 1){
                    if(arreglo.prueba == 994){
                    $("#res_nit").append('NIT INEXISTENTE, Desea Continuar');
                    $("#cerrar").append("No");
                    $('#boton_si').show();
                   
                    }else{
                        $("#res_nit").append('NIT CORRECTO');
                        $('#boton_si').hide();
                        $("#cerrar").append("Cerrar");
                        $('#boton_si').show();   
                    }
                    $('#modal_respuesta_servidor').trigger('click');
                    if(arreglo.razon_social != null)
                    {
                        console.log("entro al if de razon social");
                        $("#razon_social").val(arreglo.razon_social.razon_social);
                        $("#email").val(arreglo.razon_social.email);
                    }    
                }else{
                    escliente();
                }
            });
            }
        else{
        escliente();
        }
}

function escliente(){
    var nro_documento = document.getElementById("nro_documento").value;
    var parametros={
       "dato": nro_documento,
        };
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method:'GET',
            url:"{{ url('es_cliente') }}",
            data:parametros
        
        }).done(function(res){
            
            var arreglo = JSON.parse(res);
                console.log(arreglo)
                
                if(arreglo.razon_social != null)
                {
                    console.log("entro al if de razon social");
                    $("#razon_social").val(arreglo.razon_social.razon_social);
                    $("#email").val(arreglo.razon_social.email);
                }
                        
            });
}

$(document).ready(function() {

    $('#modal_respuesta_servidor1').trigger('click');

    $("#id_tipo_documento").change(function(){
        var cod = document.getElementById("id_tipo_documento").value;
        //console.log(cod + "codigo del select");
        if(cod == 1){
            $("#complemento").attr("readonly", false);
            //console.log("entro al if");
        } else {
            $("#complemento").attr("readonly", true);
        }
        if(cod == 5){
            nro_doc = document.getElementById("nro_documento").value;
            if( nro_doc != ""){
                validarnit();
            }

        }
    });

    $("#id_cliente1").change(function(){
        var id_cliente = document.getElementById("id_cliente1").value;

        var parametros={
        "dato": id_cliente,
            };
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method:'GET',
                url:"{{ url('id_cliente') }}",
                data:parametros

            }).done(function(res){

                var arreglo = JSON.parse(res);
                    //console.log(arreglo)

                    select = document.querySelector('#id_tipo_documento');
                    const id_tipo = arreglo.cliente11.id_tipo_documento
                    select.value = id_tipo
                        $("#tipo_precio").val(arreglo.cliente11.id_categoria_precio);

                        $("#nro_documento").val(arreglo.cliente11.nro_documento);
                        $("#validanit").val(arreglo.cliente11.excepcion);
                        $("#complemento").val(arreglo.cliente11.complemento);

                        $("#razon_social").val(arreglo.cliente11.razon_social);
                        $("#email").val(arreglo.cliente11.email);

                        $("#id_cliente2").val(id_cliente);

                        $('#id_cliente1').prop("disabled", true);

                        validarnit();

                });
        });

        $("#id_grupo").change(function(){
        var id_grupo = document.getElementById("id_grupo").value;

        var parametros={
        "dato": id_grupo,
            };
            $('#id_producto').empty();
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method:'GET',
                url:"{{ url('porducto_grupo') }}",
                data:parametros

            }).done(function(res){

                var arreglo = JSON.parse(res);
                   //console.log(arreglo)

                    for (var x = 0; x < arreglo.prueba.length; x++){
                        $('#id_producto').append($("<option/>", {
                            value:  arreglo.prueba[x].id,
                            text: arreglo.prueba[x].descripcion_producto
                        }));

                    }

                });
        });
$("#id_tipo_pago").change(function(){
    var cod1 = document.getElementById("id_tipo_pago").value;

    if(cod1 == 2){
        $("#nro_tarjeta").attr("readonly", false); 
        $("#nro_tarjeta2").attr("readonly", false);
        $("#div_tarjeta").css('display','block');
        $("#div_tarjeta2").css('display','block');
        document.getElementById("div_tarjeta").style.display="block";
        document.getElementById('div_tarjeta2').style.display='block';
        document.getElementById('nro_tarjeta').placeholder='Cuatro Primeros Dígitos';
        document.getElementById('nro_tarjeta2').placeholder='Cuatro Ultimos Dígitos';
        
    } else {
        $("#nro_tarjeta").attr("readonly", true); 
        $("#nro_tarjeta2").attr("readonly", true); 
        $("#div_tarjeta").css('display','none');
        $("#div_tarjeta2").css('display','none');       
        document.getElementById('nro_tarjeta').placeholder='';
        document.getElementById('nro_tarjeta2').placeholder='';
    }
    if(cod1 == 5){
        nro_doc = document.getElementById("nro_documento").value;
        if( nro_doc != ""){
            validarnit();
        }

    }
});
});

</script>
@endsection