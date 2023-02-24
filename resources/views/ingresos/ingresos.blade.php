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
<form method="POST" action="{{ url('ingresos') }}" autocomplete="off" id ="formulario">
@csrf
<div class ="divpagina">
    <div class ="titulobotonagregar ">
        <div><h5 class="titulos"><i class="fas fa-angle-double-right"></i>  Ingresos</h5></div>
            <div class=" form-group col-sm-1 labelliquidaciones">
                <label for="name" class="titulonrofac">{{ __('Fecha:') }}</label>
                <input name="fecha" type="date" class="form-control input_facturacion" value="{{$fecha}}" readonly required>
            </div>
            <div class=" form-group col-sm-1 labelliquidaciones">
                <label for="name" class="titulonrofac">{{ __('Nro:') }}</label>
                <input name="id_ingreso" type="text" class="form-control input_facturacion" value="{{$id_ing->nro_por_sucursal}}" readonly required>
            </div>
    </div>

    <div class="divformulario">
        <div class = "titulo_form_fac">
            <h6>Detalle Productos: </h6>
        </div>
        <div class = "row">
            <div class="form-group col-md-5 ">
                <label for="name">{{ __('Seleccionar Grupo:') }}</label>
                <select name="id_grupo" id="id_grupo"type = "number" class="form-control form-control-sm" required>
                    <option value=""></option>
                    @foreach($grupos as $gr)
                    <option value="{{$gr->id}}">{{$gr->id}} - {{$gr->descripcion_grupo}}  </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-3 ">
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
                        <th class = "tamano_letra_factura" style="width : 60px;">Precio Paquete</th>
                        <th class = "tamano_letra_factura" style="width : 120px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan ="3"></td>
                        <td colspan ="3"> <label for="" class = "input_facturacion1">TOTALBs </label></td>
                        <td class = "padding_tabla"><input type="double" style="width : 100%" class="sinborde alinacionderecha1  input_facturacion1" id ="total_detalle" name="total_detalle" value ="{{old('total_detalle')}}" readonly ></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

     <div class="botones_atras_guardar">
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('IngresosController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
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


function validar() {
    sw = 0;
    subtotal = document.getElementsByName("subtotal[]");
    descripcion = document.getElementsByName("descripcion[]");
   // console.log(descripcion);
   
   
    if(subtotal.length>0){

        for(var j=0; j<subtotal.length;j++){
            cadena =String(subtotal[j].value)
            if( cadena === "NaN.NaN" || cadena === "0.00" || cadena === "0"){
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

        if(id_producto === ""){
        alert("NO SELECCIONO PRODUCTO");
            document.getElementById("id_producto").focus();
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

                var linea2 ='<td style="display:none;"><input type="text"  name="unidad_por_paquete[]" id ="unidad_por_paquete'+cont+'" readonly value="'+arreglo.prueba.unidad_por_paquete+'" ></td>';

                var linea3 = '<td class = "padding_tabla" style="width : 60px;"><center><button type="button" class="borrar btn btn-outline-danger btn-sm texto_tabla" onClick="eliminarfila()" ><i class="fas fa-times"></i></button></center></td>'
                var linea4 ='<td class = "padding_tabla"><input type="double" class ="sinborde alinacionderecha1"  style="width :100%" name="cantidad[]" id ="cantidad'+cont+'" onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" readonly></td>';
                var linea5 ='<td class = "padding_tabla"><input type="text" class="sinborde input_facturacion1" style="width : 100%;" name="descripcion_producto[]" value="'+arreglo.prueba.descripcion_producto+'"  readonly></td>';

                var linea6 = '<td class = "padding_tabla"><input style="width : 100%; "type="double" class = "alinacionderecha1 input_facturacion1 " id = "cantidad_paquete'+cont+'" name="cantidad_paquete[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)" ></td>';
                var linea7 = '<td class = "padding_tabla"><input style="width :100%; "type="double" class = "alinacionderecha1 input_facturacion1 " id = "cantidad_unidad'+cont+'" name="cantidad_unidad[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"></td>';

                var linea8 = '<td class = "padding_tabla"><input style="width :100%; "type="double" class = "alinacionderecha1 input_facturacion1 " id = "precio_unitario'+cont+'" name="precio_unitario[]"  onblur="calcula()" value="0" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"></td>';

                var linea9 = '<td class = "padding_tabla"><input style="width : 100%; type="double" class=" alinacionderecha1  input_facturacion1 sinborde subtotal" id = "subtotal'+cont+'" name="subtotal[]" onblur="calcula(this.form)" value="'+0+'" readonly></td></tr>';

                var linea = linea0+linea1+linea2+linea3+linea4+linea5+linea6+linea7+linea8+linea9;
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

    
    //-------------
    //cantidades--------
    var unidad_por_paquete = document.getElementsByName("unidad_por_paquete[]");
    var cantidad = document.getElementsByName("cantidad[]");
    var cantidad_paquete = document.getElementsByName("cantidad_paquete[]");
    var cantidad_unidad = document.getElementsByName("cantidad_unidad[]");
    //---------------

    var precio_unitario = document.getElementsByName("precio_unitario[]");
    
    var subtotal = document.getElementsByName("subtotal[]");

    var sumtot = 0;
   

    for(var j=0; j<cantidad.length;j++){

        //calculos para cantidad total
        //--------------------------------------------------------------------
        unidad_por_paquete0 =  unidad_por_paquete[j].value.replace(/,/g, '');
        unidad_por_paquete1 = redondea( unidad_por_paquete0);
        unidad_por_paquete3= decimales( unidad_por_paquete1.toFixed(2));
        $('#'+ unidad_por_paquete[j].id).val( unidad_por_paquete3);

        cantidad_paquete0 = cantidad_paquete[j].value.replace(/,/g, '');
        cantidad_paquete1 = redondea(cantidad_paquete0);
        cantidad_paquete3= decimales(cantidad_paquete1.toFixed(2));
        $('#'+cantidad_paquete[j].id).val(cantidad_paquete3);

        cantidad_unidad0 = cantidad_unidad[j].value.replace(/,/g, '');
        cantidad_unidad1 = redondea(cantidad_unidad0);
        cantidad_unidad3= decimales(cantidad_unidad1.toFixed(2));
        $('#'+cantidad_unidad[j].id).val(cantidad_unidad3);

        cantidad_total = (unidad_por_paquete1*cantidad_paquete1)+cantidad_unidad1; //con este dato las operaciones
        cantidad_total1 = redondea(cantidad_total);
        cantidad_total3= decimales(cantidad_total1.toFixed(2));
        $('#'+ cantidad[j].id).val(cantidad_total3);
        //-----------------------------------------------------------------------

        
        //calculando subtotal - descuento
        //----------------------------------------------------------------------
        precio_unitario0 = precio_unitario[j].value.replace(/,/g, '');
        precio_unitario1 = redondea(precio_unitario0);
        precio_unitario3= decimales( precio_unitario1.toFixed(2));
        $('#'+precio_unitario[j].id).val( precio_unitario3);

        auxi_precio_unitario = precio_unitario1/unidad_por_paquete1;

        subtot_linea = (auxi_precio_unitario * cantidad_total); //con este dato se hace los calculos
        subtot_linea1 = redondea(subtot_linea);
        subtot_linea3= decimales(subtot_linea1.toFixed(2));

        $('#'+subtotal[j].id).val( subtot_linea3);

        sumtot = sumtot + subtot_linea1;

    }
    
    $("#total_detalle").val(sumtot);
}

$(document).ready(function() {

    $('#modal_respuesta_servidor1').trigger('click');

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

                        $('#id_cliente1').prop("disabled", true);

                       

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
});

</script>
@endsection