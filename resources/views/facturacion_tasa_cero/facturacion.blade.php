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
<form method="POST" action="{{ url('facturacionT') }}" autocomplete="off" id ="formulario">
@csrf
<div class ="divpagina">
    <div class ="titulobotonagregar">
        <div><h5 class="titulos"><i class="fas fa-angle-double-right"></i>  FACTURACION TASA CERO - EN LINEA</h5></div>
        <div>            
            <input type="text" id = "linea" name = "linea" value ="1">
            <input type="hidden" name = "manual" value = "0">
        </div>
    </div>
   

    <div class = "tamano_letra_fatura">
        <div  class="divformulario">
        <div class = "titulo_form_fac">
            <h6>Datos Cliente:</h6>
        </div>
        <div class ="row ">
            <div class = "datos_fac">
                    
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
                        <label for="name"  class="titulonrofac">{{ __('Tipo de Documento:') }}</label>
                        <select name="id_tipo_documento" id = "id_tipo_documento" type = "number" class="form-control input_facturacion " required>
                            <option value=""></option>
                            @foreach($tipo_doc as $td)
                            <option  value=" {{$td->codigo_clasificador}} " {{ old( "id_tipo_documento") == $td->codigo_clasificador ? 'selected' : '' }}>{{$td->descripcion}}  </option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="form-group col-md-2 labelliquidaciones">
                            <label for="name" class="titulonrofac">Nro Documento: </label>
                            <input name="nro_documento" id="nro_documento" type="text" class="form-control input_facturacion" maxlength="15" onblur="validarnit()" value="{{ old('nro_documento') }}"   required>
                            <input type="text" id = "validanit" name = "validanit" value = "{{ old('validanit')}}">
                    </div>
                    <div class="form-group  col-md-1 labelliquidaciones">
                        <label for="name" class="titulonrofac">&nbsp;</label>
                        <input name="complemento" type="text" class="form-control input_facturacion" id = "complemento"readonly placeholder ="Complemento" value="{{ old('complemento') }}" required>
                    </div>
                    <div class="form-group col-md-4 labelliquidaciones">
                        <label for="name"  class="titulonrofac">{{ __('Tipo de Pago:') }}</label>
                        <select name="id_tipo_pago" id="id_tipo_pago"type = "number" class="form-control input_facturacion" required>
                            @foreach($tipo_pago as $tp)
                            <option value=" {{$tp->codigo_clasificador}} ">{{$tp->descripcion}}  </option>
                            @endforeach
                        </select>
                    </div>
                </div>
               
               
               

                <div class = "cliente_fac_datos">

                    <div class="form-group col-md-7 labelliquidaciones">
                        <label for="name" class="titulonrofac">Razon Social:</label>
                        <input name="razon_social" id = "razon_social" type="text" class="form-control input_facturacion" value="{{ old('razon_social') }}"  required>
                    </div>

                    <div class="form-group col-md-5 labelliquidaciones">
                        <label for="name" class="titulonrofac">Email:</label>
                        <input id="email" id = "email" type="email" class="form-control @error('email') is-invalid @enderror input_facturacion" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    
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
                    <label for="name">{{ __('Seleccionar Producto Impuestos:') }}</label>
                    <select name="id_producto" id="id_producto"type = "number" class="form-control input_facturacion" required>
                        <option value=""></option>
                        @foreach($productos as $p)
                        <option value=" {{$p->id}} ">{{$p->id}} - {{$p->descripcion_producto}}  </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4 ">
                    <label for="name">{{ __('Unidad Medida:') }}</label>
                    <select name="id_unidad_medida" id="id_unidad_medida"type = "number" class="form-control input_facturacion" required>
                        <option value=""></option>
                        @foreach($unidad_medida as $u)
                        <option value=" {{$u->id}} ">{{$u->descripcion}}  </option>
                        @endforeach
                    </select>
                </div>
                <div class = "boton_agregar_producto">
                    <button type="button" onclick="agregar();" class="btn btn-outline-success btn-sm" id = "boton_agregar"> + Agregar Fila</button> 
                </div>
                
            </div>   
                
            <div  class="table-responsive">
                <table id ="detalle" class="table1 table-bordered" >
                    <thead  class="table-success ">
                        <tr>
                            <th class = "tamano_letra_factura" style="width : 70px;">Accion</th>
                            <th class = "tamano_letra_factura" style="display:none;">id</th>
                            <th class = "tamano_letra_factura" style="width : 70px;">Codigo</th>
                            <th class = "tamano_letra_factura" style="width : 90px;">Cantidad</th>
                            <th class = "tamano_letra_factura" style="width : 100px;">Unida Medida</th>
                            <th class = "tamano_letra_factura" style="width : 200px;">Descripcion</th>                        
                            <th class = "tamano_letra_factura" style="width : 130px;">Precio Unitario</th>
                            <th class = "tamano_letra_factura" style="width : 130px;">Subtotal</th>                        
                        </tr>
                    </thead>
                    <tbody>
                        
                    
                      @if(old('cantidad'))
                            @for( $i =0; $i < count(old('cantidad')); $i++) 
                            <tr id ="fila'+cont+'">
                                <td style="display:none;"><input type="text"  name="codigo_pro[]" readonly value="{{ old( 'id_tipo_documento')}}" ></td>
                                <td style="display:none;"><input type="text"  name="codigo_producto[]" readonly value="{{ old( 'id_tipo_documento')}}" ></td>
                                <td class = "padding_tabla" style="width : 60px;"><center><button type="button" class="borrar btn btn-outline-danger btn-sm texto_tabla" onClick="eliminarfila()" ><i class="fas fa-times"></i></button></center></td>
                                <td class = "padding_tabla"><input type="text" class="sinborde" style="width : 35px; " name="codigo[]" value="{{ old( 'id_tipo_documento')}}"  readonly></td>
                                <td class = "padding_tabla"><input type="double" class ="alinacionderecha1"  style="width :100px;" name="cantidad[]" id ="cantidad'+cont+'" onblur="calcula()" value="{{old('cantidad.'.$i)}}" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" ></td>
                                <td style="display:none;" class = "padding_tabla" ><input type="text" style="width :55px;" class=" sinborde alinacionderecha1" id = "codigo_unidadmedida'+cont+'" name="codigo_unidad_medida[]"  value="58" readonly></td>
                                <td class = "padding_tabla" ><input type="text" style="width :100px;" class=" sinborde alinacionderecha1" id = "unidadmedida'+cont+'" name="unidad_medida[]"  value="Unidad (Servicios)" readonly></td>
                                <td class = "padding_tabla"><textarea class = " input_descripcion_fac" rows = "3" cols = "50"  id = "descripcion'+cont+'" name="descripcion[]"  >{{old('descripcion.'.$i)}}</textarea>
                                <td class = "padding_tabla"><input style="width : 130px; "type="double" class = "alinacionderecha1 input_uni_subt_fac preciounitario" id = "precio_unitario'+cont+'" name="precio_uni[]"  onblur="calcula()" value="{{old('precio_uni.'.$i)}}" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"></td>
                                <td class = "padding_tabla"><input style="width : 130px;" type="double" class=" alinacionderecha1  input_uni_subt_fac sinborde subtotal" id = "subtotal'+cont+'" name="subtotal[]" onblur="calcula(this.form)" value="{{old('subtotal.'.$i)}}" readonly></td>
                            </tr>                  
                    
                           @endfor
                          @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan ="5"></td><td>TOTAL</td>
                            
                            <td ><input type="double" class="sinborde alinacionderecha1  input_uni_subt_fac" id ="total_detalle" name="total_detalle" value ="{{old('total_detalle')}}" readonly ></td>
                            
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
      
     <div class="botones_atras_guardar">          
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('FacturaTasaCeroController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
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
    console.log(descripcion);
    tipo_doc = document.getElementById("id_tipo_documento").value;
    nro_documento = document.getElementById("nro_documento").value;
    complemento = document.getElementById("complemento").value;
    razon_social = document.getElementById("razon_social").value;
    id_tipo_pago = document.getElementById("id_tipo_pago").value;
   
    email = document.getElementById("email").value;
    console.log(email+"  email")

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
        console.log("");
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
            console.log(cadena1+ "textarea");
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
   
    var producto = document.getElementById("id_producto");
    id_producto = producto.options[producto.selectedIndex].value;
    texto_producto = producto.options[producto.selectedIndex].text;

    var unidad_medida = document.getElementById("id_unidad_medida");
    id_unidad_medida = unidad_medida.options[unidad_medida.selectedIndex].value;
    texto_unidad_medida = unidad_medida.options[unidad_medida.selectedIndex].text;

    $('#id_unidad_medida').prop('disabled', 'disabled');
    $('#id_producto').prop('disabled', 'disabled');

    var linea0 = '<tr id ="fila'+cont+'"><td style="display:none;"><input type="text"  name="codigo_pro[]" readonly value="'+id_producto+'" ></td>';
    var linea001 = '<td style="display:none;"><input type="text"  name="codigo_producto[]" readonly value="'+id_producto+'" ></td>';
    var linea01 ='<td class = "padding_tabla" style="width : 60px;"><center><button type="button" class="borrar btn btn-outline-danger btn-sm texto_tabla" onClick="eliminarfila()" ><i class="fas fa-times"></i></button></center></td>'
    var linea02 = '<td class = "padding_tabla"><input type="text" class="sinborde" style="width : 35px; " name="codigo[]" value="'+id_producto+'"  readonly></td>'; 
    var linea2 = '<td class = "padding_tabla"><input type="double" class ="alinacionderecha1"  style="width :100px;" name="cantidad[]" id ="cantidad'+cont+'" onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" ></td>';
    var linea41 = '<td style="display:none;" class = "padding_tabla" ><input type="text" style="width :55px;" class=" sinborde alinacionderecha1" id = "codigo_unidadmedida'+cont+'" name="codigo_unidad_medida[]"  value="'+id_unidad_medida+' " readonly></td>';
    var linea4 = '<td class = "padding_tabla" ><input type="text" style="width :100px;" class=" sinborde alinacionderecha1" id = "unidadmedida'+cont+'" name="unidad_medida[]"  value="'+texto_unidad_medida+'" readonly></td>';
    var linea3 = '<td class = "padding_tabla"><textarea class = " input_descripcion_fac" rows = "3" cols = "50"  id = "descripcion'+cont+'" name="descripcion[]"  value=""></textarea>';    
    var linea42 = '<td class = "padding_tabla"><input style="width : 130px; "type="double" class = "alinacionderecha1 input_uni_subt_fac preciounitario" id = "precio_unitario'+cont+'" name="precio_uni[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"></td>';
    var linea6 = '<td class = "padding_tabla"><input style="width : 130px;type="double" class=" alinacionderecha1  input_uni_subt_fac sinborde subtotal" id = "subtotal'+cont+'" name="subtotal[]" onblur="calcula(this.form)" value="'+0+'" readonly></td></tr>';
    var linea = linea0+linea001+linea01+linea02+linea2+linea41+linea4+linea3+linea42+linea6;
    $('#detalle > tbody:first').append(linea); 
    
    cont++; 
         
       
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
function calcula(){
    var cantidad = document.getElementsByName("cantidad[]");
    var preciounitario = document.getElementsByName("precio_uni[]");
    var subtotal_a = document.getElementsByName("subtotal[]");
    var sumtot = 0;
    var subtot = 0;

    for(var j=0; j<cantidad.length;j++){
        cantidad0 = cantidad[j].value.replace(/,/g, '');
        cantidad1 = redondea(cantidad0);
        cantidad3= decimales(cantidad1.toFixed(2));
        $('#'+cantidad[j].id).val(cantidad3);

        preciounitario0 = preciounitario[j].value.replace(/,/g, '');
        preciounitario1 = redondea(preciounitario0);
        preciounitario3= decimales( preciounitario1.toFixed(2)); 
        $('#'+preciounitario[j].id).val( preciounitario3);

        subtot = cantidad1 * preciounitario1;
        
        subtot2 = redondea(subtot);
        subtotal= decimales(subtot2.toFixed(2)); 
        $('#'+subtotal_a[j].id).val(subtotal);

    
        sumtot =  sumtot+parseFloat(subtot2);
        sumtot1 = decimales(sumtot.toFixed(2)); 
       
    }
    $("#total_detalle").val(sumtot1);
    $("#subtotal_fob").val(sumtot1);
    $("#subtotal_moneda").val(sumtot1);
    $("#total_general_moneda").val(sumtot1);
    
}

function validarnit(){
    linea = document.getElementById("linea").value;
   
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
    if(cod == 5 && linea == 1){ 

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

                if(arreglo.conexion == 1 && linea == 1){
                    if(arreglo.prueba == 994){
                    $("#res_nit").append('NIT INEXISTENTE, Desea Continuar');
                    $("#cerrar").append("No");
                    $('#boton_si').show();
                   
                    }else{
                        $("#res_nit").append('NIT CORRECTO');
                        $('#boton_si').hide();
                        $("#cerrar").append("Cerrar");   
                    }
                    $('#modal_respuesta_servidor').trigger('click');
                }
                if(arreglo.razon_social != null)
                    {
                        console.log("entro al if de razon social");
                        $("#razon_social").val(arreglo.razon_social.razon_social);
                        $("#email").val(arreglo.razon_social.email);
                    }                       
            });
    }
    else{
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
}


$(document).ready(function() {

    $('#modal_respuesta_servidor1').trigger('click');


    var x = localStorage.getItem("btn_linea");
    $("#linea").val(x);
$("#id_tipo_documento").change(function(){
    var cod = document.getElementById("id_tipo_documento").value;
    console.log(cod + "codigo del select");
    if(cod == 1){
        $("#complemento").attr("readonly", false); 
        console.log("entro al if");
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


});

</script>
@endsection 