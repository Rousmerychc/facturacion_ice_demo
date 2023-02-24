@extends('layouts.index')

@section('content')   
<div class ="divpagina">

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> <i class="fas fa-angle-double-right"></i> AGREGAR PRODUCTO</h3>
    </div>
   
<form method="POST" action="{{ url('productos') }}" autocomplete="off">
@csrf
    <div  class="divformulario"> 
        <div class="row">
            <div class="form-group col-md-3">
                   <label for="name">{{ __('Grupo:') }}</label>
                   <select name="grupo2" id="grupo" type = "number" class="form-control input_facturacion" required>   
                        @foreach($grupo2 as $g)
                        <option value=" {{$g->id}}"> {{$g->descripcion_grupo}}  </option>
                        @endforeach
                    </select>
               </div>

               <div class="form-group col-md-4">
                   <label for="name">{{ __('Descripcion:') }}</label>
                   <input name="descripcion" type="text" class="form-control input_facturacion" required>
               </div>

               <div class="form-group col-md-2">
                   <label for="name">{{ __('Unidad Medida:') }}</label>
                   <select name="unidad_medida" id="unidad_medida" type = "number" class=" form-control input_facturacion" required>   
                        @foreach($unidad_medida as $um)
                        <option value=" {{$um->id}}"> {{$um->descripcion}}  </option>
                        @endforeach
                    </select>
               </div>

               <div class="form-group col-md-3">
                   <label for="name">{{ __('Descripcion Impuestos:') }}</label>
                   <select name="codigo_impuestos" id="codigo_impuestos" type = "number" class="form-control input_facturacion" required>   
                        @foreach($parametrica_producto as $pp)
                        <option value=" {{$pp->id}}"> {{$pp->descripcion_producto}}  </option>
                        @endforeach
                    </select>
                </div>

               <div class="form-group col-md-3">
                   <label for="name">{{ __('Grupo ICE y Porcentual:') }}</label>
                   <select name="grupo" id="grupo" type = "number" class="form-control input_facturacion" required>   
                        @foreach($grupos as $g)
                        <option value=" {{$g->id}}"> {{$g->descripcion}}  </option>
                        @endforeach
                    </select>
               </div>

               <div class="form-group col-md-3">
                   <label for="name">{{ __('Cantidad en Litros por Unidad:') }}</label>
                   <input name="cantidad_litros_x_unidad" type="text" class="form-control input_facturacion"  onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
               </div> 

               <div class="form-group col-md-1">
                   <label for="name">{{ __('Precio 1:') }}</label>
                   <input name="precio1" type="text" class="form-control input_facturacion"  onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
               </div>
               <div class="form-group col-md-1">
                   <label for="name">{{ __('Precio 2:') }}</label>
                   <input name="precio2" type="text" class="form-control input_facturacion"  onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
               </div>
               <div class="form-group col-md-1">
                   <label for="name">{{ __('Precio 3:') }}</label>
                   <input name="precio3" type="text" class="form-control input_facturacion"  onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
               </div>
               <div class="form-group col-md-2">
                   <label for="name">{{ __('Unidad por Paquete:') }}</label>
                   <input name="unidad_por_paquete" type="text" class="form-control input_facturacion"  onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
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
            <a class="btn btn-outline-danger" href="{{  action('ProductosController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar </button>
        </div>
    </div>
         
</form>
</div>                              
        
@endsection
@section('js')
<script>



</script>
@endsection 