@extends('layouts.index')

@section('content')   
<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> EDITAR PRODUCTO</h3>
    </div>
 
     
<form method="POST" action=" {{ url('/productos/'.$producto->id) }} " autocomplete="off">
@csrf
@method('PATCH')
<div  class="divformulario"> 
    <div  class="row">
        <div class="form-group col-md-3">
            <label for="name">{{ __('Grupo') }}</label>
            <select name="grupo2"  type = "number" class="form-control input_facturacion"  required>
                
                @foreach($grupo2 as $gru)
                    @if($producto->id_grupo == $gru->id)
                        <option value=" {{$gru->id}} " selected>{{$gru->descripcion_grupo}}</option>
                    @else
                        <option value=" {{$gru->id}} " >{{$gru->descripcion_grupo}}</option>
                    @endif
                @endforeach
            </select>
        </div> 
       
        <div class="form-group col-md-4">
            <label for="name">{{ __('Descripcion') }}</label>
            <input name="descripcion" type="text" class="form-control input_facturacion"  maxlength="100" value ="{{$producto->descripcion_producto}}" required>
        </div>
        
        <div class="form-group col-md-2">
            <label for="name">{{ __('Unidad Medida') }}</label>
            <select name="unidad_medida" type = "number" class="form-control input_facturacion" required>
                
                @foreach($unidad_medida as $uni)
                    @if($producto->id_medida == $uni->id)
                        <option value=" {{$uni->id}} " selected>{{$uni->descripcion}}</option>
                    @else
                        <option value=" {{$uni->id}} ">{{$uni->descripcion}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="name">{{ __('Descripcion Impuestos') }}</label>
            <select name="codigo_impuestos"  type = "number" class="form-control input_facturacion" id = "descripcion_impuestos"  required>
                
                @foreach($parametrica_producto as $pro)
                    @if($producto->id_parametrica_producto == $pro->id)
                        <option value=" {{$pro->id}} " selected>{{$pro->descripcion_producto}}</option>
                    @else
                        <option value=" {{$pro->id}} " >{{$pro->descripcion_producto}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="name">{{ __('Grupo ICE y Porcentual:') }}</label>
            <select name="grupo" id="grupo" type = "number" class="form-control input_facturacion" required>   
                @foreach($grupos as $g)
                    @if($producto->id_grupo_porcentual == $g->id)
                        <option value=" {{$g->id}}" selected> {{$g->descripcion}}  </option>
                    @else
                        <option value=" {{$g->id}}"> {{$g->descripcion}}  </option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="name">{{ __('Cantidad en Litros por Unidad:') }}</label>
            <input name="cantidad_litros_x_unidad" type="text" class="form-control input_facturacion"   value= "{{$producto->cantidad_litros_x_unidad}}"  onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
        </div> 

        <div class="form-group col-md-1">
            <label for="name">{{ __('Precio 1:') }}</label>
            <input name="precio1" type="text" class="form-control input_facturacion"  value= "{{$producto->precio1}}" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
        </div>

        <div class="form-group col-md-1">
            <label for="name">{{ __('Precio 2:') }}</label>
            <input name="precio2" type="text" class="form-control input_facturacion"  value= "{{$producto->precio2}}" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
        </div>

        <div class="form-group col-md-1">
            <label for="name">{{ __('Precio 3:') }}</label>
            <input name="precio3" type="text" class="form-control input_facturacion"  value= "{{$producto->precio3}}" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
        </div>

        
        <div class="form-group col-md-2">
            <label for="name">{{ __('Unidad por Paquete:') }}</label>
            <input name="unidad_por_paquete" type="text" class="form-control input_facturacion" value= "{{$producto->unidad_por_paquete}}" onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
        </div>
        
        <div class="form-group col-md-1">
            <label for="password-confirm" >{{ __('Estado') }}</label>
            @if($producto->estado == 1 )
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado" value ="1" checked>  
                    <label class="form-check-label" for="exampleRadios1">Activo</label>
                </div>
            @else    
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado" value="1">
                    <label class="form-check-label" for="exampleRadios2">Activo</label>
                </div>
            @endif    
        </div> 

    </div>
</div>
<div class="botones_atras_guardar">          
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('ProductosController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar Cambios </button>
        </div>
    </div>       
</form>
</div>                              
        
@endsection

@section('js')
<script>



</script>
@endsection 