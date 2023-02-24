@extends('layouts.index')

@section('content')   
<div class ="divpagina">

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> <i class="fas fa-angle-double-right"></i> AGREGAR CLIENTE</h3>
    </div>
    <input type="text" id = "linea" name = "linea" value ="1">
   
<form method="POST" action="{{ url('clientes') }}" autocomplete="off">
@csrf
    <div  class="divformulario"> 
        <div class="row">
               
            <div class="form-group col-md-3">
                <label for="name">{{ __('Descripcion:') }}</label>
                <input name="descripcion" type="text" class="form-control input_facturacion" required>
            </div>

            <div class="form-group col-md-3">
                <label for="name">{{ __('Responsable:') }}</label>
                <input name="responsable" type="text" class="form-control input_facturacion" required>
            </div>
            
            <div class="form-group col-md-3">
                <label for="name">{{ __('Direccion:') }}</label>
                <input name="direccion" type="text" class="form-control input_facturacion" required>
            </div>

            <div class="form-group col-md-3 labelliquidaciones">
                    <label for="name" >Email:</label>
                    <input id="email" id = "email" type="email" class="form-control @error('email') is-invalid @enderror input_facturacion" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>

            <div class="form-group col-md-3">
                   <label for="name">{{ __('Tipo Documento:') }}</label>
                   <select name="id_tipo_documento" id="id_tipo_documento" type = "number" class=" form-control input_facturacion" required>   
                        <option value=""></option>
                        @foreach($tipo_documento as $td)
                            <option value=" {{$td->id}}"> {{$td->descripcion}}  </option>
                        @endforeach
                    </select>
               </div>

               <div class="form-group col-md-2">
                   <label for="name">{{ __('Nro Documento:') }}</label>
                   <input name="nro_documento"  id="nro_documento" type="text" class="form-control input_facturacion" onblur="validarnit()"required>
                   <input type="text" id = "validanit" name = "validanit" value = "0">
               </div> 

               <div class="form-group  col-md-1 labelliquidaciones">
                        <label for="name">&nbsp;</label>
                        <input name="complemento" type="text" class="form-control input_facturacion" id = "complemento"readonly placeholder ="Complemento" value="{{ old('complemento') }}">
                </div>

                <div class="form-group col-md-3">
                    <label for="name">{{ __('Razon Social:') }}</label>
                    <input name="razon_social" type="text" class="form-control input_facturacion" required>
                </div>

               <div class="form-group col-md-2">
                   <label for="name">{{ __('Precio:') }}</label>
                   <select name="id_categoria_precio" id="codigo_impuestos" type = "number" class="form-control input_facturacion" required>                           
                        <option value=" 1"> Precio 1  </option>
                        <option value=" 2"> Precio 2  </option>
                        <option value=" 3"> Precio 3  </option>
                    </select>
                </div>
                <div class="form-group col-md-1">
                <label for="password-confirm" >{{ __('Estado') }}</label>
               
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado" value="1" checked  >  
                    <label class="form-check-label" for="exampleRadios1">Activo</label>
                </div>
           </div>
    </div>
    </div>
     <div class="botones_atras_guardar">          
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('ClientesController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar </button>
        </div>
    </div>
         
</form>
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
        
@endsection
@section('js')
<script>

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

function  si_continua() {
    $("#validanit").val(1);
}
function no_continua(){
    $("#validanit").val(0); 
}
function validarnit(){
   
    linea = document.getElementById("linea").value;
    var cod = document.getElementById("id_tipo_documento").value;
   
    var nro_documento = document.getElementById("nro_documento").value
    $("#res_nit").empty();
    $("#cerrar").empty();
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
                                  
            });
    }
}

</script>
@endsection 